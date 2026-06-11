# Tests de sérialisation dans Pinoox

[← Retour à l'index](../README.md)

Pour les API et Resources, inspectez la sortie JSON avec `assertJsonPath()` et `json()` sur `TestResponse`. Pour les modèles Eloquent, assertez `toArray()` / `toJson()` dans `inApp()`.

---

## API — structure de l'enveloppe

Les réponses API Pinoox incluent généralement les champs `success` et `data` :

```php
// apps/com_my_shop/tests/Feature/ProductSerializationTest.php

it('serializes product in api envelope', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'Mouse',
            'price' => 250000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products/1');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'Mouse')
        ->assertJsonPath('data.price', 250000);
});
```

---

## Lire une partie du JSON

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — réponse de ressource créée

```php
it('returns created resource', function () {
    $response = appPostJson(appPackage(), '/api/v1/products', [
        'title' => 'Keyboard',
        'price' => 1800000,
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.title', 'Keyboard')
        ->and($response->json('data.id'))->toBeInt();
});
```

---

## Unit — toArray du modèle

```php
// apps/com_my_shop/tests/Unit/ProductArrayTest.php

it('hides internal fields in array', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::make([
            'title' => 'Test',
            'internal_note' => 'confidential',
        ]);

        $array = $product->toArray();

        expect($array)->toHaveKey('title')
            ->and($array)->not->toHaveKey('internal_note');
    });
});
```

Définissez les champs `$hidden` et les casts sur le modèle — configurez là, assertez dans les tests.

---

## Unit — chaîne JSON

```php
it('encodes to valid json', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::make(['title' => 'A']);

        $json = $product->toJson();
        $decoded = json_decode($json, true);

        expect($decoded['title'])->toBe('A');
    });
});
```

---

## Exécuter les tests

```bash
php pinoox test com_my_shop -f Serialization
```

---

## Documentation associée

- [Tests HTTP](./http-tests.md)
- [Réponses](../basic/responses.md)
- [Eloquent — sérialisation](../eloquent-orm/serialization.md)
- [Ressources API](../eloquent-orm/api-resources.md)
- [Mutators / casts](../eloquent-orm/mutators-casts.md)

---

[← Retour à l'index](../README.md)

# Serialisierungstests in Pinoox

[← Zurück zum Index](../README.md)

Für APIs und Ressourcen JSON-Ausgabe mit `assertJsonPath()` und `json()` auf `TestResponse` prüfen. Für Eloquent-Models `toArray()` / `toJson()` innerhalb von `inApp()` assertieren.

---

## API — Envelope-Struktur

Pinoox-API-Responses enthalten typischerweise die Felder `success` und `data`:

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

## Teil des JSON lesen

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — Response der erstellten Ressource

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

## Unit — Model toArray

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

`$hidden`-Felder und Casts am Model definieren — dort konfigurieren, in Tests prüfen.

---

## Unit — JSON-String

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

## Tests ausführen

```bash
php pinoox test com_my_shop -f Serialization
```

---

## Verwandte Dokumentation

- [HTTP-Tests](./http-tests.md)
- [Responses](../basic/responses.md)
- [Eloquent — Serialisierung](../eloquent-orm/serialization.md)
- [API-Ressourcen](../eloquent-orm/api-resources.md)
- [Mutatoren / Casts](../eloquent-orm/mutators-casts.md)

---

[← Zurück zum Index](../README.md)

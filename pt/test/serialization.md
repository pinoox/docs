# Testes de serialização no Pinoox

[← Voltar ao índice](../README.md)

Para APIs e Resources, inspecione a saída JSON com `assertJsonPath()` e `json()` em `TestResponse`. Para models Eloquent, asserte `toArray()` / `toJson()` dentro de `inApp()`.

---

## API — estrutura do envelope

Respostas de API do Pinoox costumam incluir campos `success` e `data`:

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

## Ler parte do JSON

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — resposta de recurso criado

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

## Unit — model toArray

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

Defina campos `$hidden` e casts no model — configure lá, asserte nos testes.

---

## Unit — string JSON

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

## Executar testes

```bash
php pinoox test com_my_shop -f Serialization
```

---

## Documentação relacionada

- [Testes HTTP](./http-tests.md)
- [Respostas](../basic/responses.md)
- [Eloquent — serialização](../eloquent-orm/serialization.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Mutators / casts](../eloquent-orm/mutators-casts.md)

---

[← Voltar ao índice](../README.md)

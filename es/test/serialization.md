# Tests de serialización en Pinoox

[← Volver al índice](../README.md)

Para APIs y Resources, inspecciona la salida JSON con `assertJsonPath()` y `json()` en `TestResponse`. Para modelos Eloquent, comprueba `toArray()` / `toJson()` dentro de `inApp()`.

---

## API — estructura del sobre

Las respuestas API de Pinoox suelen incluir campos `success` y `data`:

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

## Leer parte del JSON

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — respuesta de recurso creado

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

## Unit — toArray del modelo

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

Define campos `$hidden` y casts en el modelo — configura ahí, comprueba en tests.

---

## Unit — cadena JSON

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

## Ejecutar tests

```bash
php pinoox test com_my_shop -f Serialization
```

---

## Documentación relacionada

- [Tests HTTP](./http-tests.md)
- [Responses](../basic/responses.md)
- [Eloquent — serialización](../eloquent-orm/serialization.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Mutators / casts](../eloquent-orm/mutators-casts.md)

---

[← Volver al índice](../README.md)

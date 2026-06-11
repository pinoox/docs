# Tests de base de datos en Pinoox

[← Volver al índice](../README.md)

Para probar modelos, migraciones y endpoints que dependen de DB, ejecuta código dentro de `inApp()` y usa la base de datos de test (`mode=test`). Ejecuta migraciones antes de los tests para que el esquema esté listo.

---

## Requisitos previos

1. En `.env.testing` (o `.env`), configura una base de datos de test separada de producción.
2. Ejecuta migraciones de la app una vez en la configuración de test.

```php
// apps/com_my_shop/tests/Pest.php

beforeEach(function () {
    appPackage('com_my_shop');
});

beforeAll(function () {
    $root = dirname(__DIR__, 4);
    $process = new Symfony\Component\Process\Process(
        ['php', 'pinoox', 'migrate', 'com_my_shop', '--force'],
        $root
    );
    $process->run();
});
```

---

## Unit — crear y leer un modelo

```php
// apps/com_my_shop/tests/Unit/ProductModelTest.php

it('creates a product', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::create([
            'title' => 'Test product',
            'price' => 99000,
        ]);

        expect($product->id)->not->toBeNull()
            ->and($product->title)->toBe('Test product');
    });
});
```

---

## Feature — API con base de datos

```php
it('lists products from database', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'Book',
            'price' => 50000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Book');
});
```

---

## Limpieza entre tests

Para evitar colisiones de datos, trunca tablas relacionadas tras cada test:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Probar migraciones

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Los nombres de tabla usan el prefijo del paquete (`{package}_`) según la convención de la app.

---

## Ejecutar tests

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Consejos

1. Nunca ejecutes tests contra una base de datos de producción — usa `APP_ENV=test` y una DB separada.
2. Llama a seeders en `beforeEach` solo cuando haga falta; prefiere crear datos mínimos dentro de cada test.
3. Prueba consultas y relaciones en **Unit**; prueba endpoints completos en **Feature**.

---

## Documentación relacionada

- [Primeros pasos con testing](./getting-started.md)
- [Tests HTTP](./http-tests.md)
- [Primeros pasos con base de datos](../database/getting-started.md)
- [Migraciones](../database/migrations.md)
- [Eloquent — primeros pasos](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← Volver al índice](../README.md)

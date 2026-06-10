# Database Testing in Pinoox

To test models, migrations, and DB-dependent endpoints, run code inside `inApp()` and use the test database (`mode=test`). Run migrations before tests so the schema is ready.

---

## Prerequisites

1. In `.env.testing` (or `.env`), configure a test database separate from production.
2. Run app migrations once in test setup.

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

## Unit — create and read a model

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

## Feature — API with database

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

## Cleanup between tests

To avoid data collisions, truncate related tables after each test:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Testing migrations

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Table names use the package prefix (`{package}_`) per app convention.

---

## Running tests

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Tips

1. Never run tests against a production database — use `APP_ENV=test` and a separate DB.
2. Call seeders in `beforeEach` only when needed; prefer creating minimal data inside each test.
3. Test queries and relations in **Unit**; test full endpoints in **Feature**.

---

## Related docs

- [Getting started with testing](./getting-started.md)
- [HTTP tests](./http-tests.md)
- [Database getting started](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent — getting started](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

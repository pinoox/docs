# Database Testing in Pinoox

[← इंडेक्स पर वापस जाएँ](../README.md)

Models, migrations, और DB-dependent endpoints test करने के लिए `inApp()` के अंदर code चलाएँ और test database (`mode=test`) उपयोग करें। Schema ready हो tests से पहले migrations चलाएँ।

---

## Prerequisites

1. `.env.testing` (या `.env`) में production से अलग test database configure करें।
2. Test setup में app migrations एक बार चलाएँ।

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

## Tests के बीच cleanup

Data collisions avoid करने के लिए related tables truncate करें:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Migrations test

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Table names app convention के अनुसार package prefix (`{package}_`) उपयोग करते हैं।

---

## Running tests

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Tips

1. Production database पर tests कभी न चलाएँ — `APP_ENV=test` और separate DB उपयोग करें।
2. Seeders `beforeEach` में केवल ज़रूरत हो तो call करें; हर test में minimal data create prefer करें।
3. Queries और relations **Unit** में test करें; full endpoints **Feature** में।

---

## संबंधित docs

- [Getting started with testing](./getting-started.md)
- [HTTP tests](./http-tests.md)
- [Database getting started](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent — getting started](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

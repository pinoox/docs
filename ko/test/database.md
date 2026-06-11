# Pinoox Database 테스트

[← 색인으로 돌아가기](../README.md)

Model, migration, DB 의존 endpoint 테스트는 `inApp()` 내부에서 code 실행, test database(`mode=test`) 사용. schema 준비를 위해 test 전 migration 실행.

---

## Prerequisites

1. `.env.testing`(또는 `.env`)에 production과 별도 test database 설정.
2. test setup에서 앱 migration 한 번 실행.

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

## Unit — model create와 read

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

## Feature — database가 있는 API

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

## Test 간 cleanup

data collision 방지를 위해 각 test 후 관련 table truncate:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Migration 테스트

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Table name은 앱 convention에 따라 package prefix(`{package}_`) 사용.

---

## Test 실행

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Tips

1. production database에 test 실행 금지 — `APP_ENV=test`와 별도 DB 사용.
2. 필요할 때만 `beforeEach`에서 seeder 호출; test 내부 minimal data 생성 선호.
3. query와 relation은 **Unit**; full endpoint는 **Feature**.

---

## 관련 문서

- [테스트 시작하기](./getting-started.md)
- [HTTP tests](./http-tests.md)
- [Database 시작하기](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent — 시작하기](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← 색인으로 돌아가기](../README.md)

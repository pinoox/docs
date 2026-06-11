# Pinoox 中的数据库测试

[← 返回索引](../README.md)

要测试模型、迁移和依赖数据库的端点，在 `inApp()` 内运行代码并使用测试数据库（`mode=test`）。测试前运行迁移以确保结构就绪。

---

## 前置条件

1. 在 `.env.testing`（或 `.env`）中配置与生产分离的测试数据库。
2. 在测试设置中至少运行一次应用迁移。

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

## Unit — 创建并读取模型

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

## Feature — 带数据库的 API

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

## 测试间清理

为避免数据冲突，每次测试后清空相关表：

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## 测试迁移

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

表名按应用约定使用包前缀（`{package}_`）。

---

## 运行测试

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## 提示

1. 切勿对生产数据库运行测试 — 使用 `APP_ENV=test` 和独立数据库。
2. 仅在需要时在 `beforeEach` 中调用填充器；优先在每个测试内创建最少数据。
3. 在 **Unit** 中测试查询与关联；在 **Feature** 中测试完整端点。

---

## 相关文档

- [测试入门](./getting-started.md)
- [HTTP 测试](./http-tests.md)
- [数据库入门](../database/getting-started.md)
- [迁移](../database/migrations.md)
- [Eloquent — 入门](../eloquent-orm/getting-started.md)
- [填充器](../eloquent-orm/factories.md)

---

[← 返回索引](../README.md)

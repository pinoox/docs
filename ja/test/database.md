# Pinoox での Database テスト

[← 索引に戻る](../README.md)

Model、Migration、DB 依存エンドポイントをテストするには `inApp()` 内でコードを実行し、テスト Database（`mode=test`）を使用します。スキーマ準備のためテスト前に Migration を実行してください。

---

## 前提条件

1. `.env.testing`（または `.env`）で本番と別のテスト Database を設定。
2. テストセットアップで一度アプリ Migration を実行。

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

## Unit — Model の作成と読み取り

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

## Feature — Database 付き API

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

## テスト間のクリーンアップ

データ衝突を避けるため、各テスト後に関連テーブルを truncate:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Migration のテスト

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

テーブル名はアプリ規約に従いパッケージプレフィックス（`{package}_`）を使用します。

---

## テストの実行

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## ヒント

1. 本番 Database に対してテストを実行しない — `APP_ENV=test` と別 DB を使用。
2. 必要な場合のみ `beforeEach` で Seeder を呼ぶ。各テスト内で最小データを作成することを優先。
3. クエリとリレーションは **Unit** でテスト。完全エンドポイントは **Feature** でテスト。

---

## 関連ドキュメント

- [テストはじめに](./getting-started.md)
- [HTTP テスト](./http-tests.md)
- [Database はじめに](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent はじめに](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← 索引に戻る](../README.md)

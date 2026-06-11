# Pinoox でのテストはじめに

[← 索引に戻る](../README.md)

Pinoox は **フレームワークコア**（`tests/`）と **各アプリ**（`apps/{package}/tests/`）で同じアプローチを使用: [Pest](https://pestphp.com/)、共有 bootstrap、`AppTestKit`。このガイドではその標準ワークフローを実践例とともに説明します。

---

## テストスタック

| ツール | 役割 |
|------|------|
| Pest | PHP テストの実行 |
| `Pinoox\Component\Test\AppTestKit` | 環境 boot、一時アプリ、HTTP リクエスト |
| `tests/bootstrap.php` | コアとアプリテストの共有エントリーポイント |

---

## テストの実行

```bash
# すべてのコアテスト
vendor/bin/pest

# CLI から（対話式パッケージ選択）
php pinoox test

# 特定のアプリ
php pinoox test com_my_shop

# テスト名でフィルタ
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Feature または Unit のみ
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

CI では `composer.json` のスクリプトも使用できます:

```bash
composer test          # プラットフォームテスト
composer test:apps     # すべてのアプリテスト
```

---

## アプリテストフォルダ構造

`php pinoox app:create` 実行時に `tests/` フォルダが自動作成されます:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← スモークテスト
    └── Unit/
```

新しいテストを作成:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## `tests/Pest.php` ファイル

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

`appPackage()` ヘルパーはヘルパーと自動検出用にアクティブアプリを設定します。

---

## グローバルヘルパー

| ヘルパー | 目的 |
|--------|---------|
| `appPackage($package?)` | アクティブパッケージの設定 / 読み取り |
| `inApp($package, fn)` | `App::meeting()` 内でコード実行 |
| `appPath($package, $sub = '')` | アプリフォルダへのパス |
| `fakeApp($package, $files)` | カスタムファイル付き一時アプリ作成 |
| `deleteFakeApp($package)` | 一時アプリ削除 |
| `appGet($package, $uri, ...)` | GET リクエスト → `TestResponse` |
| `appPost($package, $uri, $data)` | POST リクエスト |
| `appPostJson($package, $uri, $json)` | JSON POST リクエスト |
| `pinooxBoot()` | テスト環境を boot |

---

## Unit — Component クラスのテスト

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — アプリ boot スモークテスト

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## コア vs アプリ

| 場所 | 目的 | 基底ケース |
|----------|---------|-----------|
| `tests/Feature/` | フレームワーク、Portal、Router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP、Flow、統合 | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component、純粋ロジック | `Tests\AppTestCase` |

---

## テストモード

テスト環境では `mode` が自動的に `test` に設定されます:

```php
config('~pinoox')->get('mode'); // 'test'
```

CI では必要に応じて `.env.testing` または `APP_ENV=test` を設定してください。

---

## ヒント

1. `fakeApp()` 後は `afterEach` で必ず `deleteFakeApp()` を呼ぶ。
2. アプリ内の config、Portal、Model には `inApp()` を使用。
3. ルートと API には `appGet` / `appPostJson` を使用。
4. ルート → **Feature**。`Component/` クラス → **Unit**。
5. 手動コピーより `php pinoox test:create` を使用。

---

## 関連ドキュメント

- [HTTP テスト](./http-tests.md)
- [Console テスト](./console-tests.md)
- [Browser（HTML）テスト](./browser-tests.md)
- [Database テスト](./database.md)
- [Mocking](./mocking.md)
- [最初のアプリ](../start/your-first-app.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)

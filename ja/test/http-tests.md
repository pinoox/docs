# Pinoox での HTTP テスト

[← 索引に戻る](../README.md)

Controller、API、Flow をテストするには Pinoox HTTP ヘルパー `appGet()`、`appPost()`、`appPostJson()` を使用します。いずれも組み込みアサーション付き `TestResponse` を返します。

---

## 前提条件

`apps/{package}/tests/Pest.php` で `beforeEach` にアプリパッケージを設定:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — 商品リスト

```php
// apps/com_my_shop/tests/Feature/ProductApiTest.php

it('returns product list', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $response
        ->assertOk()
        ->assertJsonPath('success', true);
});
```

---

## フォーム POST — データ送信

```php
it('submits contact form', function () {
    $response = appPost(appPackage(), '/contact', [
        'name' => 'Ali',
        'email' => 'ali@example.com',
        'message' => 'Hello',
    ]);

    $response->assertOk();
});
```

---

## JSON POST — API

```php
it('creates an order', function () {
    $response = appPostJson(appPackage(), '/api/v1/orders', [
        'product_id' => 1,
        'qty' => 2,
    ]);

    $response->assertStatus(201);
});
```

---

## カスタムリクエスト

ヘッダー、Cookie、または一般的でない HTTP メソッドには `appCall()` を使用:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## TestResponse アサーション

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // HTML テキスト
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // JSON 配列
$response->status();
$response->content();
```

---

## Flow のテスト（例: 認証）

Flow 保護ルートは他のエンドポイントと同様にテスト:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // inApp 内でユーザーをログインし、次に:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## テストの実行

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## 関連ドキュメント

- [テストはじめに](./getting-started.md)
- [JSON Serialization テスト](./serialization.md)
- [Database テスト](./database.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Response](../basic/responses.md)
- [Request](../basic/requests.md)

---

[← 索引に戻る](../README.md)

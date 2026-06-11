# Pinoox での Browser（HTML）テスト

[← 索引に戻る](../README.md)

Twig と HTML ページには **`appGet()` と `assertSee()` 付き Feature テスト** を使用 — 実ブラウザや Dusk は不要。HTTP はシミュレートされ、HTML コンテンツをアサートします。

---

## 前提条件

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## ホームページ — タイトルとテキスト

```php
// apps/com_my_shop/tests/Feature/HomePageTest.php

it('shows welcome message on home page', function () {
    $response = appGet(appPackage(), '/');

    $response
        ->assertOk()
        ->assertSee('My Shop');
});
```

---

## フォーム — フィールドの存在

```php
it('renders login form', function () {
    $response = appGet(appPackage(), '/login');

    $response
        ->assertOk()
        ->assertSee('name="email"')
        ->assertSee('name="password"');
});
```

---

## POST 後のリダイレクト

```php
it('redirects after successful login', function () {
    $response = appPost(appPackage(), '/login', [
        'email' => 'user@example.com',
        'password' => 'secret',
    ]);

    $response->assertStatus(302);
});
```

---

## 404 ページ

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Database との組み合わせ

ページが DB データに依存する場合、先にレコードを作成（`inApp` 内）してからページを開く:

```php
it('shows product name on detail page', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'PHP Book',
            'slug' => 'php-book',
        ]);
    });

    $response = appGet(appPackage(), '/products/php-book');

    $response->assertSee('PHP Book');
});
```

---

## テストの実行

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## 制限

このアプローチはクライアントサイド JavaScript（Vue/Vite SPA）を実行しません。SPA には API テスト（`appPostJson`）を使用し、必要に応じてフロントエンドレイヤーで別途 E2E テストを行ってください。

---

## 関連ドキュメント

- [HTTP テスト](./http-tests.md)
- [Database テスト](./database.md)
- [View](../basic/views.md)
- [Templates](../basic/templates.md)
- [Serialization テスト](./serialization.md)

---

[← 索引に戻る](../README.md)

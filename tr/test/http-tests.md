# Pinoox'ta HTTP testleri

[← Dizine dön](../README.md)

Controller, API ve Flow'ları test etmek için Pinoox HTTP helper'larını kullanın: `appGet()`, `appPost()` ve `appPostJson()`. Her biri yerleşik assertion'larla bir `TestResponse` döndürür.

---

## Ön koşullar

`apps/{package}/tests/Pest.php` içinde `beforeEach`'te uygulama paketini ayarlayın:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — ürün listesi

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

## Form POST — veri gönderme

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

## Özel istek

Başlıklar, cookie'ler veya yaygın olmayan HTTP metotları için `appCall()` kullanın:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## TestResponse assertion'ları

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // HTML text
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // JSON array
$response->status();
$response->content();
```

---

## Flow testi (ör. auth)

Flow korumalı route'ları diğer endpoint'ler gibi test edin:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Log in the user inside inApp, then:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## Testleri çalıştırma

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## İlgili dokümantasyon

- [Teste başlarken](./getting-started.md)
- [JSON serileştirme testleri](./serialization.md)
- [Veritabanı testleri](./database.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Response](../basic/responses.md)
- [Request](../basic/requests.md)

---

[← Dizine dön](../README.md)

# Pinoox HTTP 테스트

[← 색인으로 돌아가기](../README.md)

Controller, API, Flow 테스트에는 Pinoox HTTP helper `appGet()`, `appPost()`, `appPostJson()` 사용. 각각 built-in assertion이 있는 `TestResponse` 반환.

---

## Prerequisites

`apps/{package}/tests/Pest.php`에서 `beforeEach`에 앱 package 설정:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — product list

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

## Form POST — data 제출

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

## Custom request

header, cookie, uncommon HTTP method는 `appCall()`:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## TestResponse assertion

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

## Flow 테스트 (예: auth)

Flow 보호 route는 다른 endpoint처럼 테스트:

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

## Test 실행

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## 관련 문서

- [테스트 시작하기](./getting-started.md)
- [JSON serialization tests](./serialization.md)
- [Database tests](./database.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← 색인으로 돌아가기](../README.md)

# HTTP Testing in Pinoox

[← इंडेक्स पर वापस जाएँ](../README.md)

Controllers, APIs, और Flows test करने के लिए Pinoox HTTP helpers उपयोग करें: `appGet()`, `appPost()`, और `appPostJson()`। हर एक built-in assertions के साथ `TestResponse` return करता है।

---

## Prerequisites

`apps/{package}/tests/Pest.php` में `beforeEach` में app package set करें:

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

## Form POST — submit data

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

Headers, cookies, या uncommon HTTP methods के लिए `appCall()` उपयोग करें:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## TestResponse assertions

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

## Flow test (e.g. auth)

Flow-protected routes किसी भी endpoint की तरह test करें:

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

## Running tests

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## संबंधित docs

- [Getting started with testing](./getting-started.md)
- [JSON serialization tests](./serialization.md)
- [Database tests](./database.md)
- [Routers](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

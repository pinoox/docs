# HTTP Testing in Pinoox

[← Back to index](../../README.md)

To test controllers, APIs, and Flows, use Pinoox HTTP helpers: `appGet()`, `appPost()`, and `appPostJson()`. Each returns a `TestResponse` with built-in assertions.

---

## Prerequisites

In `apps/{package}/tests/Pest.php`, set the app package in `beforeEach`:

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

For headers, cookies, or uncommon HTTP methods, use `appCall()`:

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

## Testing Flow (e.g. auth)

Test Flow-protected routes like any other endpoint:

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

## Related docs

- [Getting started with testing](./getting-started.md)
- [JSON serialization tests](./serialization.md)
- [Database tests](./database.md)
- [Routers](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← Back to index](../../README.md)

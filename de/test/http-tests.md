# HTTP-Tests in Pinoox

[← Zurück zum Index](../README.md)

Zum Testen von Controllern, APIs und Flows die Pinoox-HTTP-Helper verwenden: `appGet()`, `appPost()` und `appPostJson()`. Jeder gibt eine `TestResponse` mit eingebauten Assertions zurück.

---

## Voraussetzungen

In `apps/{package}/tests/Pest.php` das App-Package in `beforeEach` setzen:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — Produktliste

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

## Form-POST — Daten senden

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

## JSON-POST — API

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

## Benutzerdefinierte Anfrage

Für Header, Cookies oder unübliche HTTP-Methoden `appCall()` verwenden:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## TestResponse-Assertions

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // HTML-Text
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // JSON-Array
$response->status();
$response->content();
```

---

## Flow testen (z. B. Auth)

Flow-geschützte Routen wie jeden anderen Endpunkt testen:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Benutzer innerhalb von inApp einloggen, dann:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## Tests ausführen

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## Verwandte Dokumentation

- [Erste Schritte beim Testen](./getting-started.md)
- [JSON-Serialisierungstests](./serialization.md)
- [Datenbank-Tests](./database.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← Zurück zum Index](../README.md)

# Tests HTTP en Pinoox

[← Volver al índice](../README.md)

Para probar controllers, APIs y Flows, usa los helpers HTTP de Pinoox: `appGet()`, `appPost()` y `appPostJson()`. Cada uno devuelve un `TestResponse` con aserciones integradas.

---

## Requisitos previos

En `apps/{package}/tests/Pest.php`, establece el paquete de la app en `beforeEach`:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — lista de productos

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

## POST de formulario — enviar datos

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

## POST JSON — API

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

## Petición personalizada

Para cabeceras, cookies o métodos HTTP poco comunes, usa `appCall()`:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## Aserciones TestResponse

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // texto HTML
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // array JSON
$response->status();
$response->content();
```

---

## Probar Flow (p. ej. auth)

Prueba rutas protegidas por Flow como cualquier otro endpoint:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Inicia sesión del usuario dentro de inApp, luego:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## Ejecutar tests

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## Documentación relacionada

- [Primeros pasos con testing](./getting-started.md)
- [Tests de serialización JSON](./serialization.md)
- [Tests de base de datos](./database.md)
- [Routers](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← Volver al índice](../README.md)

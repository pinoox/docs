# Tests de navegador (HTML) en Pinoox

[← Volver al índice](../README.md)

Para páginas Twig y HTML, Pinoox usa **tests Feature con `appGet()` y `assertSee()`** — no hace falta navegador real ni Dusk. HTTP se simula y se comprueba el contenido HTML.

---

## Requisitos previos

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Página de inicio — título y texto

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

## Formulario — presencia de campos

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

## Redirección tras POST

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

## Página 404

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Combinado con base de datos

Si una página depende de datos DB, crea registros primero (dentro de `inApp`), luego abre la página:

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

## Ejecutar tests

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Limitación

Este enfoque no ejecuta JavaScript del lado del cliente (Vue/Vite SPA). Para SPAs, usa tests API (`appPostJson`) y, cuando haga falta, tests E2E separados en la capa frontend.

---

## Documentación relacionada

- [Tests HTTP](./http-tests.md)
- [Tests de base de datos](./database.md)
- [Vistas](../basic/views.md)
- [Plantillas](../basic/templates.md)
- [Tests de serialización](./serialization.md)

---

[← Volver al índice](../README.md)

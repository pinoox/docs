# Browser (HTML) Testing in Pinoox

[← Back to index](../README.md)

For Twig and HTML pages, Pinoox uses **Feature tests with `appGet()` and `assertSee()`** — no real browser or Dusk required. HTTP is simulated and HTML content is asserted.

---

## Prerequisites

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Home page — title and text

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

## Form — field presence

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

## Redirect after POST

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

## 404 page

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Combined with database

If a page depends on DB data, create records first (inside `inApp`), then open the page:

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

## Running tests

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Limitation

This approach does not execute client-side JavaScript (Vue/Vite SPA). For SPAs, use API tests (`appPostJson`) and, when needed, separate E2E tests in the frontend layer.

---

## Related docs

- [HTTP tests](./http-tests.md)
- [Database tests](./database.md)
- [Views](../basic/views.md)
- [Templates](../basic/templates.md)
- [Serialization tests](./serialization.md)

---

[← Back to index](../README.md)

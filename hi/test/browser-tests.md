# Browser (HTML) Testing in Pinoox

[← इंडेक्स पर वापस जाएँ](../README.md)

Twig और HTML pages के लिए Pinoox **Feature tests with `appGet()` and `assertSee()`** उपयोग करता है — real browser या Dusk की ज़रूरत नहीं। HTTP simulate होता है और HTML content assert होता है।

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

## Database के साथ combined

Page DB data पर depend करे तो पहले records create करें (`inApp` के अंदर), फिर page खोलें:

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

यह approach client-side JavaScript (Vue/Vite SPA) execute नहीं करता। SPAs के लिए API tests (`appPostJson`) और ज़रूरत हो तो frontend layer में separate E2E tests उपयोग करें।

---

## संबंधित docs

- [HTTP tests](./http-tests.md)
- [Database tests](./database.md)
- [Views](../basic/views.md)
- [Templates](../basic/templates.md)
- [Serialization tests](./serialization.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

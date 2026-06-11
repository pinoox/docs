# Browser- (HTML-)Tests in Pinoox

[← Zurück zum Index](../README.md)

Für Twig- und HTML-Seiten verwendet Pinoox **Feature-Tests mit `appGet()` und `assertSee()`** — kein echter Browser oder Dusk nötig. HTTP wird simuliert und HTML-Inhalt wird geprüft.

---

## Voraussetzungen

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Startseite — Titel und Text

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

## Formular — Feldvorhandensein

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

## Redirect nach POST

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

## 404-Seite

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Kombiniert mit Datenbank

Wenn eine Seite von DB-Daten abhängt, zuerst Datensätze anlegen (innerhalb von `inApp`), dann die Seite öffnen:

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

## Tests ausführen

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Einschränkung

Dieser Ansatz führt kein clientseitiges JavaScript (Vue/Vite-SPA) aus. Für SPAs API-Tests (`appPostJson`) und bei Bedarf separate E2E-Tests in der Frontend-Schicht verwenden.

---

## Verwandte Dokumentation

- [HTTP-Tests](./http-tests.md)
- [Datenbank-Tests](./database.md)
- [Views](../basic/views.md)
- [Templates](../basic/templates.md)
- [Serialisierungstests](./serialization.md)

---

[← Zurück zum Index](../README.md)

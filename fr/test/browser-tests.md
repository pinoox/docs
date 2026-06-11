# Tests navigateur (HTML) dans Pinoox

[← Retour à l'index](../README.md)

Pour les pages Twig et HTML, Pinoox utilise des **tests Feature avec `appGet()` et `assertSee()`** — pas de vrai navigateur ni Dusk requis. Le HTTP est simulé et le contenu HTML est asserté.

---

## Prérequis

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Page d'accueil — titre et texte

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

## Formulaire — présence des champs

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

## Redirection après POST

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

## Page 404

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Combiné avec la base de données

Si une page dépend de données DB, créez d'abord les enregistrements (dans `inApp`), puis ouvrez la page :

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

## Exécuter les tests

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Limitation

Cette approche n'exécute pas le JavaScript côté client (SPA Vue/Vite). Pour les SPA, utilisez les tests API (`appPostJson`) et, si nécessaire, des tests E2E séparés dans la couche frontend.

---

## Documentation associée

- [Tests HTTP](./http-tests.md)
- [Tests base de données](./database.md)
- [Views](../basic/views.md)
- [Modèles](../basic/templates.md)
- [Tests de sérialisation](./serialization.md)

---

[← Retour à l'index](../README.md)

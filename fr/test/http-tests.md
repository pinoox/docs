# Tests HTTP dans Pinoox

[← Retour à l'index](../README.md)

Pour tester les contrôleurs, API et Flows, utilisez les helpers HTTP Pinoox : `appGet()`, `appPost()` et `appPostJson()`. Chacun renvoie un `TestResponse` avec des assertions intégrées.

---

## Prérequis

Dans `apps/{package}/tests/Pest.php`, définissez le paquet de l'app dans `beforeEach` :

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — liste de produits

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

## POST formulaire — soumettre des données

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

## Requête personnalisée

Pour les en-têtes, cookies ou méthodes HTTP inhabituelles, utilisez `appCall()` :

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## Assertions TestResponse

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // texte HTML
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // tableau JSON
$response->status();
$response->content();
```

---

## Tester un Flow (ex. auth)

Testez les routes protégées par Flow comme tout autre endpoint :

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Connectez l'utilisateur dans inApp, puis :
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## Exécuter les tests

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## Documentation associée

- [Premiers pas avec les tests](./getting-started.md)
- [Tests de sérialisation JSON](./serialization.md)
- [Tests base de données](./database.md)
- [Router](../basic/routers.md)
- [Contrôleurs](../basic/controllers.md)
- [Réponses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← Retour à l'index](../README.md)

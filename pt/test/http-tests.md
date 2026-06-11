# Testes HTTP no Pinoox

[← Voltar ao índice](../README.md)

Para testar controllers, APIs e Flows, use os helpers HTTP do Pinoox: `appGet()`, `appPost()` e `appPostJson()`. Cada um retorna um `TestResponse` com asserções embutidas.

---

## Pré-requisitos

Em `apps/{package}/tests/Pest.php`, defina o pacote do app em `beforeEach`:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — lista de produtos

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

## POST de formulário — enviar dados

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

## Requisição personalizada

Para headers, cookies ou métodos HTTP incomuns, use `appCall()`:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## Asserções do TestResponse

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

## Testar Flow (ex.: auth)

Teste rotas protegidas por Flow como qualquer outro endpoint:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Faça login do usuário dentro de inApp, depois:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## Executar testes

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## Documentação relacionada

- [Primeiros passos com testes](./getting-started.md)
- [Testes de serialização JSON](./serialization.md)
- [Testes de banco de dados](./database.md)
- [Routers](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Respostas](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← Voltar ao índice](../README.md)

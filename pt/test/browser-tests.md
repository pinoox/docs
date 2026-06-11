# Testes de browser (HTML) no Pinoox

[← Voltar ao índice](../README.md)

Para páginas Twig e HTML, o Pinoox usa **testes Feature com `appGet()` e `assertSee()`** — sem browser real ou Dusk. O HTTP é simulado e o conteúdo HTML é assertado.

---

## Pré-requisitos

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Página inicial — título e texto

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

## Formulário — presença de campos

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

## Redirect após POST

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

## Combinado com banco de dados

Se uma página depende de dados do DB, crie registros primeiro (dentro de `inApp`), depois abra a página:

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

## Executar testes

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Limitação

Esta abordagem não executa JavaScript no cliente (SPA Vue/Vite). Para SPAs, use testes de API (`appPostJson`) e, quando necessário, testes E2E separados na camada de frontend.

---

## Documentação relacionada

- [Testes HTTP](./http-tests.md)
- [Testes de banco de dados](./database.md)
- [Views](../basic/views.md)
- [Templates](../basic/templates.md)
- [Testes de serialização](./serialization.md)

---

[← Voltar ao índice](../README.md)

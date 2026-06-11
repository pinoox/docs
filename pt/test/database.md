# Testes de banco de dados no Pinoox

[← Voltar ao índice](../README.md)

Para testar models, migrations e endpoints dependentes de DB, execute código dentro de `inApp()` e use o banco de teste (`mode=test`). Execute migrations antes dos testes para o schema estar pronto.

---

## Pré-requisitos

1. Em `.env.testing` (ou `.env`), configure um banco de teste separado da produção.
2. Execute migrations do app uma vez no setup de testes.

```php
// apps/com_my_shop/tests/Pest.php

beforeEach(function () {
    appPackage('com_my_shop');
});

beforeAll(function () {
    $root = dirname(__DIR__, 4);
    $process = new Symfony\Component\Process\Process(
        ['php', 'pinoox', 'migrate', 'com_my_shop', '--force'],
        $root
    );
    $process->run();
});
```

---

## Unit — criar e ler um model

```php
// apps/com_my_shop/tests/Unit/ProductModelTest.php

it('creates a product', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::create([
            'title' => 'Test product',
            'price' => 99000,
        ]);

        expect($product->id)->not->toBeNull()
            ->and($product->title)->toBe('Test product');
    });
});
```

---

## Feature — API com banco de dados

```php
it('lists products from database', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'Book',
            'price' => 50000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Book');
});
```

---

## Limpeza entre testes

Para evitar colisão de dados, truncate tabelas relacionadas após cada teste:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Testar migrations

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Nomes de tabela usam o prefixo do pacote (`{package}_`) conforme convenção do app.

---

## Executar testes

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Dicas

1. Nunca execute testes contra banco de produção — use `APP_ENV=test` e DB separado.
2. Chame seeders em `beforeEach` apenas quando necessário; prefira criar dados mínimos dentro de cada teste.
3. Teste queries e relações em **Unit**; teste endpoints completos em **Feature**.

---

## Documentação relacionada

- [Primeiros passos com testes](./getting-started.md)
- [Testes HTTP](./http-tests.md)
- [Primeiros passos com banco de dados](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent — primeiros passos](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← Voltar ao índice](../README.md)

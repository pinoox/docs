# Tests base de données dans Pinoox

[← Retour à l'index](../README.md)

Pour tester les modèles, migrations et endpoints dépendants de la DB, exécutez le code dans `inApp()` et utilisez la base de test (`mode=test`). Exécutez les migrations avant les tests pour que le schéma soit prêt.

---

## Prérequis

1. Dans `.env.testing` (ou `.env`), configurez une base de test séparée de la production.
2. Exécutez les migrations de l'app une fois dans le setup de test.

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

## Unit — créer et lire un modèle

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

## Feature — API avec base de données

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

## Nettoyage entre les tests

Pour éviter les collisions de données, tronquez les tables concernées après chaque test :

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Tester les migrations

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Les noms de table utilisent le préfixe du paquet (`{package}_`) selon la convention de l'app.

---

## Exécuter les tests

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Conseils

1. N'exécutez jamais les tests contre une base de production — utilisez `APP_ENV=test` et une DB séparée.
2. Appelez les seeders dans `beforeEach` uniquement si nécessaire ; préférez créer des données minimales dans chaque test.
3. Testez requêtes et relations en **Unit** ; testez les endpoints complets en **Feature**.

---

## Documentation associée

- [Premiers pas avec les tests](./getting-started.md)
- [Tests HTTP](./http-tests.md)
- [Premiers pas base de données](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent — premiers pas](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← Retour à l'index](../README.md)

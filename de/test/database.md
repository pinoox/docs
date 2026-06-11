# Datenbank-Tests in Pinoox

[← Zurück zum Index](../README.md)

Zum Testen von Models, Migrationen und DB-abhängigen Endpunkten Code innerhalb von `inApp()` ausführen und die Testdatenbank (`mode=test`) verwenden. Migrationen vor den Tests ausführen, damit das Schema bereit ist.

---

## Voraussetzungen

1. In `.env.testing` (oder `.env`) eine Testdatenbank getrennt von der Produktion konfigurieren.
2. App-Migrationen einmal im Test-Setup ausführen.

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

## Unit — Model erstellen und lesen

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

## Feature — API mit Datenbank

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

## Aufräumen zwischen Tests

Um Datenkollisionen zu vermeiden, verwandte Tabellen nach jedem Test leeren:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Migrationen testen

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Tabellennamen verwenden das Package-Präfix (`{package}_`) gemäß App-Konvention.

---

## Tests ausführen

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Tipps

1. Tests niemals gegen eine Produktionsdatenbank — `APP_ENV=test` und separate DB verwenden.
2. Seeder nur in `beforeEach` aufrufen, wenn nötig; minimale Daten lieber in jedem Test anlegen.
3. Abfragen und Beziehungen in **Unit** testen; vollständige Endpunkte in **Feature**.

---

## Verwandte Dokumentation

- [Erste Schritte beim Testen](./getting-started.md)
- [HTTP-Tests](./http-tests.md)
- [Datenbank — Erste Schritte](../database/getting-started.md)
- [Migrationen](../database/migrations.md)
- [Eloquent — Erste Schritte](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← Zurück zum Index](../README.md)

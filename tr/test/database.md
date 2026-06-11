# Pinoox'ta veritabanı testleri

[← Dizine dön](../README.md)

Model, migration ve DB'ye bağlı endpoint'leri test etmek için kodu `inApp()` içinde çalıştırın ve test veritabanını (`mode=test`) kullanın. Şema hazır olsun diye testlerden önce migration'ları çalıştırın.

---

## Ön koşullar

1. `.env.testing` (veya `.env`) içinde üretimden ayrı bir test veritabanı yapılandırın.
2. Test kurulumunda uygulama migration'larını bir kez çalıştırın.

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

## Unit — model oluşturma ve okuma

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

## Feature — veritabanı ile API

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

## Testler arası temizlik

Veri çakışmasını önlemek için her testten sonra ilgili tabloları temizleyin:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Migration testi

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Tablo adları uygulama kuralına göre paket öneki kullanır (`{package}_`).

---

## Testleri çalıştırma

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## İpuçları

1. Testleri asla üretim veritabanında çalıştırmayın — `APP_ENV=test` ve ayrı DB kullanın.
2. Seeder'ları yalnızca gerektiğinde `beforeEach` içinde çağırın; her testte minimal veri oluşturmayı tercih edin.
3. Sorguları ve ilişkileri **Unit**'te test edin; tam endpoint'leri **Feature**'da test edin.

---

## İlgili dokümantasyon

- [Teste başlarken](./getting-started.md)
- [HTTP testleri](./http-tests.md)
- [Veritabanına başlarken](../database/getting-started.md)
- [Migration'lar](../database/migrations.md)
- [Eloquent — başlarken](../eloquent-orm/getting-started.md)
- [Factory'ler](../eloquent-orm/factories.md)

---

[← Dizine dön](../README.md)

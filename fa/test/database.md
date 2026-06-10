# تست دیتابیس در پینوکس

برای تست مدل، migration و endpointهای وابسته به DB، کد را داخل `inApp()` اجرا کنید و از دیتابیس تست (`mode=test`) استفاده کنید. migrationها را قبل از تست اجرا کنید تا schema آماده باشد.

---

## پیش‌نیاز

1. در `.env.testing` (یا `.env`) اتصال دیتابیس تست را جدا از production تنظیم کنید.
2. migrationهای اپ را یک‌بار در setup تست اجرا کنید.

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

## Unit — ایجاد و خواندن مدل

```php
// apps/com_my_shop/tests/Unit/ProductModelTest.php

it('creates a product', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::create([
            'title' => 'محصول تست',
            'price' => 99000,
        ]);

        expect($product->id)->not->toBeNull()
            ->and($product->title)->toBe('محصول تست');
    });
});
```

---

## Feature — API با دیتابیس

```php
it('lists products from database', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'کتاب',
            'price' => 50000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.title', 'کتاب');
});
```

---

## پاک‌سازی بین تست‌ها

برای جلوگیری از تداخل داده، بعد از هر تست جدول‌های مرتبط را truncate کنید:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## تست migration

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

نام جدول با پیشوند پکیج (`{package}_`) مطابق convention اپ است.

---

## اجرا

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## نکات

1. هرگز تست را روی دیتابیس production اجرا نکنید — `APP_ENV=test` و DB جداگانه.
2. seeder را فقط وقتی لازم است در `beforeEach` صدا بزنید؛ ترجیحاً داده minimal در خود تست بسازید.
3. query و relation را در **Unit**؛ endpoint کامل را در **Feature** تست کنید.

---

## مستندات مرتبط

- [شروع تست در پینوکس](./getting-started.md)
- [تست HTTP](./http-tests.md)
- [شروع دیتابیس](../database/getting-started.md)
- [Migrations](../database/migrations.md)
- [Eloquent — شروع](../eloquent-orm/getting-started.md)
- [Factoryها](../eloquent-orm/factories.md)

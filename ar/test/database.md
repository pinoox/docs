# اختبار قاعدة البيانات في Pinoox

[← العودة إلى الفهرس](../README.md)

لاختبار النماذج والترحيلات ونقاط النهاية المعتمدة على DB، شغّل الكود داخل `inApp()` واستخدم قاعدة الاختبار (`mode=test`). شغّل الترحيلات قبل الاختبارات حتى يكون المخطط جاهزًا.

---

## المتطلبات المسبقة

1. في `.env.testing` (أو `.env`)، اضبط قاعدة اختبار منفصلة عن الإنتاج.
2. شغّل ترحيلات التطبيق مرة في إعداد الاختبار.

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

## Unit — إنشاء وقراءة نموذج

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

## Feature — API مع قاعدة البيانات

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

## التنظيف بين الاختبارات

لتجنب تصادم البيانات، افرغ الجداول ذات الصلة بعد كل اختبار:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## اختبار الترحيلات

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

أسماء الجداول تستخدم بادئة الحزمة (`{package}_`) حسب اتفاقية التطبيق.

---

## تشغيل الاختبارات

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## نصائح

1. لا تشغّل الاختبارات على قاعدة إنتاج — استخدم `APP_ENV=test` وDB منفصلة.
2. استدعِ seeders في `beforeEach` فقط عند الحاجة؛ يُفضّل إنشاء بيانات minimal داخل كل اختبار.
3. اختبر الاستعلامات والعلاقات في **Unit**؛ اختبر نقاط النهاية الكاملة في **Feature**.

---

## وثائق ذات صلة

- [البدء مع الاختبار](./getting-started.md)
- [اختبارات HTTP](./http-tests.md)
- [البدء مع قاعدة البيانات](../database/getting-started.md)
- [الترحيلات (Migrations)](../database/migrations.md)
- [Eloquent — البدء](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← العودة إلى الفهرس](../README.md)

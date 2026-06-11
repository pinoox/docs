# البدء مع الاختبار في Pinoox

[← العودة إلى الفهرس](../README.md)

يستخدم Pinoox أسلوبًا واحدًا لـ **نواة الإطار** (`tests/`) و**كل تطبيق** (`apps/{package}/tests/`): [Pest](https://pestphp.com/)، bootstrap مشترك، و`AppTestKit`. يشرح هذا الدليل سير العمل المعياري مع أمثلة عملية.

---

## مكدس الاختبار

| الأداة | الدور |
|------|------|
| Pest | تشغيل اختبارات PHP |
| `Pinoox\Component\Test\AppTestKit` | إقلاع البيئة، تطبيق مؤقت، طلبات HTTP |
| `tests/bootstrap.php` | نقطة دخول مشتركة لاختبارات النواة والتطبيق |

---

## تشغيل الاختبارات

```bash
# All core tests
vendor/bin/pest

# From CLI (interactive package selection)
php pinoox test

# A specific app
php pinoox test com_my_shop

# Filter by test name
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Feature or Unit only
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

في CI يمكنك أيضًا استخدام السكربتات في `composer.json`:

```bash
composer test          # platform tests
composer test:apps     # all app tests
```

---

## بنية مجلد اختبار التطبيق

`php pinoox app:create` ينشئ مجلد `tests/` تلقائيًا:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

إنشاء اختبار جديد:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## ملف `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

المساعد `appPackage()` يضبط التطبيق النشط للمساعدات والاكتشاف التلقائي.

---

## المساعدات العامة

| المساعد | الغرض |
|--------|---------|
| `appPackage($package?)` | ضبط / قراءة الحزمة النشطة |
| `inApp($package, fn)` | تشغيل كود داخل `App::meeting()` |
| `appPath($package, $sub = '')` | مسار مجلد التطبيق |
| `fakeApp($package, $files)` | إنشاء تطبيق مؤقت بملفات مخصصة |
| `deleteFakeApp($package)` | إزالة تطبيق مؤقت |
| `appGet($package, $uri, ...)` | طلب GET → `TestResponse` |
| `appPost($package, $uri, $data)` | طلب POST |
| `appPostJson($package, $uri, $json)` | طلب POST JSON |
| `pinooxBoot()` | إقلاع بيئة الاختبار |

---

## Unit — اختبار فئة Component

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — smoke test لإقلاع التطبيق

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## النواة مقابل التطبيق

| الموقع | الغرض | الحالة الأساسية |
|----------|---------|-----------|
| `tests/Feature/` | الإطار، portals، router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP، Flow، تكامل | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component، منطق خالص | `Tests\AppTestCase` |

---

## وضع الاختبار

في بيئة الاختبار، `mode` يُضبط تلقائيًا إلى `test`:

```php
config('~pinoox')->get('mode'); // 'test'
```

في CI، اضبط `.env.testing` أو `APP_ENV=test` عند الحاجة.

---

## نصائح

1. بعد `fakeApp()`، استدعِ دائمًا `deleteFakeApp()` في `afterEach`.
2. استخدم `inApp()` للإعدادات أو portals أو نماذج داخل تطبيق.
3. استخدم `appGet` / `appPostJson` للمسارات وAPIs.
4. المسارات → **Feature**؛ فئات `Component/` → **Unit**.
5. استخدم `php pinoox test:create` بدل نسخ الملفات يدويًا.

---

## وثائق ذات صلة

- [اختبارات HTTP](./http-tests.md)
- [اختبارات Console](./console-tests.md)
- [اختبارات المتصفح (HTML)](./browser-tests.md)
- [اختبارات قاعدة البيانات](./database.md)
- [Mocking](./mocking.md)
- [تطبيقك الأول](../start/your-first-app.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)

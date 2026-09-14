# شروع تست در پینوکس

[← بازگشت به فهرست](../README.md)

پینوکس برای **هسته فریمورک** (`tests/`) و **هر اپ** (`apps/{package}/tests/`) یک روش واحد دارد: [Pest](https://pestphp.com/)، بوت‌استرپ مشترک و `AppTestKit`. در این راهنما همان روش استاندارد را با مثال عملی می‌بینید.

---

## پشته تست

| ابزار | نقش |
|-------|-----|
| Pest | اجرای تست‌های PHP |
| `Pinoox\Component\Test\AppTestKit` | بوت محیط، اپ موقت، درخواست HTTP |
| `tests/bootstrap.php` | نقطه ورود مشترک برای تست هسته و اپ |

---

## اجرای تست‌ها

```bash
# همه تست‌های هسته
vendor/bin/pest

# از CLI (انتخاب تعاملی پکیج)
php pinoox test

# یک اپ مشخص
php pinoox test com_my_shop

# فیلتر بر اساس نام تست
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# فقط Feature یا Unit
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

در CI می‌توانید از اسکریپت‌های `composer.json` هم استفاده کنید:

```bash
composer test          # تست پلتفرم
composer test:apps     # تست همه اپ‌ها
```

---

## ساختار پوشه تست اپ

با `php pinoox app:create` پوشه `tests/` به‌صورت خودکار ساخته می‌شود:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← بوت‌استرپ + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

ساخت تست جدید:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## فایل `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

تابع `appPackage()` اپ فعال را برای helperها و تشخیص خودکار تنظیم می‌کند.

---

## Helperهای سراسری

| Helper | کاربرد |
|--------|--------|
| `appPackage($package?)` | تنظیم / خواندن پکیج فعال |
| `inApp($package, fn)` | اجرای کد داخل `App::meeting()` |
| `appPath($package, $sub = '')` | مسیر پوشه اپ |
| `fakeApp($package, $files)` | ساخت اپ موقت با فایل‌های دلخواه |
| `deleteFakeApp($package)` | حذف اپ موقت |
| `appGet($package, $uri, ...)` | درخواست GET → `TestResponse` |
| `appPost($package, $uri, $data)` | درخواست POST |
| `appPostJson($package, $uri, $json)` | درخواست JSON POST |
| `pinooxBoot()` | بوت محیط تست |

---

## Unit — تست کلاس Component

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

## Feature — smoke test بوت اپ

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## هسته در برابر اپ

| محل | هدف | Base case |
|-----|-----|-----------|
| `tests/Feature/` | فریمورک، پورتال، روتر | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP، Flow، یکپارچگی | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component، منطق خالص | `Tests\AppTestCase` |

---

## حالت test

در محیط تست، `mode` به‌صورت خودکار روی `test` قرار می‌گیرد:

```php
config('~pinoox')->get('mode'); // 'test'
```

در CI در صورت نیاز `.env.testing` یا `APP_ENV=test` تنظیم کنید.

---

## نکات

1. بعد از `fakeApp()` حتماً در `afterEach` تابع `deleteFakeApp()` را صدا بزنید.
2. برای config، پورتال یا مدل داخل اپ از `inApp()` استفاده کنید.
3. برای روت و API از `appGet` / `appPostJson` استفاده کنید.
4. روت‌ها → **Feature**؛ کلاس‌های `Component/` → **Unit**.
5. به‌جای کپی دستی فایل، `php pinoox test:create` بزنید.

---

## مستندات مرتبط

- [تست HTTP](./http-tests.md)
- [تست Console](./console-tests.md)
- [تست مرورگر (HTML)](./browser-tests.md)
- [تست دیتابیس](./database.md)
- [Mocking — شبیه‌سازی](./mocking.md)
- [رویدادها (Events)](../advanced/events.md)
- [ساخت اولین اپلیکیشن](../start/your-first-app.md)
- [ساختار پوشه‌بندی](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

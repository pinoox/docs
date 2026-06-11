# Mocking در پینوکس

[← بازگشت به فهرست](../README.md)

برای جدا کردن واحد تحت تست از وابستگی‌های خارجی از **Pest + Mockery** (`mock()`) برای کلاس‌ها و **`fakeApp()`** برای اپ موقت استفاده کنید. هر دو در bootstrap تست پینوکس در دسترس‌اند.

---

## Mock کلاس — Unit

```php
// apps/com_my_shop/tests/Unit/OrderServiceTest.php

use App\com_my_shop\Component\PaymentGateway;
use App\com_my_shop\Component\OrderService;

it('charges via payment gateway', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('charge')
        ->once()
        ->with(100000)
        ->andReturn(['status' => 'paid']);

    $service = new OrderService($gateway);

    expect($service->checkout(100000))->toBe(['status' => 'paid']);
});
```

---

## Mock Portal / سرویس static

وقتی Portal به Component delegate می‌کند، منطق را در Component تست کنید و Component را mock کنید — نه Portal را مستقیم.

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // سرویس تحت تست را با mock inject کنید
    });
});
```

---

## fakeApp — اپ موقت (تست هسته)

برای تست router یا boot بدون ساخت اپ واقعی:

```php
beforeEach(fn () => fakeApp('com_test_shop', [
    'app.php' => '<?php return [
        "package" => "com_test_shop",
        "enable" => true,
        "router" => ["routes" => ["routes/web.php"]],
    ];',
    'routes/web.php' => '<?php use function Pinoox\Router\get; get("/", fn() => "OK");',
]));

afterEach(fn () => deleteFakeApp('com_test_shop'));

it('loads custom routes', function () {
    expect(Pinoox\Portal\App\AppEngine::exists('com_test_shop'))->toBeTrue();
});
```

**مهم:** همیشه `deleteFakeApp()` در `afterEach` فراموش نشود.

---

## fake HTTP — بدون mock دستی

برای endpoint واقعی به‌جای mock کنترلر از `appGet` / `appPostJson` استفاده کنید — ساده‌تر و نزدیک‌تر به رفتار production.

---

## Spy — بررسی فراخوانی

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... اجرای کد
```

---

## اجرا

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## نکات

1. فقط وابستگی‌های slow یا external (ایمیل، پرداخت، API خارجی) را mock کنید.
2. منطق DB و HTTP integration را در Feature با دیتابیس تست بنویسید، نه mock سنگین.
3. بعد از `fakeApp()` حتماً cleanup انجام دهید.

---

## مستندات مرتبط

- [شروع تست در پینوکس](./getting-started.md)
- [تست HTTP](./http-tests.md)
- [تست Console](./console-tests.md)
- [Portal — پورتال](../basic/portal.md)
- [سرویس‌ها](../advanced/services.md)

---

[← بازگشت به فهرست](../README.md)

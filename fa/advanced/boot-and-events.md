# boot.php و رویدادها

[← بازگشت به فهرست](../README.md)

علاوه بر `routes/`، می‌توانید در **`boot.php`** مسیر، API، Flow، schedule و listener ثبت کنید — مفید برای **افزونه**، micro-module، یا اتصال به اپ میزبان (مثل manager).

---

## سه حالت اپ

| حالت | تنظیم | رفتار |
|------|--------|--------|
| **Route only** | فقط `router.routes` | فقط وقتی URL اپ فعال است |
| **Boot global** | `boot-global => true` | در هر درخواست HTTP boot می‌شود |
| **Boot + Route** | `boot.php` + routes | پیش‌فرض scaffold |

افزونه روی اپ میزبان:

```php
'extends' => ['com_pinoox_manager'],
```

فقط وقتی manager boot شود، افزونه شما هم boot می‌شود (سبک‌تر از global).

---

## boot.php پایه

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => response()->json(['ok' => true]),
        'name' => 'health',
    ]);

    $register->when('com_pinoox_manager', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => response()->json(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['manager.auth'],
        ]);
    });
};
```

---

## AppRegister — متدهای پرکاربرد

| متد | کاربرد |
|-----|--------|
| `web(callable)` | ثبت route با builder |
| `route([...])` | یک route web |
| `api([manifest])` | manifest کامل API |
| `apiRoute([...])` | یک endpoint API |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | alias Flow |
| `schedule(callable)` | task زمان‌بندی |
| `listen('event', listener)` | listener رویداد |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | hook روی boot اپ دیگر |

---

## Portal Event

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

در [ایمیل](./mail.md) از Event برای جدا کردن ارسال از کنترلر استفاده شده است.

**Flow** = قبل از کنترلر (middleware). **Event** = بعد از عمل (side effect).

---

## Helper

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();              // boot اپ جاری
AppBoot::booted('com_acme');    // bool

app_boot();                     // helper
```

---

## cache boot

کلید `'boot' => true` در `app.php` → `cache.stores` باعث bake شدن boot در Pinker می‌شود — [Pinker](./pinker.md).

---

## مستندات مرتبط

- [زمان‌بندی — Schedule](./schedule.md)
- [فلو — Flow](../basic/flows.md)
- [روتر](../basic/routers.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

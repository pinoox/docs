# مرجع app.php

[← بازگشت به فهرست](../README.md)

`app.php` manifest اپ است. schema پیش‌فرض در `vendor/pinoox/pincore/Component/Package/data/source.php` تعریف شده — فقط کلیدهایی که نیاز دارید override کنید.

---

## هویت و فعال‌سازی

| کلید | کاربرد |
|------|--------|
| `package` | نام پوشه = namespace — [قاعده نام‌گذاری پکیج](./package-naming.md) |
| `name` | نام نمایشی |
| `enable` | فعال / غیرفعال |
| `description`, `developer`, `icon` | metadata |
| `version-name`, `version-code` | نسخه اپ |
| `sys-app`, `hidden`, `dock` | اپ سیستمی / مخفی / dock manager |
| `minpin` | حداقل نسخه پلتفرم |

---

## Router و boot

| کلید | کاربرد |
|------|--------|
| `router.routes` | فایل‌های `routes/*.php` |
| `boot` | اجرای `boot.php` (پیش‌فرض true) |
| `boot-global` | boot در هر درخواست |
| `extends` | boot وقتی اپ میزبان boot شد |
| `loader` | فایل‌های اضافه (`func.php`) |
| `depends` | اپ‌های پیش‌نیاز / اختیاری — [وابستگی اپ‌ها](./app-depends.md) |

جزئیات boot: [boot.php و رویدادها](../advanced/boot-and-events.md).

---

## Flow و امنیت

| کلید | کاربرد |
|------|--------|
| `flow` | Flowهای سراسری (BootFlow) |
| `alias` | نام → کلاس Flow (`auth`, `manager.auth`, …) |
| `auth` | mode، lifetime، JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | اشتراک user/file/access با platform |

جزئیات: [Flow](../basic/flows.md)، [مدیریت کاربران](../advanced/user-management.md)، [دسترسی](../advanced/access-permissions.md).

---

## UI و theme

| کلید | کاربرد |
|------|--------|
| `theme` | پوشه theme فعال |
| `theme-context`, `theme-contexts` | چند درخت تم — [کانتکست تم](../basic/theme-contexts.md) |
| `theme-extends` | fallback منسوخ؛ ترجیح دهید `extends` در [theme.php](../basic/theme-manifest.md) |
| `frontend` | `stack`، `profile`، `entry`، `manifest` — [فرانت‌اند و Vite](../basic/frontend-vite.md) |
| `lang` | locale پیش‌فرض |
| `open` | رفتار باز شدن در manager (`app-details`, `app-view`) |

---

## دیتابیس و storage

| کلید | کاربرد |
|------|--------|
| `database` | override اتصال DB |
| `table.prefix` | prefix جداول |
| `transport.user` / `file_storage` / `access` | preset یا granular |
| `filesystem` | disk، thumb، access |

---

## Runtime

| کلید | کاربرد |
|------|--------|
| `runtime.mode`, `runtime.debug` | override mode |
| `cache` | bake routes/api/boot/twig |
| `log`, `redis`, `date` | per-app override |
| `container` | DI bindings — [Kernel و pipeline بوت](../advanced/kernel.md) |

### تاریخ و timezone

تقویم و منطقه زمانی را در سطح اپ تنظیم کنید. فرم‌های کوتاه هنگام load manifest نرمال می‌شوند.

| فرم | مثال |
|------|------|
| رشته کوتاه | `'date' => 'jalali'` |
| بلوک کامل | `'date' => ['calendar' => 'jalali', 'timezone' => 'Asia/Tehran']` |
| alias ریشه | `'calendar' => 'jalali'`, `'timezone' => 'Asia/Tehran'` |

مقادیر: `jalali` \| `gregorian`. در صورت عدم تنظیم، از `lang` و پیش‌فرض پلتفرم استفاده می‌شود.

جزئیات: [تاریخ و تقویم](../basic/date-and-calendar.md).

---

## Pinker / Pinx

| کلید | کاربرد |
|------|--------|
| `pinx` | type، minpin، sign |
| `build` | exclude/include برای package |

---

## مثال ترکیبی

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## مستندات مرتبط

- [ساختار پوشه‌بندی](../start/structure.md)
- [وابستگی اپ‌ها](./app-depends.md)
- [کانتکست تم](../basic/theme-contexts.md)
- [مانیفست تم (`theme.php`)](../basic/theme-manifest.md)
- [پیکربندی — Config](../basic/config.md)

---

[← بازگشت به فهرست](../README.md)

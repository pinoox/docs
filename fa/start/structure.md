# ساختار پوشه‌بندی

[← بازگشت به فهرست](../../readme-fa.md)

پینوکس از معماری HMVC استفاده می‌کند: هر اپ در `apps/{package}/` یک ماژول MVC کامل و مستقل است. هسته فریمورک در `vendor/pinoox/pincore/` قرار دارد و فقط برای تغییرات خود پلتفرم ویرایش می‌شود.

---

## ساختار پروژه

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← هسته (Composer package)
├── apps/                    ← همه اپ‌ها
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
│   └── app-router.config.php  ← نگاشت URL → اپ
└── uploads/
```

---

## ساختار یک اپ

```
apps/com_acme_shop/
├── app.php                  ← manifest (الزامی)
├── boot.php                 ← ثبت برنامه‌ای route/event (اختیاری)
├── schedule.php             ← cron (اختیاری)
├── Controller/              ← handlerهای HTTP
├── Model/                   ← Eloquent models
├── Flow/                    ← middleware
├── Component/               ← منطق کسب‌وکار
├── Portal/                  ← facadeهای اپ (اختیاری)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← ثابت‌های نام action (اختیاری)
├── theme/default/           ← Twig + assets
├── lang/fa/                 ← ترجمه
├── config/                  ← تنظیمات اپ
├── database/migrations/
├── database/seed/
├── patches/                 ← Patch داده (یک‌باره)
└── pinker/                  ← mirror برای build
```

**ویو** در پوشه جداگانه `View/` نیست — قالب‌ها در `theme/{themeName}/` قرار می‌گیرند.

---

## app.php — فیلدهای مهم

```php
<?php

return [
    'package' => 'com_acme_shop',   // = نام پوشه
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespace

PSR-4: `App\` → `apps/`

| فایل | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop\Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## قوانین نام‌گذاری

- پکیج: `com_{vendor}_{name}` — مثلاً `com_acme_shop`
- نام پوشه = `package` در `app.php` = بخش namespace
- پیشوند جدول DB: `{package}_` (مثلاً `com_acme_shop_orders`)

---

## مرز اپ و هسته

| تغییر | محل |
|-------|-----|
| endpoint جدید | `apps/{package}/Controller/` + `routes/` |
| migration | `apps/{package}/database/migrations/` |
| باگ فریمورک | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

اپ‌ها به یکدیگر وابسته نشوند — فقط از Portalهای `Pinoox\Portal\*` استفاده کنید.

---

## مستندات مرتبط

- [ساخت اولین اپلیکیشن](./your-first-app.md)
- [روتر](../basic/routers.md)
- [کنترلر](../basic/controllers.md)
- [فلو — Flow](../basic/flows.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

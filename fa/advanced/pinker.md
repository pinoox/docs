# Pinker و Cache

**Pinker** لایه bake/runtime پینوکس ۳.x است: config و cache از سورس به PHP قابل `include` تبدیل می‌شود تا boot سریع‌تر شود. مسیر استاندارد هر اپ: **`pinker/apps/{package}/`**.

---

## ساختار پوشه

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← نسخه bake‌شده app.php
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← قالب‌های کامپایل‌شده
```

همچنین در سطح پروژه:

```
pinker/config/          ← config bake‌شده (غیر env-sensitive)
pinker/state/config/    ← override بعد از نصب (مثلاً database)
```

---

## دستورات CLI

```bash
# bake مجدد Pinker برای یک اپ
php pinoox pinker:rebuild com_acme_shop

# alias کوتاه
php pinoox bake com_acme_shop

# وضعیت: مقایسه سورس با bake
php pinoox pinker:status com_acme_shop

# ساخت cache (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# فقط Twig
php pinoox cache:build com_acme_shop --only=twig

# فقط Pinker
php pinoox cache:build com_acme_shop --only=pinker

# پاک‌سازی
php pinoox cache:clear com_acme_shop
```

---

## چه زمانی rebuild کنیم؟

| رویداد | دستور |
|--------|--------|
| تغییر `app.php` یا config | `pinker:rebuild` |
| تغییر route / api | `cache:build` |
| تغییر فایل `.twig` در production | `cache:build --only=twig` |
| بعد از نصب روی سرور | `cache:build` + `pinker:rebuild` |
| قبل از ساخت `.pinx` | `cache:build` (cache داخل پکیج) |

---

## فعال‌سازی cache در runtime

در `apps/{package}/app.php`:

```php
'cache' => [
    'enabled' => false,   // پیش‌فرض — در production می‌توانید true کنید
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## mirror در اپ — `pinker/app.php`

هر اپ می‌تواند mirror bake‌شده داشته باشد:

```
apps/com_acme_shop/pinker/app.php   ← منبع/مرجع در repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## helper `pinker()`

برای bake دستی داده:

```php
pinker($data, ['lifetime' => 3600]);
```

معمولاً از CLI استفاده می‌کنید؛ در اپ روزمره لازم نیست.

---

## workflow پیشنهادی deploy

```bash
# 1. build frontend
php pinoox theme:frontend build com_acme_shop

# 2. cache
php pinoox cache:build com_acme_shop

# 3. pinker (env-specific)
php pinoox pinker:rebuild com_acme_shop
```

---

## نکات

- `pinker/state/` را دستی edit نکنید — installer آنجا می‌نویسد.
- در development معمولاً cache runtime خاموش است؛ فقط بعد از تغییر سنگین rebuild کنید.
- `.pinx` می‌تواند cache از قبل build‌شده را حمل کند؛ روی سرور مقصد یک بار `cache:build --only=pinker` بزنید.

---

## مستندات مرتبط

- [پیکربندی](../basic/config.md)
- [قالب Twig](../basic/templates.md)
- [CLI](../../../pinoox docs/pinoox-cli.md)
- [روتر](../../basic/routers.md)

# Pinroll — انتشار و دیپلوی

[← بازگشت به فهرست](../README.md)

**Pinroll** (`pinoox/pinroll`) موتور رسمی انتشار و rollout پینوکس است. پکیج اپ می‌سازد، به **هاست** می‌فرستد، از طریق **PinGate** نصب می‌کند و rollback، hook و retention دارد.

Pinroll یک **کتابخانه Composer** است — نه یک اپ پینوکس. دستورات CLI با نصب پکیج ثبت می‌شوند.

| مفهوم | معنی |
|-------|------|
| **Host** | مقصد دیپلوی (`production`، `staging` — کلید آرایه همان نام است) |
| **Transport (`via`)** | نحوه ارسال (`ftp`، `ssh`، `pinion`، `local`) |
| **PinGate** | نقطه ورود HTTP روی هاست (`pingate.php` + `gate/`) برای install / status / rollback |
| **Bundle** | دستور ساخت اختیاری؛ دیپلوی عادی اپ‌ها را خودکار تشخیص می‌دهد |

---

## نصب

روی پروژه **platform**:

```bash
composer require --dev pinoox/pinroll
```

---

## پروسه راه‌اندازی

```mermaid
flowchart LR
    A[pinroll:init] --> B[پر کردن .env]
    B --> C[pinroll:connect]
    C --> D[pinroll:apps]
    D --> E[pinroll:check]
    E --> F[آماده دیپلوی]
```

| مرحله | دستور | کار |
|-------|--------|-----|
| 1 | `php pinoox pinroll:init` | ساخت `pinroll/pinroll.config.php` |
| 2 | ویرایش `.env` | تنظیم اعتبار FTP/SSH با کلیدهای `PINROLL_*` |
| 3 | `php pinoox pinroll:connect` | مسیر دیپلوی، URL سایت، آپلود PinGate |
| 4 | `php pinoox pinroll:apps` | انتخاب پکیج‌های پیش‌فرض هاست |
| 5 | `php pinoox pinroll:check` | بررسی اتصال و PinGate |
| 6 | `php pinoox pinroll:deploy` | ساخت، آپلود و نصب (رفتن لایو) |

```bash
php pinoox pinroll:init
# پر کردن PINROLL_* در .env
php pinoox pinroll:connect
php pinoox pinroll:apps
php pinoox pinroll:check
php pinoox pinroll:deploy
```

---

## راه‌اندازی پروژه

```bash
php pinoox pinroll:init
```

ساختار:

```
pinroll/
  pinroll.config.php
```

دستور ساخت از `apps/` خودکار تشخیص داده می‌شود (برای دیپلوی عادی نیازی به `pinroll/bundles/*.php` نیست).

---

## پیکربندی

### هاست‌ها (`pinroll/pinroll.config.php`)

```php
<?php

return [
    // وقتی در CLI نام هاست ننویسید
    'default_host' => 'production',

    // پیش‌فرض سراسری — هر هاست می‌تواند override کند
    'keep' => 2,
    'store' => 'both',      // local | remote | both
    'auto_clean' => true,   // بعد از install موفق، آرشیوهای قدیمی‌تر از keep پاک شوند

    'hosts' => [
        'production' => [
            'deploy_path' => 'public_html',
            'via' => 'ftp',

            // پکیج‌های پیش‌فرض push/install (یا با pinroll:apps)
            'apps' => ['com_pinoox_shop'],

            'gate' => [
                'url' => env('PINROLL_PRODUCTION_URL', ''),
                'token' => env('PINROLL_PRODUCTION_TOKEN', ''),
            ],

            'ftp' => [
                'host' => env('PINROLL_PRODUCTION_HOST', ''),
                'user' => env('PINROLL_PRODUCTION_USER', ''),
                'password' => env('PINROLL_PRODUCTION_PASSWORD', ''),
            ],

            'hooks' => [
                'before_install' => ['php pinoox migrate --force'],
                'after_install' => ['php pinoox cache:build'],
            ],
        ],
    ],
];
```

| کلید | توضیح |
|------|--------|
| `default_host` | هاست پیش‌فرض وقتی آرگومان CLI خالی است |
| `deploy_path` | ریشه دیپلوی نسبت به لاگین FTP/SSH |
| `hostname` | آدرس اتصال اختیاری اگر با host ترنسپورت فرق دارد |
| `via` | ترنسپورت پیش‌فرض: `ftp`، `ssh`، `pinion`، `local` |
| `gate.url` / `gate.token` | اعتبار PinGate |
| `ftp` / `ssh` | اطلاعات اتصال |
| `apps` | پکیج‌های پیش‌فرض برای push/install |
| `hooks` | دستورات شل اطراف push / install / rollback |
| `keep` / `store` / `auto_clean` | Retention (سراسری یا per-host) |

### کلیدهای `.env`

```env
PINROLL_PRODUCTION_URL=https://example.com/pingate.php?route=
PINROLL_PRODUCTION_TOKEN=…
PINROLL_PRODUCTION_HOST=ftp.example.com
PINROLL_PRODUCTION_USER=…
PINROLL_PRODUCTION_PASSWORD=…
```

---

## انتخاب اپ‌ها

اگر `hosts.*.apps` خالی باشد و `--app` / `--apps` ندهید، push/deploy به‌صورت تعاملی پکیج می‌پرسد.

تنظیم یک‌باره:

```bash
php pinoox pinroll:apps                         # انتخاب تعاملی
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:apps --all
php pinoox pinroll:apps --list
php pinoox pinroll:apps --clear                 # حذف apps[] (دوباره prompt)
```

---

## Connect

```bash
php pinoox pinroll:connect          # بار اول: مسیر دیپلوی + URL سایت + آپلود PinGate
php pinoox pinroll:connect          # دفعات بعد: فقط بررسی اتصال (بدون پرسش)
php pinoox pinroll:connect --reset  # راه‌اندازی مجدد کامل
```

وقتی هاست از قبل پیکربندی شده باشد (`deploy_path` + URL گیت + اعتبار ترنسپورت)، پرسش‌های راه‌اندازی اجرا نمی‌شود و فقط وضعیت اتصال نمایش داده می‌شود.

---

## واژگان CLI

```bash
# با default_host
php pinoox pinroll:push
php pinoox pinroll:install
php pinoox pinroll:deploy

# اپ / هاست صریح
php pinoox pinroll:deploy --app=com_pinoox_shop
php pinoox pinroll:install staging --app=com_pinoox_shop
```

| دستور | کار |
|-------|-----|
| `pinroll:push` | ساخت + آپلود (بدون نصب) |
| `pinroll:install` | نصب release آماده‌شده روی هاست |
| `pinroll:deploy` | push + install (رفتن لایو) |

---

## حالت‌های local

### الف) `via: local` — ترنسپورت

آرشیو در `storage/pinroll/incoming/` همین ماشین ذخیره می‌شود (بدون FTP/SSH).

```bash
php pinoox pinroll:push --via=local --app=com_pinoox_shop
```

### ب) `pinroll:install --local` — نصب روی همین هاست

بعد از SSH به پروداکشن (ریشه سایت):

```bash
php pinoox pinroll:install --local
php pinoox pinroll:install --local --list
```

### ج) `store: local` / `both` — retention

| `store` | آرشیو کجا نگه داشته می‌شود | بعد از install |
|---------|---------------------------|----------------|
| `remote` (پیش‌فرض) | هاست `storage/pinroll/incoming/` | تا `keep` هرس می‌شود |
| `local` | ماشین توسعه (incoming + pinx export) | فقط لوکال |
| `both` | ماشین توسعه **و** هاست | هر دو هرس می‌شوند |

با `store: local|both`، هنگام push یک کپی `.pinx` در `storage/pinroll/incoming/` لوکال برای rollback نگه داشته می‌شود.

---

## Retention

| کلید | مقادیر | رفتار |
|------|--------|--------|
| `keep` | `0`…`N` | N تا جدیدترین؛ `0` یعنی بدون هرس |
| `store` | `local` \| `remote` \| `both` | کدام طرف آرشیو نگه دارد |
| `auto_clean` | bool | بعد از install موفق، قدیمی‌تر از `keep` پاک شود |

**پاک‌سازی لوکال شامل**

- `storage/pinroll/incoming/*.pinx`
- `apps/{package}/pinx/export/*.pinx` (N تا جدیدترین برای هر اپ)
- پوشه‌های موقت release/session زیر `storage/`

**پاک‌سازی ریموت شامل**

- `storage/pinroll/incoming/` روی هاست (از طریق PinGate `/cleanup`)

```bash
php pinoox pinroll:cleanup              # ریموت
php pinoox pinroll:cleanup --local      # همین ماشین
php pinoox pinroll:cleanup --dry-run
php pinoox pinroll:cleanup -k=2
```

---

## Hooks

```php
'hooks' => [
    'before_push' => ['npm run build'],
    'after_push' => [],
    'before_install' => ['php pinoox migrate --force'],
    'after_install' => ['php pinoox cache:build'],
    'before_rollback' => [],
    'after_rollback' => [],
],
```

| Hook | اجرا روی | زمان |
|------|----------|------|
| `before_push` / `after_push` | ماشین توسعه | اطراف آپلود |
| `before_install` / `after_install` | هاست (یا `--local`) | اطراف نصب Pinx |
| `before_rollback` / `after_rollback` | هاست / پایپلاین لوکال | اطراف rollback |

---

## Rollback و migration

`pinroll:rollback` پکیج **قبلی** را با force دوباره نصب می‌کند (کد). به‌صورت خودکار همه migrationها و patchهای دیتابیس را برنمی‌گرداند.

| لایه | در rollback |
|------|-------------|
| فایل‌های اپ / پکیج Pinx | از آرشیو قبلی برمی‌گردد |
| Migration با `down()` | فقط اگر خودتان migrate rollback بزنید (مثلاً در hook) |
| Patch یک‌طرفه / اصلاح داده | برنمی‌گردد |

پیشنهاد عملی:

1. برای مشکل اسکیما ترجیحاً **forward-fix** بفرستید.
2. migrationهای حساس را reversible بنویسید.
3. `keep >= 2` و ترجیحاً `store: both` تا آرشیو قبلی موجود باشد.
4. قبل از دیپلوی پرریسک، بکاپ دیتابیس بگیرید.

```bash
php pinoox pinroll:rollback
php pinoox pinroll:rollback --deploy-id=20260710_091021_3f980930
php pinoox pinroll:migrate:dry-run
```

---

## جریان سریع (FTP + PinGate)

```bash
php pinoox pinroll:init
# پر کردن PINROLL_* در .env
php pinoox pinroll:connect
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:vendor          # اختیاری: هسته/وابستگی‌های هاست
php pinoox pinroll:check
php pinoox pinroll:deploy
```

---

## مسیرهای PinGate

| متد | مسیر | کاربرد |
|-----|------|--------|
| `GET` | `/status` | سلامت / نسخه |
| `GET` | `/incoming` | لیست releaseهای staged |
| `POST` | `/install` | نصب (`/apply` برای سازگاری) |
| `POST` | `/rollback` | نصب مجدد نسخه قبلی |
| `POST` | `/cleanup` | هرس آرشیوهای قدیمی |
| `GET` | `/history` | تاریخچه rollout |

احراز هویت: `Authorization: Bearer {token}`.

---

## مرجع CLI

| دستور | کاربرد |
|-------|--------|
| `pinroll:init` | ساخت `pinroll/pinroll.config.php` |
| `pinroll:connect` | راه‌اندازی / بررسی (`--reset` برای تکرار) |
| `pinroll:apps` | تنظیم `hosts.*.apps` |
| `pinroll:vendor` | خروجی `vendor/` → `pinroll/vendor.zip` |
| `pinroll:gate` | ساخت / آپلود PinGate |
| `pinroll:check` | بررسی هاست / PinGate |
| `pinroll:push` | فقط ساخت و آپلود |
| `pinroll:install` | نصب release آماده |
| `pinroll:deploy` | push + install |
| `pinroll:rollback` | rollback از PinGate یا آرشیو لوکال |
| `pinroll:cleanup` | هرس (`--local`، `--dry-run`، `-k`) |

### گزینه‌های push / deploy

| فلگ | اثر |
|-----|-----|
| (پیش‌فرض) | فقط `.pinx` اپ |
| `--all` | اپ + vendor + theme |
| `--vendor` | همگام‌سازی vendor |
| `--theme` | همگام‌سازی theme dist |
| `--app=` / `--apps=` | انتخاب پکیج |
| `--via=` | override ترنسپورت |
| `--host=` | override هاست |

---

## ترنسپورت‌ها

| `via` | کاربرد |
|-------|--------|
| `ftp` | هاست اشتراکی — آپلود + نصب PinGate |
| `ssh` | VPS — آپلود SFTP، نصب SSH |
| `pinion` | آپلود تکه‌ای HTTP از طریق PinGate |
| `local` | همان ماشین / تست |

---

## مستندات مرتبط

- [پروتکل Pinion](../advanced/pinion.md)
- [مستند کامل انگلیسی](../../en/deploy/pinroll.md)
- [مرجع CLI](../start/cli-reference.md)

---

[← بازگشت به فهرست](../README.md)

# Pinroll — انتشار و دیپلوی

[← بازگشت به فهرست](../README.md)

**Pinroll** (`pinoox/pinroll`) موتور رسمی انتشار و rollout پینوکس است. پکیج اپ می‌سازد، به **هاست** می‌فرستد، از طریق **PinGate** نصب می‌کند و rollback، hook و retention دارد.

Pinroll یک **کتابخانه Composer** است — نه یک اپ پینوکس. دستورات CLI با نصب پکیج ثبت می‌شوند.

| مفهوم | معنی |
|-------|------|
| **Host** | مقصد دیپلوی (`production`، `staging` — کلید آرایه همان نام است) |
| **Transport (`via`)** | نحوه ارسال (`ftp`، `ssh`، `pinion`، `local`) |
| **PinGate** | یک فایل عمومی روی هاست (`pingate.php`) برای install / status / rollback / vendor / نصب اولیه |
| **Bundle** | دستور ساخت اختیاری؛ دیپلوی عادی اپ‌ها را خودکار تشخیص می‌دهد |

---

## نصب

روی پروژه کامل **platform**، Pinroll را روی **ماشین توسعه** نصب کنید (پیشنهاد: `require-dev`):

```bash
composer require --dev pinoox/pinroll
```

هاست به Pinroll داخل `vendor/` نیاز ندارد. `pingate.php` پکیج‌ها را با pincore (`pinx:install` / `pinx:update`) نصب می‌کند. Pinroll را فقط وقتی در `require` بگذارید که بخواهید PinGate روی سرور از کلاس‌های Pinroll استفاده کند.

روی پروژه تک‌اپ (Pinx):

```bash
composer require --dev pinoox/pinroll
pinx deploy
pinx provision   # هاست خالی
```

---

## پروسه راه‌اندازی

```mermaid
flowchart LR
    A[pinroll:init] --> B[پر کردن .env]
    B --> C{هاست خالی؟}
    C -->|بله| D[pinroll:provision]
    C -->|خیر| E[pinroll:connect]
    E --> F[pinroll:apps]
    F --> G[pinroll:check]
    G --> H[pinroll:deploy]
```

| مرحله | دستور | کار |
|-------|--------|-----|
| 1 | `php pinoox pinroll:init` | ساخت `.pinoox/pinroll.config.php` و استاب `.env` |
| 2 | ویرایش `.env` | `PINROLL_*` برای FTP/SSH و برای هاست خالی `PINROLL_DB_*` / `PINROLL_ADMIN_*` |
| 3الف | `php pinoox pinroll:provision` | **هاست خالی:** آپلود PinGate + zip پلتفرم، سپس setup نصب‌کننده |
| 3ب | `php pinoox pinroll:connect` | **سایت موجود:** مسیر دیپلوی، URL، آپلود PinGate |
| 4 | `php pinoox pinroll:apps` | پکیج‌های پیش‌فرض برای دیپلوی‌های بعدی |
| 5 | `php pinoox pinroll:check` | بررسی اتصال و PinGate |
| 6 | `php pinoox pinroll:deploy` | ساخت، آپلود و نصب |
| 6ب | `php pinoox pinroll:deploy --full` | به‌روزرسانی **پلتفرم + همه اپ‌های نصب‌شده** |

```bash
php pinoox pinroll:init
# پر کردن PINROLL_* در .env
php pinoox pinroll:provision   # بار اول روی پوشه FTP خالی
# به‌روزرسانی‌های بعدی:
php pinoox pinroll:deploy --full
```

---

## راه‌اندازی پروژه

```bash
php pinoox pinroll:init
```

ساختار:

```
.pinoox/
  pinroll.config.php
```

مسیر قدیمی `pinroll/pinroll.config.php` اگر باشد هنوز خوانده می‌شود.

دستور ساخت از `apps/` خودکار تشخیص داده می‌شود (برای دیپلوی عادی نیازی به `pinroll/bundles/*.php` نیست).

---

## پیکربندی

### هاست‌ها (`.pinoox/pinroll.config.php`)

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

برای production کلیدهای **بدون پیشوند هاست** هم خوانده می‌شوند (`PINROLL_VIA`، `PINROLL_DB_HOST`، …). بقیه هاست‌ها: `PINROLL_{HOST}_*`.

```env
PINROLL_VIA=ftp
PINROLL_PATH=public_html
PINROLL_URL=https://example.com/pingate.php?route=
PINROLL_TOKEN=…
PINROLL_HOST=ftp.example.com
PINROLL_USER=…
PINROLL_PASSWORD=…

PINROLL_LANG=fa
PINROLL_DB_HOST=localhost
PINROLL_DB_DATABASE=pinoox
PINROLL_DB_USERNAME=…
PINROLL_DB_PASSWORD=…
PINROLL_ADMIN_FNAME=آدا
PINROLL_ADMIN_LNAME=لاولیس
PINROLL_ADMIN_EMAIL=ada@example.com
PINROLL_ADMIN_USERNAME=admin
PINROLL_ADMIN_PASSWORD=…
PINROLL_BUILD_EXCLUDE=docs,tests
```

ترتیب ادغام provision: **فلگ CLI → `.env` → `provision` هاست → `provision` سراسری → پیش‌فرض**.

---

## هاست خالی — `pinroll:provision`

یک‌بار، روی پوشهٔ **خالی** FTP/SFTP (بدون `index.php`). به‌روزرسانی بعدی `pinroll:deploy` است نه provision.

### روش الف — فقط `.env` (غیرتعاملی)

```bash
php pinoox pinroll:init
# PINROLL_HOST / USER / PASSWORD و PINROLL_DB_* / PINROLL_ADMIN_* را پر کنید
php pinoox pinroll:provision --no-interaction
```

### روش ب — فایل کانفیگ

اسرار در `.env`؛ ساختار در `.pinoox/pinroll.config.php` (`provision` + `hosts`). بعد:

```bash
php pinoox pinroll:provision
```

### روش ج — ویزارد تعاملی

فیلدهای DB/مدیر را خالی بگذارید و بدون `--no-interaction` اجرا کنید. همان فیلدهای نصب‌کننده وب پرسیده می‌شود.

### روش د — فلگ CLI

```bash
php pinoox pinroll:provision production \
  --db-host=localhost --db-database=pinoox --db-username=root --db-password=secret \
  --admin-fname=Ada --admin-lname=Lovelace --admin-email=ada@example.com \
  --admin-username=admin --admin-password=secret1 --lang=fa
```

### روش هـ — فقط setup بعد از شکست extract

اگر zip استخراج شد ولی setup شکست خورد:

```bash
php pinoox pinroll:provision --setup-only
```

`--force` روی `index.php` موجود extract می‌کند و setup را بعد از disable شدن نصب‌کننده تکرار می‌کند. `--reupload` دوباره `platform.zip` می‌سازد و آپلود می‌کند.

میانبر Pinx: `pinx provision`.

**محدودیت‌ها:** PHP هاست باید `ZipArchive` و زمان/حافظه کافی داشته باشد (setup تا ۱۰ دقیقه). دیتابیس باید از **همان هاست** قابل اتصال باشد. zip اول بزرگ است.

---

## به‌روزرسانی پلتفرم + همه اپ‌ها — `--full`

`--all` یعنی app + vendor + theme.  
`--full` یعنی **zip پلتفرم (`pinx:update`) به‌علاوه همه اپ‌های کشف‌شده/نصب‌شده**، بدون پرسش.

```bash
php pinoox pinroll:deploy --full
pinx deploy --full
```

با `--app=` می‌توان `--full` را به یک پکیج محدود کرد.

---

## include / exclude در zip پلتفرم

`pinx:build platform` فایل `platform/build.config.php` را می‌خواند و بعد `build` داخل `.pinoox/pinroll.config.php` را **ادغام** می‌کند:

```php
'build' => [
    'exclude' => ['docs', 'tests'],
    'include' => [],
],
```

لیست‌ها با هم جمع می‌شوند. env اختیاری: `PINROLL_BUILD_EXCLUDE` / `PINROLL_BUILD_INCLUDE` (جدا با ویرگول).

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

در `pinroll:deploy` چنداپ، پاک‌سازی retention فقط بعد از **آخرین** install اجرا می‌شود تا releaseهای staged هم‌بچ در میانه حذف نشوند.

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

## Vendor هاست

PinGate و نصب ریموت به یک `vendor/` کامل platform روی هاست نیاز دارند (شامل `pinoox/pinroll` و `pinoox/pincore`).

`pinroll:vendor` یک `pinroll/vendor.zip` **production** می‌سازد با همان پایپ‌لاین **PlatformComposer** که در `pinx:build platform` استفاده می‌شود:

- `require-dev` را حذف می‌کند (Pest، DevDB، Inspector، …)
- پکیج‌های production را نگه می‌دارد (از جمله `pinoox/pinroll` وقتی در `require` باشد)
- repositoryهای path در Composer را به فایل واقعی تبدیل می‌کند

```bash
# فقط ساخت zip
php pinoox pinroll:vendor

# ساخت، آپلود FTP، استخراج روی هاست با PinGate POST /vendor
php pinoox pinroll:vendor --push
```

| فلگ | اثر |
|-----|-----|
| (پیش‌فرض) | نوشتن `pinroll/vendor.zip` |
| `--push` | آپلود FTP + استخراج PinGate (هاست‌های FTP) |
| `--prune` | هرس اختیاری tests/docs داخل vendor |
| `-o` / `--output=` | مسیر zip سفارشی |

**جریان پیشنهادی اولین بار / به‌روزرسانی هسته**

```bash
php pinoox pinroll:gate -n          # آپلود PinGate (شامل مسیر /vendor)
php pinoox pinroll:vendor --push -n
php pinoox pinroll:check
```

`POST /vendor` در PinGate فقط `vendor.zip` کنار `pingate.php` را می‌پذیرد، فقط ورودی‌های زیر `vendor/` را استخراج می‌کند (امن در برابر zip-slip)، توکن‌های نامعتبر را rate-limit می‌کند و بعد از موفقیت zip را حذف می‌کند.

> ترجیحاً `pinroll:vendor --push` را به‌جای `pinroll:deploy --vendor` استفاده کنید. فلگ `--vendor` روی push/deploy درخت خام `vendor/` لوکال را با FTP همگام می‌کند و فقط وقتی هیچ اپی در همان اجرا دیپلوی نشود.

---

## فرانت‌اند اپ (theme dist)

دیپلوی اپ قبل از `pinx:build`، `fe:build` را اجرا می‌کند. پکیج‌های `.pinx` production شامل `dist/` تم هستند و `src/` / ابزار Vite تم را کنار می‌گذارند (حتی اگر `dist/` در gitignore باشد).

---

## جریان سریع (FTP + PinGate)

```bash
php pinoox pinroll:init
# پر کردن PINROLL_* در .env
php pinoox pinroll:connect
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:vendor --push   # vendor هاست (PlatformComposer + استخراج PinGate)
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
| `POST` | `/bootstrap` | استخراج `platform.zip` (نصب اول) |
| `POST` | `/setup` | اجرای SetupService نصب‌کننده (`db` + `user`) |
| `POST` | `/check-db` | تست اتصال دیتابیس **روی هاست** |
| `POST` | `/vendor` | استخراج امن `vendor.zip` آپلودشده |
| `POST` | `/rollback` | نصب مجدد نسخه قبلی |
| `POST` | `/cleanup` | هرس آرشیوهای قدیمی |
| `GET` | `/history` | تاریخچه rollout |

احراز هویت: `Authorization: Bearer {token}`.

---

## مرجع CLI

| دستور | کاربرد |
|-------|--------|
| `pinroll:init` | ساخت `.pinoox/pinroll.config.php` |
| `pinroll:provision` | نصب اولیه هاست خالی (PinGate + platform.zip + setup) |
| `pinroll:connect` | راه‌اندازی / بررسی (`--reset` برای تکرار) |
| `pinroll:apps` | تنظیم `hosts.*.apps` |
| `pinroll:vendor` | `vendor.zip` production (`--push` به هاست) |
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
| `--full` | zip پلتفرم + همه اپ‌های نصب‌شده |
| `--all` | اپ + vendor + theme |
| `--vendor` | همگام‌سازی FTP درخت `vendor/` (بدون اپ در همان اجرا) |
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

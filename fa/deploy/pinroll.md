# Pinroll — انتشار و دیپلوی

[← بازگشت به فهرست](../README.md)

> **تازه‌کار؟** [راهنمای سریع Pinroll](../start/pinroll-quickstart.md) — نصب، connect و deploy به زبان ساده.

Pinroll (`pinoox/pinroll`) پروژه پینوکس را به هاست می‌فرستد: نصب اول، به‌روزرسانی، migrate/patch و rollback.

آن را روی **ماشین توسعه** نصب کنید. هاست به Pinroll داخل `vendor/` نیاز ندارد.

```bash
composer require --dev pinoox/pinroll
php pinoox pinroll:init
# روش اتصال: kit / FTP / SSH — یا PINROLL_* در .env
```

بعد یکی از سناریوها را انتخاب کنید. مرجع کامل در [پیشرفته](#پیشرفته) است.

---

## سناریوها

### ۱. هاست خالی (نصب اول)

پوشه FTP/SFTP خالی — هنوز `index.php` نیست.

```bash
php pinoox pinroll:init
# PINROLL_HOST / USER / PASSWORD / URL
# PINROLL_DB_USERNAME / PINROLL_DB_PASSWORD (و نام دیتابیس اگر pinoox نیست)
php pinoox pinroll:provision
```

کارش: آپلود `pingate.php` → استخراج `platform.zip` → اجرای setup نصب‌کننده (دیتابیس + ادمین).

اگر ادمین را ندهید، پیش‌فرض این است:

| فیلد | پیش‌فرض |
|------|---------|
| نام | `support` |
| نام خانوادگی | `pinoox` |
| ایمیل | `info@pinoox.com` |
| نام کاربری | `admin` |
| رمز | `123456` |

در پروداکشن عوضش کنید (`PINROLL_ADMIN_*` یا `--admin-*`).

بعد از setup موفق (مثل نصب‌کننده وب):

- `/` → `com_pinoox_welcome`
- `/manager` → `com_pinoox_manager`
- `com_pinoox_installer` **غیرفعال** می‌شود

به‌روزرسانی بعدی `pinroll:deploy` است، نه دوباره provision.

اگر zip استخراج شد ولی setup شکست خورد:

```bash
php pinoox pinroll:provision --setup-only
```

`--force` روی `index.php` موجود extract می‌کند و setup را تکرار می‌کند. `--reupload` دوباره `platform.zip` می‌سازد.

میانبر Pinx: `pinx provision`.

### ۲. سایت موجود

سایت از قبل بالا است. یک‌بار PinGate را آماده کنید، بعد دیپلوی.

#### روش‌های اتصال

| روش | کی | دستور |
|-----|-----|--------|
| **Zip kit** | بدون FTP — فقط File Manager | `php pinoox pinroll:kit` |
| **FTP** | هاست اشتراکی | `php pinoox pinroll:connect --via=ftp` |
| **SSH** | VPS | `php pinoox pinroll:connect --via=ssh` |
| **FTP یک‌بار → Pinion** | اول gate با FTP، بعد آپلود HTTP | `php pinoox pinroll:connect --bootstrap-ftp` |
| **انتخاب تعاملی** | نمی‌دانید کدام | `php pinoox pinroll:connect` |

**Zip kit (بدون FTP):**

```bash
php pinoox pinroll:kit
# → storage/pinroll/pinroll-kit-production.zip
# داخل public_html استخراج کنید (pingate.php + storage/pinroll/tokens/…)
php pinoox pinroll:check
php pinoox pinroll:deploy
```

`pinroll:gate --kit` همان zip را می‌سازد. بعد از kit، `via` معمولاً `pinion` است.

**با connect (FTP/SSH یا picker):**

```bash
php pinoox pinroll:connect
php pinoox pinroll:apps
php pinoox pinroll:check
php pinoox pinroll:deploy
```

`pinroll:connect` مسیر دیپلوی + origin سایت را می‌پرسد، PinGate را آماده می‌کند (آپلود یا kit) و **site + token** را در `.pinoox/pinroll.config.php` می‌نویسد. اگر هاست از قبل تنظیم شده باشد فقط اتصال را بررسی می‌کند (`--reset` برای تکرار).

**مراحل `pinroll:deploy` (با نصب روی هاست):**

1. Build — ساخت `.pinx`
2. Connect — ترنسپورت هاست (`ftp` / `ssh` / `pinion`)
3. **Ensure PinGate** — سلامت `pingate.php`؛ در صورت خرابی خودکار آپلود
4. **Cleanup leftovers** — هرس آرشیو/tmp/zip قدیمی یا ناقص
5. Upload `.pinx`
6. Install via PinGate

فقط آپلود: `pinroll:push`. فقط نصب فایل آماده: `pinroll:install`.

### ۳. به‌روزرسانی پلتفرم + همه اپ‌ها

```bash
php pinoox pinroll:deploy --full
```

zip پلتفرم (`pinx:update` روی هاست) و همه اپ‌های کشف‌شده/نصب‌شده. `apps[]` هاست نادیده گرفته می‌شود مگر `--app` / `--apps` بدهید.

### ۴. به‌روزرسانی یک اپ

```bash
php pinoox pinroll:deploy --app=com_pinoox_shop
php pinoox pinroll:push --app=com_pinoox_shop     # فقط آپلود
php pinoox pinroll:install --app=com_pinoox_shop  # نصب staged
```

### ۵. بعد از رسیدن فایل‌ها — migrate، patch، seed

روی هاست (SSH به ریشه سایت) یا روی همین ماشین:

```bash
php pinoox pinroll:setup                 # migrate + patch (پلتفرم، بعد اپ‌ها)
php pinoox pinroll:setup --dry-run
php pinoox pinroll:setup --migrate --patch --seed
php pinoox pinroll:setup --app=com_pinoox_shop --migrate
```

این همان `POST ?route=setup` در PinGate نیست (آن نصب اولیه است). `pinx setup` هم فرق دارد (وابستگی لوکال تک‌اپ).

### ۶. Rollback

```bash
php pinoox pinroll:rollback
php pinoox pinroll:rollback --deploy-id=20260710_091021_3f980930
```

فایل‌های **پکیج قبلی** را برمی‌گرداند. migration دیتابیس را خودکار برنمی‌گرداند.

### ۷. پروژه تک‌اپ (Pinx) — فقط پکیج

راهنمای اختصاصی: [دیپلوی اپ Pinx](./pinx.md).

از یک پروژه Pinx (`app.php` در ریشه)، **دیپلوی پیش‌فرض فقط `.pinx` همین اپ را می‌فرستد**. درخت پروژه، `vendor/` یا zip پلتفرم آپلود نمی‌شود.

```bash
composer require --dev pinoox/pinroll
pinx pinroll:init
pinx connect --via=ftp          # یا: pinx kit
pinx deploy                     # fe:build + pinx:build → آپلود .pinx → pinx:install
```

`pinx deploy` مقدار `--app=` را از پکیج همین پروژه می‌گذارد. `apps[]` هاست نادیده گرفته می‌شود تا لیست چنداپه باقی‌مانده رلیز را عوض نکند.

| دستور | چه چیزی می‌رود |
|-------|----------------|
| `pinx deploy` | فقط `.pinx` همین پکیج (نصب یا آپدیت روی هاست) |
| `pinx deploy --full` | zip پلتفرم **به‌علاوه** اپ‌ها — فقط وقتی هسته هاست را هم می‌خواهید آپدیت کنید |
| `pinx provision` | هاست خالی (یک‌بار): platform.zip + نصب‌کننده، نه آپدیت اپ |

هاست باید از قبل پلتفرم پینوکس باشد (`pinx provision` یا نصب موجود). `.pinx` در `apps/{package}/` می‌نشیند.

---

## پیشرفته

معماری، کانفیگ، PinGate، retention و همه فلگ‌ها.

### چیست؟

Pinroll یک **کتابخانه Composer** است، نه یک اپ پینوکس. دستورات با نصب پکیج ثبت می‌شوند.

| مفهوم | معنی |
|-------|------|
| **Host** | مقصد دیپلوی (`production`، `staging` — کلید آرایه همان نام است) |
| **Transport (`via`)** | نحوه ارسال (`ftp`، `ssh`، `pinion`، `local`) — kit برای راه‌اندازی بدون FTP |
| **PinGate** | یک فایل عمومی روی هاست (`pingate.php?route=`) برای install / status / rollback / vendor / sync / نصب اولیه |
| **Bundle** | دستور ساخت اختیاری؛ دیپلوی عادی اپ‌ها را خودکار تشخیص می‌دهد |

```mermaid
flowchart LR
    subgraph dev [ماشین توسعه]
        CLI["php pinoox pinroll:*"]
    end
    subgraph transport [ترنسپورت]
        FTP[FTP]
        SSH[SSH]
        Pinion[Pinion]
    end
    subgraph remote [هاست]
        Gate[pingate.php]
    end
    CLI --> transport --> Gate
```

| لایه | مسیر |
|------|------|
| موتور | `pinoox/pinroll` |
| کانفیگ canonical | `vendor/pinoox/pinroll/config/pinroll.php` (اسکیمای کامل) |
| Overlay پروژه | `.pinoox/pinroll.config.php` (همراه `.pinoox/` در gitignore) — هر override از جمله اسرار |
| PinGate | `{deploy_path}/pingate.php` |
| Runtime | `storage/pinroll/` |
| بیلد لوکال | `apps/{package}/pinx/export/` |

هاست به Pinroll داخل `vendor/` نیاز ندارد. `pingate.php` با pincore (`pinx:install` / `pinx:update`) نصب می‌کند. Pinroll را فقط وقتی در `require` بگذارید که بخواهید PinGate روی سرور از کلاس‌های Pinroll استفاده کند.

### پیکربندی

**یک کانفیگ کامل** وجود دارد: `vendor/pinoox/pinroll/config/pinroll.php` داخل کتابخانه Pinroll (پیش‌فرض‌های سراسری، provision، build و `hosts.production`).

فایل پروژه یک **overlay اختیاری** است، نه کپی تولیدشده:

| فایل | Git | نقش |
|------|-----|------|
| `config/pinroll.php` کتابخانه | داخل پکیج | اسکیمای canonical |
| `.pinoox/pinroll.config.php` | نادیده (کل `.pinoox/`) | هر override از جمله `gate.site`، `gate.token`، رمز FTP |
| `.env` `PINROLL_*` | نادیده | overlay اختیاری CI (آخرین لایه برنده است) |

```bash
php pinoox pinroll:init
php pinoox pinroll:config    # هاست resolveشده (token سانسور)
```

`pinroll:init` یک **استاب کوتاه با کامنت نمونه** می‌نویسد. پیش‌فرض کتابخانه برای اجرا کافی است؛ هر نمونه را uncomment کنید تا override شود. `pinroll:connect` بعد site، token و رمز FTP را پچ می‌کند.

```php
<?php

/**
 * Overlay پینرول — همراه .pinoox/ در gitignore
 * اسکیمای canonical: vendor/pinoox/pinroll/config/pinroll.php
 * نمونه‌های کامنت‌شده را uncomment کنید تا پیش‌فرض کتابخانه عوض شود.
 */
return [
    'default_host' => 'production',

    // Optional global overrides
    // 'keep' => 3,                     // تعداد آرشیوهای جدید؛ 0 = بدون هرس
    // 'store' => 'remote',             // local | remote | both
    // 'auto_clean' => true,            // بعد از install موفق هرس شود
    // 'clean_before_deploy' => true,   // قبل از هر آپلود باقی‌مانده‌ها پاک شود
    // 'stale_days' => 7,               // آرشیو قدیمی‌تر از N روز هم حذف؛ 0 = فقط keep
    // 'lang' => 'fa',                  // زبان نصب‌کننده / provision
    // 'gate_embed_token' => false,     // false = توکن روی هاست، نه داخل pingate.php
    // 'chunk_size' => 5 * 1024 * 1024, // اندازه تکه آپلود Pinion (بایت)

    // نصب اول هاست خالی (pinroll:provision) — همان فیلدهای نصب‌کننده وب
    // 'provision' => [
    //     'db' => [
    //         'host' => 'localhost',
    //         'database' => 'pinoox',
    //         'username' => '',
    //         'password' => '',
    //         'connection' => 'mysql',
    //         'port' => '3306',
    //         'prefix' => 'pin_',
    //         'timezone' => '+03:30',
    //     ],
    //     'user' => [
    //         'fname' => 'support',
    //         'lname' => 'pinoox',
    //         'email' => 'info@pinoox.com',
    //         'username' => 'admin',
    //         'password' => '123456',
    //     ],
    // ],

    // قوانین اضافه zip پلتفرم (با platform/build.config.php ادغام می‌شود)
    // 'build' => [
    //     'exclude' => ['docs', 'tests'],
    //     'include' => [],
    // ],

    'hosts' => [
        'production' => [
            'deploy_path' => 'public_html',  // پوشه FTP/SSH در ریشه اکانت
            // 'web_path' => '',             // زیرمسیر URL (مثلاً shop)؛ '' = ریشه دامنه/ساب‌دامین
            'via' => 'ftp',                  // ftp | ssh | pinion | local
            // 'apps' => ['com_pinoox_account'],
            'gate' => [
                'site' => 'https://pinoox.com',  // فقط origin
                'token' => 'shared-host-token',
            ],
            // 'ftp' => [
            //     'host' => '',
            //     'user' => '',
            //     'password' => '',
            // ],
            // 'ssh' => [
            //     'host' => '',
            //     'user' => '',
            //     'key' => '',
            // ],
            // 'hooks' => [
            //     'before_push' => ['npm run build'],
            //     'after_install' => ['php pinoox cache:build'],
            // ],
        ],
    ],
];
```

**origin سایت** را ذخیره کنید (`https://pinoox.com` یا `https://pinoox.com/shop`)، نه `…/pingate.php?route=`. Pinroll در runtime پسوند را اضافه می‌کند. URL کامل قدیمی هنوز کار می‌کند.

#### توکن مشترک هاست

PinGate فقط **یک hash** در `pingate.php` نگه می‌دارد. آخرین `connect` / `gate --rotate` که فایل را آپلود کند برنده است؛ بقیه 401 می‌گیرند.

- **یک توکن برای هر هاست**، مثل رمز FTP (1Password / کپی هم‌تیمی / secret در CI).
- نفر اول: `pinroll:connect` → آپلود `pingate.php` + نوشتن توکن در overlay خودش.
- بقیه: **همان توکن** را در overlay (یا `.env`) کپی کنند. `--rotate` نزنید مگر بخواهید بقیه را از کار بیندازید.

`pinroll:connect` / `pinroll:gate` مقدار site، token و رمز FTP را در overlay می‌نویسند — نه در `.env`. `.env` `PINROLL_*` برای CI همچنان کار می‌کند.

**کلیدهای سراسری** (ریشه overlay؛ به همه هاست‌ها ارث می‌رسد مگر هاست override کند):

| کلید | پیش‌فرض | توضیح |
|------|---------|--------|
| `default_host` | `production` | هاست وقتی نام در CLI نیست |
| `keep` | `3` | N تا آرشیو جدید؛ `0` یعنی بدون هرس شمارشی |
| `store` | `remote` | آرشیو بعد از install کجا بماند: `local` \| `remote` \| `both` |
| `auto_clean` | `true` | بعد از install موفق، قدیمی‌تر از `keep` پاک شود |
| `clean_before_deploy` | `true` | قبل از هر upload/deploy باقی‌مانده‌ها پاک شود |
| `stale_days` | `7` | آرشیو/zip قدیمی‌تر از N روز هم حذف شود؛ `0` = فقط keep |
| `lang` | `en` | زبان نصب‌کننده / provision (`en`، `fa`، …) |
| `gate_embed_token` | `false` | `false`: توکن در `storage/pinroll/tokens/{label}.php` روی هاست است، نه داخل `pingate.php` |
| `chunk_size` | `5 * 1024 * 1024` | اندازه تکه آپلود HTTP در Pinion (بایت) |
| `lock_timeout` | `3600` | ثانیه تا lock دیپلوی کهنه نادیده گرفته شود |
| `gate_path` | `_pinoox/gate` | پیشوند داخلی مسیر PinGate — پیش‌فرض را عوض نکنید (ورودی عمومی `pingate.php?route=` است) |
| `default_transport` | `pinion` | `via` پیش‌فرض اگر هاست نداشته باشد: `ftp` \| `ssh` \| `pinion` \| `local` |
| `provision` | پایین | دیتابیس + ادمین نصب اول (`pinroll:provision`) |
| `build` | `exclude` / `include` `[]` | قوانین اضافه zip پلتفرم، ادغام با `platform/build.config.php` |

**کلیدهای هاست** (`hosts.{name}`):

| کلید | توضیح |
|------|--------|
| `deploy_path` | پوشه FTP/SSH در ریشه اکانت (`public_html`، `apps`، …) |
| `web_path` | زیرمسیر URL (`shop`)؛ `''` = ریشه دامنه یا ساب‌دامین. اگر نباشد از `deploy_path` با حذف `public_html` / `www` به‌دست می‌آید |
| `hostname` | آدرس اتصال اگر با `ftp.host` / `ssh.host` فرق دارد |
| `via` | `ftp`، `ssh`، `pinion` یا `local` |
| `gate.site` / `gate.token` | origin سایت + توکن مشترک PinGate |
| `ftp` / `ssh` | اطلاعات اتصال (`ssh`: `host`، `user`، `key`) |
| `apps` | پکیج‌های پیش‌فرض push/install |
| `hooks` | دستورات شل اطراف push / install / rollback |

`provision.db`: `host`، `database`، `username`، `password`، `connection`، `port`، `prefix`، `timezone`.  
`provision.user`: `fname`، `lname`، `email`، `username`، `password` — همان فیلدهای نصب‌کننده وب.

برای production کلیدهای **بدون پیشوند هاست** هم خوانده می‌شوند (`PINROLL_VIA`، `PINROLL_DB_HOST`، `PINROLL_SITE`، …). بقیه هاست‌ها: `PINROLL_{HOST}_*`.

```env
PINROLL_VIA=ftp
PINROLL_PATH=public_html
PINROLL_WEB_PATH=
PINROLL_KEEP=3
PINROLL_STORE=remote
PINROLL_AUTO_CLEAN=true
PINROLL_CLEAN_BEFORE_DEPLOY=true
PINROLL_STALE_DAYS=7
PINROLL_SITE=https://example.com
PINROLL_TOKEN=…
PINROLL_HOST=ftp.example.com
PINROLL_USER=…
PINROLL_PASSWORD=…

PINROLL_LANG=fa
PINROLL_DB_HOST=localhost
PINROLL_DB_DATABASE=pinoox
PINROLL_DB_USERNAME=…
PINROLL_DB_PASSWORD=…
PINROLL_DB_CONNECTION=mysql
PINROLL_DB_PORT=3306
PINROLL_DB_PREFIX=pin_
PINROLL_DB_TIMEZONE=+03:30
PINROLL_ADMIN_FNAME=support
PINROLL_ADMIN_LNAME=pinoox
PINROLL_ADMIN_EMAIL=info@pinoox.com
PINROLL_ADMIN_USERNAME=admin
PINROLL_ADMIN_PASSWORD=123456

PINROLL_BUILD_EXCLUDE=docs,tests
PINROLL_BUILD_INCLUDE=
```

ترتیب بارگذاری: **canonical کتابخانه → overlay پروژه → `PINROLL_*`**. کلیدهای تو در توی هاست هم merge می‌شوند: فقط گذاشتن `hosts.production.gate.site` مقدار `via` / `ftp` کتابخانه را پاک نمی‌کند.

ترتیب ادغام provision: **فلگ CLI → `.env` → `provision` هاست → `provision` سراسری → پیش‌فرض**. مقدار خالی پیش‌فرض را پاک نمی‌کند.

#### `deploy_path` و URL سایت

`deploy_path` پوشه FTP در ریشه اکانت است. URL سایت **همان‌طور که وارد شده** برای PinGate استفاده می‌شود — مسیر و URL قاطی نمی‌شوند.

| پوشه FTP | URL سایت | Gate URL |
|----------|----------|----------|
| `apps` | `https://apps.example.com` | `https://apps.example.com/pingate.php?route=` |
| `public_html` | `https://example.com` | `https://example.com/pingate.php?route=` |
| `public_html/shop` | `https://example.com/shop` | `https://example.com/shop/pingate.php?route=` |

مسیریابی فقط `?route=` است. از PATH_INFO (`pingate.php/push/…`) استفاده نکنید.

### جزئیات provision هاست خالی

یک‌بار روی پوشه **خالی**. PHP هاست باید `ZipArchive` و زمان/حافظه کافی داشته باشد (تا ۱۰ دقیقه). دیتابیس باید از **همان هاست** قابل اتصال باشد.

```mermaid
flowchart TD
  Dev[pinroll:provision] -->|1 pingate.php| Gate[pingate.php]
  Dev -->|2 platform.zip| Gate
  Gate -->|"POST ?route=bootstrap"| Files["index.php vendor/ apps/"]
  Dev -->|"POST ?route=setup"| Setup[SetupService]
  Setup --> Done["welcome + manager / نصب‌کننده خاموش"]
```

روش‌های دادن اطلاعات:

```bash
# فقط .env
php pinoox pinroll:provision --no-interaction

# ویزارد (دیتابیس پرسیده می‌شود؛ ادمین اگر خالی باشد پیش‌فرض است)
php pinoox pinroll:provision

# فلگ CLI
php pinoox pinroll:provision production \
  --db-host=localhost --db-database=pinoox --db-username=root --db-password=secret \
  --admin-username=admin --admin-password=secret1 --lang=fa
```

روتر بعد از نصب از `apps/com_pinoox_installer/config/app.config.php` می‌آید.

### `--full` در برابر `--all`

| فلگ | معنی |
|-----|------|
| (پیش‌فرض) | فقط `.pinx` اپ |
| `--full` | zip پلتفرم + **همه** اپ‌های نصب‌شده/کشف‌شده |
| `--all` | اپ + vendor + theme |
| `--vendor` | همگام FTP درخت خام `vendor/` — بهتر است `pinroll:vendor --push` |
| `--theme` | بیلد تم (`fe:build`) و بعد dist |

```bash
php pinoox pinroll:deploy --full
php pinoox pinroll:push --full
pinx deploy --full
```

`pinx:build platform` فایل `platform/build.config.php` را می‌خواند و بعد `build` داخل `.pinoox/pinroll.config.php` را **ادغام** می‌کند (لیست‌ها جمع می‌شوند). env اختیاری: `PINROLL_BUILD_EXCLUDE` / `PINROLL_BUILD_INCLUDE`.

### جزئیات `pinroll:setup`

پیش‌فرض (بدون فلگ مرحله): **migrate + patch** برای `platform` و بعد اپ‌ها. اگر فلگ مرحله بدهید **فقط همان‌ها** اجرا می‌شوند. ترتیب: `config` → `migrate` → `seed` → `patch`.

| فلگ | اثر |
|-----|-----|
| (پیش‌فرض) | migrate + patch |
| `--migrate` | migration دیتابیس |
| `--patch` | پچ داده |
| `--seed` | seeder (در پیش‌فرض نیست) |
| `--config` | بازنویسی `pinroll.config.php` قدیمی (`targets` → `hosts`) |
| `--dry-run` | پیش‌نمایش بدون اجرا (`seed` رد می‌شود) |
| `--skip-platform` | فقط اپ‌ها |
| `--force` | ادامه بعد از خطا / بازنویسی کانفیگ |
| `--app=` / `--apps=` | انتخاب پکیج |
| `--class=` | کلاس مشخص seeder یا patch |

نام‌های قدیمی: `pinroll:migrate-config` → `--config`؛ `pinroll:migrate:dry-run` → `--migrate --dry-run`.

### انتخاب اپ‌ها

**تک‌اپ Pinx** (`app.php` در ریشه): دیپلوی همان پکیج را خودکار می‌گیرد. `apps[]` هاست نادیده است مگر `--app` / `--apps` بدهید.

روی پلتفرم چنداپه، اگر `hosts.*.apps` خالی باشد و `--app` / `--apps` ندهید، push/deploy تعاملی می‌پرسد.

```bash
php pinoox pinroll:apps
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:apps --all
php pinoox pinroll:apps --list
php pinoox pinroll:apps --clear
```

### Connect و kit

```bash
php pinoox pinroll:kit                    # zip برای File Manager
php pinoox pinroll:connect                # منوی روش‌ها
php pinoox pinroll:connect --via=pinion
php pinoox pinroll:connect --via=ftp
php pinoox pinroll:connect --via=ssh
php pinoox pinroll:connect --bootstrap-ftp
php pinoox pinroll:connect --reset
php pinoox pinroll:gate --kit             # همان zip kit
php pinoox pinroll:config
```

`gate.site` (origin) و token را در overlay می‌نویسد. `--rotate` روی `pinroll:gate` hash جدید می‌سازد و **هم‌تیمی‌ها را از کار می‌اندازد**.

### Sync پوشه و pincore

`pinroll:sync` و `pinroll:pincore` پوشه را **zip** می‌کنند، با `via` هاست آپلود می‌کنند و روی سرور با PinGate (`POST ?route=sync`) استخراج می‌کنند — برای `ftp`، `ssh` و `pinion`.

```bash
php pinoox pinroll:pincore
php pinoox pinroll:sync --from=./pincore --to=vendor/pinoox/pincore
php pinoox pinroll:sync --from=./path --to=remote/path --via=pinion
```

هاست باید `pingate.php` به‌روز داشته باشد (با `route=sync`). اگر قدیمی است: `php pinoox pinroll:gate`.

### حالت‌های local

**`via: local`** — آرشیو در `storage/pinroll/incoming/` همین ماشین:

```bash
php pinoox pinroll:push --via=local --app=com_pinoox_shop
```

**`pinroll:install --local`** — بعد از SSH به پروداکشن (ریشه سایت):

```bash
php pinoox pinroll:install --local
php pinoox pinroll:install --local --list
```

### Retention

| کلید | مقادیر | رفتار |
|------|--------|--------|
| `keep` | `0`…`N` | N تا جدیدترین؛ `0` یعنی بدون هرس |
| `store` | `local` \| `remote` \| `both` | کدام طرف آرشیو نگه دارد |
| `auto_clean` | bool | بعد از install موفق، قدیمی‌تر از `keep` پاک شود |
| `clean_before_deploy` | bool | قبل از upload/deploy باقی‌مانده‌ها پاک شود (پیش‌فرض `true`) |
| `stale_days` | int | آرشیو/zip قدیمی‌تر از N روز هم حذف شود (پیش‌فرض `7`؛ `0` = فقط keep) |

| `store` | آرشیو کجا | بعد از install |
|---------|-----------|----------------|
| `remote` (پیش‌فرض) | هاست `storage/pinroll/incoming/` | تا `keep` هرس |
| `local` | incoming + pinx export توسعه | فقط لوکال |
| `both` | توسعه **و** هاست | هر دو |

در دیپلوی چنداپ، پاک‌سازی فقط بعد از **آخرین** install اجرا می‌شود.

```bash
php pinoox pinroll:cleanup
php pinoox pinroll:cleanup --local
php pinoox pinroll:cleanup --dry-run
php pinoox pinroll:cleanup -k=2
```

### Hooks

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

### Rollback و migration

`pinroll:rollback` پکیج **قبلی** را با force نصب می‌کند. همه migrationها و patchها را برنمی‌گرداند.

| لایه | در rollback |
|------|-------------|
| فایل‌های اپ / پکیج Pinx | از آرشیو قبلی |
| Migration با `down()` | فقط اگر خودتان rollback بزنید (مثلاً hook) |
| Patch یک‌طرفه | برنمی‌گردد |

برای اسکیما ترجیحاً forward-fix بفرستید. `keep >= 2` و ترجیحاً `store: both`. قبل از دیپلوی پرریسک بکاپ دیتابیس بگیرید.

```bash
php pinoox pinroll:setup --dry-run
```

### Vendor هاست

PinGate به `vendor/` کامل platform روی هاست نیاز دارد (pincore + Pinion). Pinroll می‌تواند `require-dev` روی ماشین توسعه بماند.

`pinroll:vendor` یک `pinroll/vendor.zip` production می‌سازد (همان PlatformComposer): `require-dev` حذف، پکیج‌های production نگه، path repo به فایل واقعی.

```bash
php pinoox pinroll:vendor
php pinoox pinroll:vendor --push
```

| فلگ | اثر |
|-----|-----|
| (پیش‌فرض) | نوشتن `pinroll/vendor.zip` |
| `--push` | آپلود FTP + استخراج PinGate |
| `--prune` | هرس tests/docs داخل vendor |
| `-o` / `--output=` | مسیر zip سفارشی |

```bash
php pinoox pinroll:gate -n
php pinoox pinroll:vendor --push -n
php pinoox pinroll:check
```

`POST /vendor` فقط `vendor.zip` کنار `pingate.php` را می‌پذیرد و فقط ورودی‌های `vendor/` را استخراج می‌کند.

ترجیحاً `pinroll:vendor --push` به‌جای `pinroll:deploy --vendor`.

### فرانت‌اند اپ (theme dist)

دیپلوی اپ قبل از `pinx:build`، `fe:build` را اجرا می‌کند. `.pinx` production شامل `dist/` تم است و `src/` / ابزار Vite را کنار می‌گذارد.

### مسیرهای PinGate

احراز هویت: `Authorization: Bearer {token}`. مسیرها: `pingate.php?route=…`.

**امنیت:** بدون توکن، همه routeها `401 JSON` برمی‌گردانند. `/status` سبک است (boot سنگین پلتفرم نمی‌زند).

| متد | مسیر | کاربرد |
|-----|------|--------|
| `GET` | `/status` | سلامت / نسخه |
| `GET` | `/incoming` | لیست releaseهای staged |
| `POST` | `/install` | نصب (`/apply` سازگاری) |
| `POST` | `/bootstrap` | استخراج `platform.zip` (نصب اول) |
| `POST` | `/setup` | SetupService نصب‌کننده سپس welcome/manager و غیرفعال کردن installer |
| `POST` | `/check-db` | تست دیتابیس **روی هاست** |
| `POST` | `/vendor` | استخراج `vendor.zip` |
| `POST` | `/sync` | استخراج zip sync مسیر (بدون boot کامل پلتفرم؛ امن برای جایگزینی pincore) |
| `POST` | `/rollback` | نصب مجدد نسخه قبلی |
| `POST` | `/cleanup` | هرس آرشیو |
| `GET` | `/history` | تاریخچه |

`/sync` زود در bootstrap PinGate اجرا می‌شود تا بتوان `vendor/pinoox/pincore` را جایگزین کرد بدون اینکه هستهٔ در حال اجرا قفل شود.

### مرجع CLI

| دستور | کاربرد |
|-------|--------|
| `pinroll:init` | استاب کوتاه overlay در `.pinoox/pinroll.config.php` |
| `pinroll:kit` | zip استخراج برای File Manager (`pingate` + token + README) |
| `pinroll:provision` | نصب اولیه هاست خالی |
| `pinroll:connect` | راه‌اندازی / بررسی (`--via=`، `--bootstrap-ftp`، `--reset`)؛ نوشتن site + token |
| `pinroll:config` | چاپ هاست resolveشده (token سانسور) |
| `pinroll:apps` | تنظیم `hosts.*.apps` |
| `pinroll:vendor` | `vendor.zip` production (`--push`) |
| `pinroll:pincore` | zip + آپلود `vendor/pinoox/pincore` + استخراج PinGate (`ftp`/`ssh`/`pinion`) |
| `pinroll:sync` | zip هر پوشه لوکال (`--from`, `--to`) + آپلود + استخراج PinGate |
| `pinroll:gate` | ساخت / آپلود PinGate (`--kit` برای zip) |
| `pinroll:check` | بررسی هاست / PinGate |
| `pinroll:push` | فقط ساخت و آپلود |
| `pinroll:setup` | بعد از دیپلوی: migrate + patch |
| `pinroll:install` | نصب release آماده |
| `pinroll:deploy` | push + install |
| `pinroll:rollback` | rollback |
| `pinroll:cleanup` | هرس (`--local`، `--dry-run`، `-k`) |
| `pinroll:build` | فقط build |
| `pinroll:status` | وضعیت rollout |
| `pinroll:history` | تاریخچه |
| `pinroll:pull` | دریافت manifest از release server |

فلگ‌های push / deploy: `--full`، `--all`، `--vendor`، `--theme`، `--app=` / `--apps=`، `--via=`، `--host=`.

### ترنسپورت‌ها

| `via` | کاربرد |
|-------|--------|
| `ftp` | هاست اشتراکی — آپلود + نصب PinGate |
| `ssh` | VPS — SFTP + نصب SSH |
| `pinion` | آپلود تکه‌ای HTTP از PinGate (بعد از kit یا bootstrap-ftp) |
| `local` | همان ماشین / تست |

راه‌اندازی بدون FTP: `pinroll:kit` → extract در `public_html` → بعد `via=pinion`.

### عیب‌یابی

| علامت | احتمال / کار |
|-------|----------------|
| `401` / Missing bearer token | توکن overlay با hash داخل `pingate.php` یکی نیست — از هم‌تیمی بپرسید یا `pinroll:connect` / `kit` / `pinroll:gate` |
| `503` یا HTML به‌جای JSON | هاست overload یا `pingate.php` قدیمی/خراب — `pinroll:gate` یا deploy جدید (مرحله Ensure PinGate) |
| PinGate request failed (HTTPS) | روی ویندوز/MAMP: Pinroll 1.5.2+ CA واقعی استفاده می‌کند؛ `pinroll:check` دوباره بزنید |
| Cannot redeclare `pinroll_pingate_run` | `pingate.php` روی هاست خراب است — `php pinoox pinroll:gate` |
| `Action "…" is already registered` | pingate را به‌روز کنید؛ نصب با skip_cache و rebuild کش داخل‌پردازشی |
| `route=sync` موجود نیست / sync شکست | `pingate.php` قدیمی است — `php pinoox pinroll:gate` یا دوباره kit |
| Package install failed | لاگ PinGate: `storage/pinroll/gate/YYYYMMDD.log` روی ماشین توسعه |
| cleanup بعد از install warning | معمولاً روی نصب اثر ندارد؛ `/cleanup` سبک‌تر در نسخه‌های بعد |

```bash
php pinoox pinroll:check
php pinoox pinroll:gate -n
php pinoox pinroll:config
```

---

## مستندات مرتبط

- [راهنمای سریع Pinroll](../start/pinroll-quickstart.md)
- [دیپلوی اپ Pinx](./pinx.md)
- [مروری بر Pinroll](../advanced/pinroll.md)
- [پروتکل Pinion](../advanced/pinion.md)
- [Pinx CLI](../start/pinx-cli.md)
- [مرجع CLI](../start/cli-reference.md)

---

[← بازگشت به فهرست](../README.md)

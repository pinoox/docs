# Pinx CLI (پروژه‌های تک‌اپ)

[← بازگشت به فهرست](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** ابزار خط فرمان توسعه‌دهنده برای پروژه‌های **تک‌اپ (single-app)** پینوکس است — ساخت اسکلت، اجرا، مایگریشن، بیلد و انتشار پکیج‌های `.pinx` بدون نیاز به منیجر چنداپه.

این ابزار روی `pinoox/pincore` و قالب `pinoox/app` ساخته شده است. ریشه پروژه شما **خودِ اپ** است: یک `app.php`، یک پکیج، یک گردش کار.

> برای نصب کلاسیک پلتفرم چنداپه، از [`php pinoox`](./cli-reference.md) استفاده کنید.

---

## شروع سریع

یک بار Pinx را نصب کنید، اپ جدید بسازید و اجرا کنید:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # پیشنهاد com_my_shop — در ویزارد تایید یا ویرایش کنید
cd my-shop
cp .env.example .env          # اگر دیتابیس دارید DB_* را تنظیم کنید
pinx setup                    # مایگریشن platform + اپ، اجرای seeder ها
pinx dev                      # http://127.0.0.1:8000
```

اگر دستور `pinx` پیدا نشد، مسیر `bin` گلوبال Composer را به `PATH` اضافه کنید:

- لینوکس / مک: `~/.composer/vendor/bin` یا `~/.config/composer/vendor/bin`
- ویندوز: `%APPDATA%\Composer\vendor\bin`

| مرحله | کاری که انجام می‌دهد |
|-------|----------------------|
| `composer global require` | دستور `pinx` را روی سیستم شما نصب می‌کند |
| `pinx new my-shop` | اسکلت از `pinoox/app`؛ ویزارد یک پکیج سه‌بخشی پیشنهاد می‌دهد (مثل `com_my_shop`) |
| `.env` | دیتابیس و مسیرهای پروژه — از `.env.example` کپی کنید |
| `pinx setup` | یک‌جا: مایگریشن platform ← مایگریشن اپ ← seeder ها |
| `pinx dev` | PHP serve + Vite HMR وقتی استک فرانت‌اند تنظیم باشد؛ **URL PHP** چاپ‌شده در ترمینال را باز کنید |

نام پکیج‌ها از الگوی `com_{vendor}_{name}` پیروی می‌کند — مثلاً `com_acme_shop`، `ir_yekdo_app`. اگر داخل یک پوشه خالی هستید، به‌جای `pinx new` از `pinx init` استفاده کنید.

**بررسی اختیاری قبل از `setup`:** دستور `pinx doctor` وضعیت PHP، ساختار، env، دیتابیس و آمادگی build را گزارش می‌دهد.

---

## روش جایگزین: `composer create-project`

بدون نصب گلوبال — قالب، `bin/pinx` را داخل خود پروژه دارد:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## تفاوت تک‌اپ با حالت کلاسیک

نصب کلاسیک پینوکس چند اپ را زیر `apps/` نگه می‌دارد و در runtime یکی را انتخاب می‌کند. حالت **تک‌اپ** این ساختار را مسطح می‌کند:

- `app.php` در ریشه پروژه هویت پکیج و تنظیمات pinx را نگه می‌دارد
- `Controller/`، `Model/`، `routes/` و `theme/` در ریشه هستند — نه داخل `apps/{package}/`
- `platform/` تنظیمات روتینگ لوکال و launcher را نگه می‌دارد (از build های `.pinx` حذف می‌شود)
- Pinx همیشه **اپ خود شما** را هدف می‌گیرد — بدون انتخاب‌گر پکیج و بدون UI منیجر

```
my-shop/                    ← ریشه پروژه = ریشه اپ
├── app.php                 ← package، version، pinx.sign، frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← لایه dev host و deploy (فقط لوکال)
├── bin/pinx                ← ورودی CLI داخل پروژه
└── vendor/pinoox/pincore   ← فریمورک
```

---

## روش‌های نصب

| محل | روش | چه زمانی |
|-----|-----|----------|
| **گلوبال** | `composer global require pinoox/pinx-cli` | پیشنهادی — `pinx new` و `pinx init` از هر جا |
| **داخل پروژه** | به‌صورت `bin/pinx` در `pinoox/app` | بعد از `composer create-project` — بدون نیاز به نصب گلوبال |

```bash
pinx -v          # نسخه CLI (مثلاً pinx-cli 1.1.7)
pinx list        # فهرست گروه‌بندی‌شده دستورها
pinx help setup  # جزئیات یک دستور
```

---

## گردش کار روزانه

```bash
pinx dev                    # PHP + Vite HMR (فوروارد به pincore dev)
pinx dev --network          # bind PHP + Vite روی LAN
pinx dev --no-frontend      # فقط PHP serve — دارایی manifest (بدون HMR)
pinx dev --open             # باز کردن مرورگر (فقط با --no-frontend)

pinx migrate                # اجرای مایگریشن‌های اپ (--platform اول platform را اجرا می‌کند)
pinx migrate:st             # وضعیت مایگریشن
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # لیست اکشن‌های نام‌دار (--validate, --json)
pinx test                   # اجرای تست‌های اپ (Pest)
```

**فرانت‌اند** (وقتی `theme/` از Vue/React + Vite استفاده می‌کند):

```bash
pinx fe:info                # استک، اسکریپت‌های npm، مسیرها
pinx fe:i                   # npm install
pinx fe:d                   # PHP + Vite HMR (همان pinx dev با استک فرانت‌اند)
pinx fe:w                   # watch — rebuild دارایی production با ذخیره
pinx fe:b                   # بیلد production
pinx fe:sc --stack=vue      # ساخت فایل‌های شروع
```

**URL PHP** چاپ‌شده توسط `pinx dev` یا `pinx fe:d` را باز کنید — نه port Vite. [فرانت‌اند و Vite](../basic/frontend-vite.md) و [@pinooxhq/vite-plugin](../basic/vite-plugin.md) را ببینید.

**وابستگی‌ها:**

```bash
pinx deps:st                # وضعیت Composer + npm
pinx deps:i                 # نصب همه
pinx deps:up                # به‌روزرسانی همه
```

Scopeها، گزینه‌ها (`--composer-only`، `--all-themes`، `--plain`، …) و عیب‌یابی: [CLI وابستگی‌ها (`deps`)](./deps-cli.md).

الزامات بین‌اپی (`depends` / `use_app()`) جداست — [وابستگی اپ‌ها](./app-depends.md). نصب Pinx قبل از extract، `depends` الزامی را اعتبارسنجی می‌کند.
**Pinker** (کش بیلد):

```bash
pinx pinker:st              # مقایسه کش با سورس
pinx pinker:rb              # بازسازی
pinx pinker:df              # تفاوت‌ها
```

---

## انتشار برای production

ساخت پکیج `.pinx` برای نصب روی پلتفرم کامل پینوکس (Manager ← Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # افزایش نسخه در app.php + بیلد
pinx release --sign         # امضا وقتی کلید در app.php → pinx.sign تنظیم شده باشد
```

`pinx build` پیش‌فرض‌های منطقی اعمال می‌کند (حذف `vendor/`، `bin/`، `.env`، `platform/` و ابزارهای dev). فقط در صورت نیاز در `app.php` بازنویسی کنید:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

این دستور یک بررسی ساختاریافته اجرا می‌کند و در صورت خطا، دستور رفع مشکل پیشنهاد می‌دهد:

| گروه | بررسی‌ها |
|------|----------|
| **Project** | `app.php`، هویت پکیج، ساختار `platform/` |
| **Runtime** | نسخه PHP (≥ 8.2)، اکستنشن‌ها، مسیرهای قابل نوشتن |
| **Dependencies** | vendor کامپوزر، Node/npm اختیاری |
| **Environment** | وجود `.env` و متغیرهای کلیدی |
| **Database** | اتصال (با `--skip-db` قابل رد شدن) |
| **Frontend** | استک تم، `package.json` (با `--skip-frontend` قابل رد شدن) |
| **Build** | آمادگی export، آیکون، فیلدهای نسخه |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # گزارش مناسب CI
pinx doctor --no-fixes      # عدم نمایش دستورهای پیشنهادی
```

---

## مرجع دستورها

برای فهرست گروه‌بندی‌شده، `pinx list` را اجرا کنید. نام‌های کوتاه (alias) داخل جدول آمده‌اند.

### پروژه

| دستور | Alias | توضیح |
|-------|-------|-------|
| `new` | — | اسکلت از `pinoox/app` (ویزارد یا فلگ) |
| `init` | — | راه‌اندازی پوشه جاری (`--force` برای بازنویسی) |
| `setup` | — | دیتابیس: مایگریشن platform + اپ، سپس seed |
| `doctor` | `dr` | بررسی سلامت — `--json`، `--skip-db`، `--skip-frontend` |
| `info` | `inf` | نمایش متادیتای `app.php` |

### توسعه

| دستور | توضیح |
|-------|-------|
| `dev` | PHP + Vite HMR وقتی `frontend.stack` برابر vue/react/vite باشد؛ `--no-frontend` برای serve فقط با manifest |

### دیتابیس

| دستور | Alias | توضیح |
|-------|-------|-------|
| `migrate:run` | `migrate` | اجرای مایگریشن‌های اپ (`--platform` اول platform را اجرا می‌کند) |
| `migrate:status` | `migrate:st` | وضعیت مایگریشن |
| `migrate:rollback` | `migrate:rb` | بازگشت آخرین batch (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | ساخت فایل مایگریشن |
| `migrate:platform` | `migrate:pl` | فقط مایگریشن‌های platform |
| `seeder:run` | `seed` | اجرای seeder ها (`-c` نام فایل) |

### پچ‌ها

| دستور | Alias | توضیح |
|-------|-------|-------|
| `patch:run` | `patch` | اجرای پچ‌های در انتظار |
| `patch:status` | `patch:st` | وضعیت پچ |
| `patch:rollback` | `patch:rb` | بازگشت آخرین batch پچ |

### بیلد و انتشار

| دستور | Alias | توضیح |
|-------|-------|-------|
| `build` | `bld` | ساخت پکیج `.pinx` |
| `release` | `rel` | افزایش نسخه + بیلد (`--bump`، `--sign`) |

### اسکلت‌سازی

| دستور | Alias | توضیح |
|-------|-------|-------|
| `make <type> <name>` | `mk` | controller، model، migration، patch، portal، form-request، seeder، test |

### روت‌ها

| دستور | توضیح |
|-------|-------|
| `route:actions` / `routes` | لیست اکشن‌های نام‌دار (`--validate`، `--json`) |

### وابستگی‌ها

| دستور | Alias | توضیح |
|-------|-------|-------|
| `deps:status` | `deps:st` | وضعیت Composer + npm |
| `deps:install` | `deps:i` | نصب وابستگی‌ها |
| `deps:update` | `deps:up` | به‌روزرسانی وابستگی‌ها |

### فرانت‌اند

| دستور | Alias | توضیح |
|-------|-------|-------|
| `fe:info` | `fe:inf` | استک تم و اسکریپت‌های npm |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | بیلد production |
| `fe:dev` | `fe:d` | PHP + Vite HMR |
| `fe:watch` | `fe:w` | rebuild با ذخیره (بدون HMR) |
| `fe:scaffold` | `fe:sc` | فایل‌های شروع (`--stack=vue\|react\|twig`) |

### زمان‌بندی

| دستور | Alias | توضیح |
|-------|-------|-------|
| `schedule:list` | `sched:ls` | لیست تسک‌های cron از `schedule.php` |
| `schedule:run` | `sched:run` | اجرای تسک‌های موعددار (`--dry-run`) |

### Pinion (آپلود تکه‌ای)

| دستور | توضیح |
|-------|-------|
| `pinion:list` | لیست sessionها (`--status`, `--json`) |
| `pinion:info {upload_id}` | جزئیات session |
| `pinion:clean` | پاکسازی منقضی‌ها (`--abort={id}`) |

### Pinker

| دستور | Alias | توضیح |
|-------|-------|-------|
| `pinker:status` | `pinker:st` | مقایسه کش با سورس |
| `pinker:rebuild` | `pinker:rb` | بازسازی کش |
| `pinker:diff` | `pinker:df` | نمایش تفاوت‌ها |
| `pinker:clear` | `pinker:cl` | پاک کردن کش |
| `pinker:overrides` | `pinker:ov` | لیست override ها |

### کیفیت و مستندات

| دستور | توضیح |
|-------|-------|
| `test` / `pest` | اجرای تست‌های اپ (`--unit`، `--feature`) |
| `api:docs` | مستندات REST API |
| `graphql:docs` | مستندات اسکیمای GraphQL |

### متا

| دستور | Alias | توضیح |
|-------|-------|-------|
| `list` | — | فهرست گروه‌بندی‌شده دستورها |
| `version` | `ver` | نسخه CLI |

---

## تشخیص اپ

Pinx از پوشه جاری به سمت بالا حرکت می‌کند تا یک پروژه تک‌اپ معتبر پیدا کند:

1. `app.php` وجود داشته باشد و آرایه‌ای با کلید غیرخالی `package` برگرداند
2. `pinoox/pincore` در `composer.json` لازم باشد یا `vendor/pinoox/pincore` موجود باشد

پکیج تشخیص‌داده‌شده را با متغیرهای محیطی می‌توان override کرد:

| متغیر | کاربرد |
|-------|--------|
| `PINX_PACKAGE` | تعیین اجباری پکیج هدف CLI |
| `PINOOX_DEV_APP` | معادل `PINX_PACKAGE` |
| `PINX_DEV=1` | حالت dev (هنگام واگذاری به pincore به‌طور خودکار تنظیم می‌شود) |

---

## پیش‌نیازها

- **PHP** ≥ 8.2 با اکستنشن‌های لازم برای `pinoox/pincore`
- **Composer** 2.x
- **Node.js** + npm — فقط هنگام استفاده از فرانت‌اند Vite/Vue/React
- **دیتابیس** — MySQL/MariaDB یا هر چیزی که در `.env` تنظیم کنید (برای اپ‌های استاتیک/Twig اختیاری)

---

## مستندات مرتبط

- [فرانت‌اند و Vite](../basic/frontend-vite.md)
- [@pinooxhq/vite-plugin](../basic/vite-plugin.md)
- [نصب و راه‌اندازی](./installing-pinoox.md)
- [مرجع CLI پینوکس (چنداپه)](./cli-reference.md)
- [ساخت اولین اپلیکیشن](./your-first-app.md)
- [مرجع app.php](./app-manifest.md)

---

[← بازگشت به فهرست](../README.md)

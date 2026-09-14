# نصب و راه‌اندازی پینوکس

[← بازگشت به فهرست](../README.md)

این راهنما نصب پینوکس ۳.x را پوشش می‌دهد. دو روش برای شروع وجود دارد:

| روش | مناسب برای |
|-----|------------|
| **الف. تک‌اپ با [Pinx CLI](./pinx-cli.md)** | ساخت یک اپ — سریع‌ترین شروع، بدون UI منیجر |
| **ب. پلتفرم کامل (کلاسیک)** | میزبانی چند اپ با نصب‌کننده گرافیکی یا CLI و منیجر |

---

## پیش‌نیازها

| ابزار | نسخه |
|-------|------|
| PHP | 8.2 یا بالاتر (با ext-mysqli، ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (اختیاری) | 18+ — فقط برای build فرانت‌اند تم |

---

## روش الف — تک‌اپ با Pinx CLI

[Pinx CLI](./pinx-cli.md) را یک بار نصب کنید، اپ جدید بسازید و اجرا کنید:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # پیشنهاد com_my_shop — در ویزارد تایید یا ویرایش کنید
cd my-shop
cp .env.example .env          # اگر دیتابیس دارید DB_* را تنظیم کنید
pinx setup                    # مایگریشن platform + اپ، اجرای seeder ها
pinx dev                      # http://127.0.0.1:8000
```

یا بدون نصب گلوبال، با قالب پروژه:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

هر زمان با `pinx doctor` وضعیت PHP، env، دیتابیس و آمادگی build را بررسی کنید. برای گردش کار روزانه و مرجع کامل دستورها، [راهنمای Pinx CLI](./pinx-cli.md) را ببینید.

---

## روش ب — پلتفرم کامل (کلاسیک)

### ۱. دریافت پروژه

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

یا آخرین Release را از [گیت‌هاب](https://github.com/pinoox/pinoox) دانلود و extract کنید، سپس `composer install` را اجرا کنید.

---

### ۲. قرار دادن در وب‌سرور

پوشه پروژه را در document root قرار دهید:

| محیط | مسیر نمونه |
|------|------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Document Root را روی **ریشه پروژه** (همان پوشه‌ای که `index.php` در آن است) تنظیم کنید — نه زیرپوشه `public`.

---

### ۳. ساخت پایگاه داده

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### ۴. اجرای نصب‌کننده

دو روش وجود دارد؛ هر دو همان مراحل نصب گرافیکی را اجرا می‌کنند (ذخیره دیتابیس، migrate هسته و اپ‌ها، patch، ساخت مدیر، زبان، و غیرفعال کردن installer).

#### نصب گرافیکی

مرورگر را باز کنید:

```
http://localhost/pinoox
```

اپ سیستمی `com_pinoox_installer` اجرا می‌شود. مراحل GUI:

1. بررسی پیش‌نیازهای PHP
2. پذیرش توافق‌نامه
3. وارد کردن اطلاعات دیتابیس
4. ساخت حساب مدیر
5. پایان نصب

#### نصب از CLI

بدون مرورگر، از فایل کانفیگ محلی (gitignore زیر `.pinoox/`):

```bash
php pinoox install-platform init
# فایل .pinoox/install-platform.php را ویرایش کنید (دیتابیس + کاربر مدیر + lang)
php pinoox install-platform check
php pinoox install-platform run
```

`init` فایل `.pinoox/install-platform.php` را می‌سازد و در صورت وجود، مقادیر `DB_*` را از `.env` پر می‌کند. فیلدهای لازم: `lang`، `db` و `user` (رمز مدیر حداقل ۶ کاراکتر). اتصال‌های پشتیبانی‌شده: `mysql`، `mariadb`، `pgsql`، `sqlsrv`.

فلگ‌های مفید:

```bash
php pinoox install-platform init --force
php pinoox install-platform run --dry-run
php pinoox install-platform run -r
php pinoox install-platform run --file=.pinoox/install-platform.php
```

پس از نصب موفق، `/` به Welcome و `/manager` به Manager می‌رود. فایل کانفیگ رمز عبور دارد — با `-r` (`--remove`) بعد از نصب حذفش کنید.

اگر اپ installer از قبل غیرفعال باشد، `run` بدون `--force` متوقف می‌شود.

مرجع دستور: [CLI پینوکس](./cli-reference.md#نصب-پلتفرم).

---

### ۵. پس از نصب

ساختار اصلی:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← اپ‌ها
├── vendor/pinoox/pincore/  ← هسته
└── config/             ← تنظیمات پروژه (شامل app-router.config.php)
```

اولین اپ خود را بسازید:

```bash
php pinoox app:create com_acme_blog
```

---

## عیب‌یابی سریع

| مشکل | راه‌حل |
|------|--------|
| صفحه سفید | `composer install` و log خطای PHP را بررسی کنید |
| 404 روی sub-route | mod_rewrite / `.htaccess` را فعال کنید |
| خطای extension | ext-mysqli و ext-zip را در php.ini فعال کنید |
| نصب‌کننده باز نمی‌شود | مسیر document root و دسترسی نوشتن پوشه‌های runtime |
| Config not found | اول `php pinoox install-platform init` را اجرا کنید |
| already installed | اگر installer غیرفعال است، `php pinoox install-platform run --force` |

---

## مستندات مرتبط

- [Pinx CLI — پروژه تک‌اپ](./pinx-cli.md)
- [مرجع CLI پینوکس](./cli-reference.md)
- [ساخت اولین اپلیکیشن](./your-first-app.md)
- [ساختار پوشه‌بندی](./structure.md)
- [پینوکس چیست؟](../introduction/what-is-pinoox.md)

---

[← بازگشت به فهرست](../README.md)

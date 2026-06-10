# نصب و راه‌اندازی پینوکس

این راهنما نصب پینوکس ۳.x روی لوکال‌هاست (MAMP/XAMPP/WAMP) را با نصب‌کننده گرافیکی توضیح می‌دهد.

---

## پیش‌نیازها

| ابزار | نسخه |
|-------|------|
| PHP | 8.1 یا بالاتر (با ext-mysqli، ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (اختیاری) | 18+ — فقط برای build فرانت‌اند تم |

---

## ۱. دریافت پروژه

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

یا آخرین Release را از [گیت‌هاب](https://github.com/pinoox/pinoox) دانلود و extract کنید، سپس `composer install` را اجرا کنید.

---

## ۲. قرار دادن در وب‌سرور

پوشه پروژه را در document root قرار دهید:

| محیط | مسیر نمونه |
|------|------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Document Root را روی **ریشه پروژه** (همان پوشه‌ای که `index.php` در آن است) تنظیم کنید — نه زیرپوشه `public`.

---

## ۳. ساخت پایگاه داده

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## ۴. اجرای نصب‌کننده

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

---

## ۵. پس از نصب

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

---

## مستندات مرتبط

- [ساخت اولین اپلیکیشن](./your-first-app.md)
- [ساختار پوشه‌بندی](./structure.md)
- [پینوکس چیست؟](../introduction/what-is-pinoox.md)

# مشکلات رایج

[← بازگشت به فهرست](../README.md)

راه‌حل‌های عملی برای خطاهای پرتکرار هنگام نصب، اجرا و توسعه روی پینوکس. هر بخش **یک روش** پیشنهادی دارد.

---

## `composer install` خطا می‌دهد

**علائم:** extension گم‌شده، نسخه PHP پایین، یا timeout شبکه.

**راه‌حل:**

1. PHP 8.2+ و extensionهای `mysqli`، `zip`، `mbstring`، `json` را فعال کنید.
2. قبل از install، چک پلتفرم را اجرا کنید:

```bash
php launcher/check.php
```

3. دوباره نصب:

```bash
composer install --no-interaction
```

روی shared hosting اگر `composer` در PATH نیست، vendor را لوکال build و آپلود کنید.

---

## خطای permission (دسترسی فایل)

**علائم:** Cannot write to `cache/`، `storage/`، `pinker/`.

**راه‌حل (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

کاربر وب‌سرور (مثلاً `www-data` یا `apache`) باید روی پوشه‌های writable بنویسد. روی Windows/MAMP معمولاً پوشه پروژه را خارج از `Program Files` قرار دهید.

---

## `.htaccess` / rewrite کار نمی‌کند

**علائم:** 404 روی همه URLها به‌جز `index.php`؛ API در مرورگر JSON برنمی‌گرداند.

**راه‌حل:**

1. `mod_rewrite` Apache را فعال کنید.
2. `AllowOverride All` برای DocumentRoot تنظیم شود.
3. فایل `.htaccess` در ریشه پروژه باشد.
4. تست سریع: `http://localhost/pinoox/api/v1/ping` — اگر JSON دیدید، rewrite درست است.

در nginx به‌جای `.htaccess` باید rule مربوط به `try_files` و `index.php` را در config سرور بنویسید.

---

## اتصال دیتابیس برقرار نمی‌شود

**علائم:** `SQLSTATE[HY000] [2002] Connection refused` یا access denied.

**راه‌حل:**

1. MySQL/MariaDB در حال اجرا باشد.
2. مقادیر `config/database.config.php` یا `.env` را بررسی کنید:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. دیتابیس از قبل ساخته شده باشد (`CREATE DATABASE ... utf8mb4`).
4. روی cPanel، host ممکن است `localhost` نباشد — از hostname پنل استفاده کنید.

---

## Pinker rebuild لازم است

**علائم:** config یا route قدیمی؛ تغییرات `app.php` اعمال نمی‌شود.

**راه‌حل:**

```bash
php pinoox pinker:rebuild com_my_shop
# یا alias:
php pinoox bake com_my_shop

# همه اپ‌ها:
php pinoox pinker:rebuild all
```

بعد از تغییر route، config، یا deploy در production معمولاً rebuild لازم است.

---

## Route not found (404 روی endpoint)

**علائم:** روت در کد تعریف شده اما 404 می‌گیرید.

**راه‌حل:**

1. فایل روت در `apps/{package}/routes/` ثبت شده و در `app.php` → `router.routes` لیست شده باشد.
2. URL با prefix اپ (`app:router`) هم‌خوان باشد:

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Pinker rebuild کنید (بخش بالا).
4. متد HTTP درست باشد (`GET` vs `POST`).

---

## 404 — اپ resolve نمی‌شود

**علائم:** صفحه پیش‌فرض یا 404؛ اپ اشتباه load می‌شود.

**راه‌حل:**

1. نگاشت path/host را بررسی کنید:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. در `config/domain.config.php` (یا map مربوط) host و path درست تنظیم شوند.
3. `'enable' => true` در `app.php` اپ باشد.
4. پوشه اپ = مقدار `'package'` در `app.php` (مثلاً `com_my_shop`).

---

## تست‌ها fail می‌شوند

```bash
php pinoox test com_my_shop
```

- `.env.testing` با DB جدا
- migration اجرا شده: `php pinoox migrate com_my_shop`
- بعد از `fakeApp()` → `deleteFakeApp()`

جزئیات: [شروع تست](../test/getting-started.md)

---

## مستندات مرتبط

- [نصب و راه‌اندازی](../start/installing-pinoox.md)
- [ساختار پوشه‌بندی](../start/structure.md)
- [روتر — Routers](../basic/routers.md)
- [پیکربندی — Config](../basic/config.md)
- [Pinker — بیلد Pinoox](../advanced/pinker.md)
- [شروع دیتابیس](../database/getting-started.md)
- [تماس با پشتیبانی](./contact-support.md)

---

[← بازگشت به فهرست](../README.md)

# المشكلات الشائعة

[← العودة إلى الفهرس](../README.md)

إصلاحات عملية للأخطاء المتكررة أثناء التثبيت والتشغيل والتطوير على Pinoox. كل قسم يوصي **بأسلوب واحد**.

---

## فشل `composer install`

**الأعراض:** امتداد ناقص، إصدار PHP منخفض، أو انتهاء مهلة الشبكة.

**الإصلاح:**

1. فعّل PHP 8.1+ والامتدادات `mysqli`، `zip`، `mbstring`، `json`.
2. شغّل فحص المنصة قبل التثبيت:

```bash
php launcher/check.php
```

3. ثبّت مجددًا:

```bash
composer install --no-interaction
```

على الاستضافة المشتركة، إذا لم يكن `composer` في PATH، ابنِ vendor محليًا وارفعه.

---

## أخطاء الصلاحيات (الوصول للملفات)

**الأعراض:** تعذّر الكتابة إلى `cache/`، `storage/`، `pinker/`.

**الإصلاح (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

يجب أن يستطيع مستخدم خادم الويب (مثل `www-data` أو `apache`) الكتابة إلى المجلدات القابلة للكتابة. على Windows/MAMP، ضع مجلد المشروع خارج `Program Files`.

---

## `.htaccess` / rewrite لا يعمل

**الأعراض:** 404 على كل عناوين URL ما عدا `index.php`؛ API لا يُرجع JSON في المتصفح.

**الإصلاح:**

1. فعّل Apache `mod_rewrite`.
2. اضبط `AllowOverride All` لـ DocumentRoot.
3. تأكد من وجود `.htaccess` في جذر المشروع.
4. اختبار سريع: `http://localhost/pinoox/api/v1/ping` — إذا رأيت JSON، rewrite يعمل.

على nginx، اكتب قواعد `try_files` و`index.php` في إعداد الخادم بدل `.htaccess`.

---

## فشل اتصال قاعدة البيانات

**الأعراض:** `SQLSTATE[HY000] [2002] Connection refused` أو access denied.

**الإصلاح:**

1. تأكد أن MySQL/MariaDB يعمل.
2. تحقق من القيم في `config/database.config.php` أو `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. أنشئ قاعدة البيانات مسبقًا (`CREATE DATABASE ... utf8mb4`).
4. على cPanel، قد لا يكون المضيف `localhost` — استخدم اسم المضيف من اللوحة.

---

## مطلوب إعادة بناء Pinker

**الأعراض:** إعدادات أو مسارات قديمة؛ تغييرات `app.php` لا تُطبَّق.

**الإصلاح:**

```bash
php pinoox pinker:rebuild com_my_shop
# or alias:
php pinoox bake com_my_shop

# all apps:
php pinoox pinker:rebuild all
```

بعد تغيير المسارات أو الإعدادات أو النشر في الإنتاج، عادةً مطلوب rebuild.

---

## المسار غير موجود (404 على نقطة النهاية)

**الأعراض:** المسار معرّف في الكود لكنك تحصل على 404.

**الإصلاح:**

1. تأكد أن ملف المسار في `apps/{package}/routes/` ومُدرج في `app.php` → `router.routes`.
2. طابق URL مع بادئة التطبيق (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. شغّل Pinker rebuild (انظر أعلاه).
4. استخدم طريقة HTTP الصحيحة (`GET` مقابل `POST`).

---

## 404 — التطبيق غير محلول

**الأعراض:** الصفحة الافتراضية أو 404؛ تطبيق خاطئ يُحمَّل.

**الإصلاح:**

1. تحقق من ربط المسار/المضيف:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. اضبط المضيف والمسار في `config/domain.config.php` (أو الخريطة المناسبة).
3. تأكد من `'enable' => true` في `app.php` للتطبيق.
4. اسم مجلد التطبيق يجب أن يساوي `'package'` في `app.php` (مثل `com_my_shop`).

---

## فشل الاختبارات

```bash
php pinoox test com_my_shop
```

- `.env.testing` مع DB منفصلة
- الترحيلات نُفّذت: `php pinoox migrate com_my_shop`
- بعد `fakeApp()` → `deleteFakeApp()`

التفاصيل: [البدء مع الاختبار](../test/getting-started.md)

---

## وثائق ذات صلة

- [تثبيت Pinoox](../start/installing-pinoox.md)
- [بنية المشروع](../start/structure.md)
- [المُوجّه (Routers)](../basic/routers.md)
- [الإعدادات (Config)](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [البدء مع قاعدة البيانات](../database/getting-started.md)
- [التواصل مع الدعم](./contact-support.md)

---

[← العودة إلى الفهرس](../README.md)

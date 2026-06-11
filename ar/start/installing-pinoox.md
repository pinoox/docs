# تثبيت Pinoox

[← العودة إلى الفهرس](../README.md)

يغطي هذا الدليل تثبيت Pinoox 3.x. هناك طريقتان للبدء:

| المسار | الأنسب لـ |
|-------|----------|
| **أ. تطبيق واحد مع [Pinx CLI](./pinx-cli.md)** | بناء تطبيق واحد — أسرع بداية، بدون واجهة المدير |
| **ب. المنصة الكاملة (الكلاسيكية)** | استضافة عدة تطبيقات مع المثبّت الرسومي والمدير |

---

## المتطلبات

| الأداة | الإصدار |
|------|---------|
| PHP | 8.1 أو أحدث (مع ext-mysqli و ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (اختياري) | 18+ — فقط لبناء قوالب الواجهة الأمامية |

---

## المسار أ — تطبيق واحد مع Pinx CLI

ثبّت [Pinx CLI](./pinx-cli.md) مرة واحدة، ثم أنشئ تطبيقًا جديدًا وشغّله:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # يقترح com_my_shop — أكّد أو عدّل في المعالج
cd my-shop
cp .env.example .env          # عيّن قيم DB_* إذا كنت تستخدم قاعدة بيانات
pinx setup                    # ترحيل المنصة + التطبيق، وتشغيل الـ seeders
pinx dev                      # http://127.0.0.1:8000
```

أو بدون تثبيت عام، عبر قالب المشروع:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

شغّل `pinx doctor` في أي وقت لفحص PHP والبيئة وقاعدة البيانات وجاهزية البناء. راجع [دليل Pinx CLI](./pinx-cli.md) الكامل لسير العمل اليومي ومرجع الأوامر.

---

## المسار ب — المنصة الكاملة (الكلاسيكية)

### 1. الحصول على المشروع

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

بدلًا من ذلك، نزّل أحدث إصدار من [GitHub](https://github.com/pinoox/pinoox)، وفك ضغطه، ثم شغّل `composer install`.

---

### 2. وضعه في خادم الويب

ضع مجلد المشروع في جذر المستندات (document root) لديك:

| البيئة | مسار نموذجي |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

عيّن جذر المستندات إلى **جذر المشروع** (المجلد الذي يحتوي على `index.php`) — وليس إلى مجلد فرعي `public`.

---

### 3. إنشاء قاعدة البيانات

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. تشغيل المثبّت

افتح متصفحك:

```
http://localhost/pinoox
```

يعمل تطبيق النظام `com_pinoox_installer`. خطوات الواجهة الرسومية هي:

1. فحص متطلبات PHP
2. قبول اتفاقية الترخيص
3. إدخال بيانات اعتماد قاعدة البيانات
4. إنشاء حساب المدير
5. إنهاء التثبيت

---

### 5. بعد التثبيت

التخطيط الرئيسي:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← التطبيقات
├── vendor/pinoox/pincore/  ← النواة
└── config/             ← إعدادات المشروع
```

أنشئ تطبيقك الأول:

```bash
php pinoox app:create com_acme_blog
```

---

## استكشاف الأخطاء السريع

| المشكلة | الحل |
|---------|-----|
| صفحة فارغة | شغّل `composer install` وافحص سجلات أخطاء PHP |
| خطأ 404 في المسارات الفرعية | فعّل mod_rewrite / ملف `.htaccess` |
| خطأ امتداد مفقود | فعّل ext-mysqli و ext-zip في php.ini |
| المثبّت لا يفتح | تحقق من جذر المستندات وصلاحيات الكتابة على مجلدات التشغيل |

---

## وثائق ذات صلة

- [Pinx CLI (تطبيق واحد)](./pinx-cli.md)
- [تطبيقك الأول](./your-first-app.md)
- [بنية المشروع](./structure.md)
- [ما هو Pinoox؟](../introduction/what-is-pinoox.md)

---

[← العودة إلى الفهرس](../README.md)

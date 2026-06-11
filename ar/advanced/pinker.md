# Pinker والتخزين المؤقت (Cache)

[← العودة إلى الفهرس](../README.md)

**Pinker** هو طبقة التحضير/التشغيل (bake/runtime) في Pinoox 3.x: تُجمَّع الإعدادات (Config) والتخزين المؤقت (Cache) من المصدر إلى ملفات PHP يمكن تضمينها عبر `include` لإقلاع أسرع. المسار القياسي لكل تطبيق: **`pinker/apps/{package}/`**.

---

## هيكل المجلدات

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← نسخة app.php المحضّرة (baked)
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← القوالب المُجمَّعة
```

على مستوى المشروع:

```
pinker/config/          ← الإعدادات المحضّرة (غير الحساسة للبيئة)
pinker/state/config/    ← تجاوزات ما بعد التثبيت (مثل قاعدة البيانات)
```

---

## أوامر سطر الأوامر (CLI)

```bash
# إعادة بناء Pinker لتطبيق واحد
php pinoox pinker:rebuild com_acme_shop

# اختصار
php pinoox bake com_acme_shop

# الحالة: مقارنة المصدر بالمخرجات المحضّرة
php pinoox pinker:status com_acme_shop

# بناء التخزين المؤقت (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Twig فقط
php pinoox cache:build com_acme_shop --only=twig

# Pinker فقط
php pinoox cache:build com_acme_shop --only=pinker

# مسح التخزين المؤقت
php pinoox cache:clear com_acme_shop
```

---

## متى يجب إعادة البناء

| الحدث | الأمر |
|-------|---------|
| تغيير `app.php` أو الإعدادات | `pinker:rebuild` |
| تغيير route / api | `cache:build` |
| تغيير `.twig` في بيئة الإنتاج | `cache:build --only=twig` |
| بعد التثبيت على الخادم | `cache:build` + `pinker:rebuild` |
| قبل بناء `.pinx` | `cache:build` (التخزين المؤقت داخل الحزمة) |

---

## تفعيل التخزين المؤقت أثناء التشغيل

في `apps/{package}/app.php`:

```php
'cache' => [
    'enabled' => false,   // الافتراضي — فعّله بقيمة true في الإنتاج عند الحاجة
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

## مرآة التطبيق — `pinker/app.php`

يمكن لكل تطبيق أن يمتلك نسخة مرآة محضّرة:

```
apps/com_acme_shop/pinker/app.php   ← المصدر/المرجع في المستودع
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← وقت التشغيل
```

---

## الدالة المساعدة `pinker()`

لتحضير البيانات يدوياً:

```php
pinker($data, ['lifetime' => 3600]);
```

عادةً تستخدم سطر الأوامر (CLI) بدلاً من ذلك؛ ونادراً ما تحتاجها في كود التطبيق.

---

## سير النشر الموصى به

```bash
# 1. بناء الواجهة الأمامية
php pinoox theme:frontend build com_acme_shop

# 2. التخزين المؤقت
php pinoox cache:build com_acme_shop

# 3. pinker (خاص بالبيئة)
php pinoox pinker:rebuild com_acme_shop
```

---

## نصائح

- لا تعدّل `pinker/state/` يدوياً — المثبّت (Installer) هو من يكتب هناك.
- في بيئة التطوير يكون التخزين المؤقت لوقت التشغيل معطّلاً عادةً؛ أعد البناء فقط بعد التغييرات الكبيرة.
- يمكن لحزمة `.pinx` أن تشحن تخزيناً مؤقتاً مبنياً مسبقاً؛ وعلى الخادم الهدف نفّذ `cache:build --only=pinker` مرة واحدة.

---

## وثائق ذات صلة

- [الإعدادات (Config)](../basic/config.md)
- [قوالب Twig](../basic/templates.md)
- [مرجع سطر الأوامر (CLI)](../start/cli-reference.md)
- [الموجّه (Router)](../basic/routers.md)

---

[← العودة إلى الفهرس](../README.md)

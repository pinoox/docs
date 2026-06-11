# بنية المشروع

[← العودة إلى الفهرس](../README.md)

يستخدم Pinoox معمارية HMVC: كل تطبيق ضمن `apps/{package}/` هو وحدة MVC كاملة ومستقلة. تقع نواة الإطار في `vendor/pinoox/pincore/` ولا تُعدَّل إلا عند تغيير المنصة نفسها.

---

## تخطيط المشروع

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← النواة (حزمة Composer)
├── apps/                    ← جميع التطبيقات
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← الملفات المرفوعة وتخزين التطبيقات
```

---

## تخطيط التطبيق

```
apps/com_acme_shop/
├── app.php                  ← ملف البيان (مطلوب)
├── boot.php                 ← مسارات/أحداث برمجية (اختياري)
├── schedule.php             ← مهام cron (اختياري)
├── Controller/              ← معالجات HTTP
├── Model/                   ← نماذج Eloquent
├── Flow/                    ← الوسائط (middleware)
├── Component/               ← منطق العمل
├── Portal/                  ← بوابات (facades) التطبيق (اختياري)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← ثوابت أسماء الإجراءات (اختياري)
├── theme/default/           ← Twig + الأصول (assets)
├── lang/en/                 ← الترجمات
├── config/                  ← إعدادات التطبيق
├── database/migrations/
└── pinker/                  ← مرآة البناء
```

العروض (Views) ليست في مجلد `View/` منفصل — تقع القوالب في `theme/{themeName}/`.

---

## app.php — الحقول الأساسية

```php
<?php

return [
    'package' => 'com_acme_shop',   // = اسم المجلد
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## مساحات الأسماء (Namespaces)

PSR-4‏: `App\` ← `apps/`

| الملف | مساحة الأسماء |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## قواعد التسمية

- الحزمة (Package): `com_{vendor}_{name}` — مثل `com_acme_shop`
- اسم المجلد = قيمة `package` في `app.php` = مقطع مساحة الأسماء
- بادئة جداول قاعدة البيانات: `{package}_` (مثل `com_acme_shop_orders`)

---

## الحد الفاصل بين التطبيق والنواة

| التغيير | الموقع |
|--------|----------|
| نقطة نهاية (endpoint) جديدة | `apps/{package}/Controller/` + `routes/` |
| ترحيل (Migration) | `apps/{package}/database/migrations/` |
| خطأ في الإطار | `pinoox/pincore` (المستودع الأصلي) |
| واجهة المستخدم | `apps/{package}/theme/` |

أبقِ التطبيقات مستقلة — استخدم بوابات `Pinoox\Portal\*` بدلًا من ربط التطبيقات ببعضها.

---

## وثائق ذات صلة

- [تطبيقك الأول](./your-first-app.md)
- [الموجّه (Router)](../basic/routers.md)
- [المتحكمات (Controllers)](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← العودة إلى الفهرس](../README.md)

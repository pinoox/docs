# مرجع ملف البيان app.php

[← العودة إلى الفهرس](../README.md)

`app.php` هو ملف بيان تطبيقك. تقع القيم الافتراضية في `vendor/pinoox/pincore/Component/Package/data/source.php` — تجاوز ما تحتاجه فقط.

---

## الهوية والتفعيل

| المفتاح | الغرض |
|-----|---------|
| `package` | اسم المجلد = مساحة الأسماء (`com_acme_shop`) |
| `name` | اسم العرض |
| `enable` | تفعيل / تعطيل التطبيق |
| `description`, `developer`, `icon` | البيانات الوصفية |
| `version-name`, `version-code` | إصدار التطبيق |
| `sys-app`, `hidden`, `dock` | تطبيق نظام / مخفي / شريط المدير |
| `minpin` | الحد الأدنى لإصدار المنصة |

---

## الموجّه (Router) والإقلاع (boot)

| المفتاح | الغرض |
|-----|---------|
| `router.routes` | ملفات `routes/*.php` |
| `boot` | تشغيل `boot.php` (افتراضيًا true) |
| `boot-global` | الإقلاع مع كل طلب HTTP |
| `extends` | الإقلاع عند إقلاع التطبيق المضيف |
| `loader` | ملفات إضافية (`func.php`) |
| `depends` | التطبيقات المطلوبة |

راجع [boot.php والأحداث](../advanced/boot-and-events.md).

---

## Flow والأمان

| المفتاح | الغرض |
|-----|---------|
| `flow` | الـ flows العامة (BootFlow) |
| `alias` | الاسم ← صنف Flow |
| `auth` | الوضع، مدة الصلاحية، JWT/cookie |
| `access` | ‏RBAC: ‏`groups`, `super_roles` |
| `transport` | مشاركة المستخدم/الملفات/الوصول مع المنصة |

راجع [Flows](../basic/flows.md) و [إدارة المستخدمين](../advanced/user-management.md) و [الوصول](../advanced/access-permissions.md).

---

## واجهة المستخدم والقالب (Theme)

| المفتاح | الغرض |
|-----|---------|
| `theme` | مجلد القالب النشط |
| `theme-context`, `theme-contexts`, `theme-extends` | تعدد السياقات / الوراثة |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | اللغة الافتراضية |
| `open` | سلوك الفتح في المدير |

---

## قاعدة البيانات والتخزين

| المفتاح | الغرض |
|-----|---------|
| `database` | تجاوز اتصال قاعدة البيانات |
| `table.prefix` | بادئة الجداول |
| `transport.user` / `file_storage` / `access` | إعدادات مسبقة أو مفاتيح تفصيلية |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## وقت التشغيل (Runtime)

| المفتاح | الغرض |
|-----|---------|
| `runtime.mode`, `runtime.debug` | تجاوزات الوضع |
| `cache` | خبز (bake) المسارات/api/boot/twig |
| `log`, `redis`, `date` | تجاوزات لكل تطبيق |
| `container` | روابط حقن الاعتماديات (DI) |

---

## Pinker / Pinx

| المفتاح | الغرض |
|-----|---------|
| `pinx` | النوع، minpin، التوقيع (sign) |
| `build` | الاستثناء/التضمين للحزم |

---

## مثال شامل

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## وثائق ذات صلة

- [بنية المشروع](./structure.md)
- [الإعدادات (Config)](../basic/config.md)

---

[← العودة إلى الفهرس](../README.md)

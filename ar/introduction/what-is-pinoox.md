# ما هو Pinoox؟

[← العودة إلى الفهرس](../README.md)

Pinoox هو إطار عمل PHP حديث ومفتوح المصدر (3.x) مبني على معمارية HMVC ومفهوم **التطبيق (app)**. يجعل تطوير الويب المعياري أمرًا بسيطًا: كل تطبيق هو وحدة MVC مستقلة ضمن `apps/{package}/`، بينما تقع نواة الإطار المشتركة في `vendor/pinoox/pincore/`.

---

## معمارية محورها التطبيق

في تثبيت واحد من Pinoox، تعمل عدة تطبيقات مستقلة جنبًا إلى جنب:

```
{project_root}/
├── index.php              ← نقطة دخول الويب
├── pinoox                 ← نقطة دخول CLI
├── composer.json
├── vendor/pinoox/pincore/ ← نواة الإطار (لا تعدّلها إلا لتغييرات النواة)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← تطبيقك
```

- **المشروع (Project)** — المجلد الذي يحتوي على `index.php` و `apps/` (اسم المجلد لا يهم).
- **التطبيق (App)** — وحدة كاملة لها متحكماتها (Controllers) ونماذجها (Models) ومساراتها (Routes) وقالبها (Theme) وإعداداتها (Config) الخاصة.
- **النواة (Core)** — المحرك المشترك (الموجّه Router، و HTTP، وقاعدة البيانات، و Twig، و CLI، وغير ذلك).

اكتب منطق العمل في `apps/`، وليس في `vendor/pinoox/pincore/`.

---

## دورة حياة طلب HTTP

```
المتصفح → index.php → الإقلاع (bootstrap)
       → تحديد التطبيق النشط (النطاق أو بادئة URL)
       → تحميل app.php و routes/
       → Flows → المتحكم → النموذج (اختياري) → العرض أو JSON
```

---

## تسمية التطبيقات

صيغة الحزمة (package) الموصى بها:

```
com_{vendor}_{name}
```

مثال: `com_acme_shop` — يجب أن يتطابق اسم المجلد وقيمة `package` في `app.php` ومقطع مساحة الأسماء (namespace) جميعًا.

---

## مناسب لـ

- المواقع متعددة الأقسام ولوحات الإدارة حيث يمكن أن يكون كل قسم تطبيقًا منفصلًا
- الفرق التي تريد تطوير الوحدات واختبارها وصيانتها بشكل مستقل
- مشاريع PHP 8.2+ مع Composer وواجهة CLI المدمجة (`php pinoox …`)

---

## وثائق ذات صلة

- [ميزات Pinoox](./features-pinoox.md)
- [تثبيت Pinoox](../start/installing-pinoox.md)
- [تطبيقك الأول](../start/your-first-app.md)
- [دليل تطبيق Notes API](../examples/simple-api-app.md)
- [دليل تطبيق دفتر الهاتف](../examples/phonebook-app.md)
- [دليل تطبيق نموذج التواصل](../examples/contact-form-app.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)

# وثائق Pinoox

الوثائق الرسمية للمطورين لبناء التطبيقات على منصة Pinoox (PHP 8.2+، معمارية HMVC).

كل دليل يشرح **منهجًا واحدًا موصى به** مع أمثلة عملية. اختر قسمًا أدناه أو تصفّح حسب الموضوع.

**اللغات:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](./README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### المقدمة

#### [ما هو Pinoox؟](./introduction/what-is-pinoox.md)
#### [ميزات Pinoox](./introduction/features-pinoox.md)
#### [المساهمة في Pinoox](./introduction/contributions.md)

### البدء

#### [تثبيت Pinoox](./start/installing-pinoox.md)
#### [تطبيقك الأول](./start/your-first-app.md)
#### [بنية المشروع](./start/structure.md)
#### [مرجع Pinoox CLI](./start/cli-reference.md)
#### [Pinx CLI (مشاريع التطبيق الواحد)](./start/pinx-cli.md)
#### [مرجع ملف البيان app.php](./start/app-manifest.md)

### أدلة عملية

#### [شرح تطبيقي: تطبيق Notes API](./examples/simple-api-app.md)
#### [شرح تطبيقي: تطبيق ويب لدفتر الهاتف](./examples/phonebook-app.md)
#### [شرح تطبيقي: تطبيق نموذج التواصل](./examples/contact-form-app.md)
#### [شرح تطبيقي: تطبيق مدونة بسيط](./examples/blog-app.md)
#### [شرح تطبيقي: لوحة المهام (Todo)](./examples/task-board-app.md)
#### [شرح تطبيقي: تطبيق معرض الصور](./examples/gallery-app.md)
#### [شرح تطبيقي: لوحة Vue SPA](./examples/vue-spa-app.md)
#### [شرح تطبيقي: لوحة React SPA](./examples/react-spa-app.md)
#### [شرح تطبيقي: Vite هجين (Twig + عنصر JS)](./examples/vite-hybrid-app.md)

### المفاهيم الأساسية

#### [المُوجّه (Router)](./basic/routers.md)
#### [المتحكمات (Controllers)](./basic/controllers.md)
#### [Flow (الوسيط)](./basic/flows.md)
#### [طلب HTTP (Request)](./basic/requests.md)
#### [استجابة HTTP (Response)](./basic/responses.md)
#### [URL وبناء الروابط](./basic/url.md)
#### [مسار الملفات (File Path)](./basic/path.md)
#### [التحقق (Validation)](./basic/validation.md)
#### [العروض (Views)](./basic/views.md)
#### [قوالب Twig](./basic/templates.md)
#### [Portal (الواجهة)](./basic/portal.md)
#### [الإعدادات (Config)](./basic/config.md)
#### [اللغة والترجمة](./basic/language.md)

### مواضيع متقدمة

#### [Pinker والتخزين المؤقت (Cache)](./advanced/pinker.md)
#### [Patches (تحديثات البيانات)](./advanced/patches.md)

#### [خدمات التطبيق (Component + Portal)](./advanced/services.md)
#### [الدوال المساعدة العامة (Global Helpers)](./advanced/helpers.md)
#### [إرسال البريد الإلكتروني](./advanced/mail.md)
#### [عميل HTTP (HTTP Client)](./advanced/http-client.md)
#### [إدارة المستخدمين](./advanced/user-management.md)
#### [إدارة الملفات (File Management)](./advanced/file-management.md)
#### [بروتوكول Pinion](./advanced/pinion.md)
#### [إدارة الرموز (Token Management)](./advanced/token-management.md)
#### [الوصول والصلاحيات (Access & Permissions)](./advanced/access-permissions.md)
#### [النقل (Transport) — الموارد المشتركة](./advanced/transport.md)
#### [ملف boot.php والأحداث (Events)](./advanced/boot-and-events.md)
#### [الجدولة (Cron)](./advanced/schedule.md)

### قاعدة البيانات

#### [البدء مع قاعدة البيانات](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [التصفح (Pagination)](./database/pagination.md)
#### [الترحيلات (Migrations)](./database/migrations.md)

### Eloquent ORM

#### [البدء مع Eloquent ORM](./eloquent-orm/getting-started.md)
#### [علاقات Eloquent (Relationships)](./eloquent-orm/relationships.md)
#### [مجموعات Eloquent (Collections)](./eloquent-orm/collections.md)
#### [Mutators و Casts](./eloquent-orm/mutators-casts.md)
#### [موارد API (API Resources)](./eloquent-orm/api-resources.md)
#### [تسلسل النموذج (Serialization)](./eloquent-orm/serialization.md)
#### [بيانات الاختبار — Seeders](./eloquent-orm/factories.md)

### الاختبار

#### [البدء مع الاختبار في Pinoox](./test/getting-started.md)
#### [اختبارات HTTP في Pinoox](./test/http-tests.md)
#### [اختبار Console في Pinoox](./test/console-tests.md)
#### [اختبار المتصفح (HTML) في Pinoox](./test/browser-tests.md)
#### [اختبار قاعدة البيانات في Pinoox](./test/database.md)
#### [اختبار التسلسل في Pinoox](./test/serialization.md)
#### [Mocking في Pinoox](./test/mocking.md)

### الأسئلة الشائعة

#### [المشكلات الشائعة](./faq/common-issues.md)
#### [التواصل مع الدعم](./faq/contact-support.md)

---

### المصدر
**مصدر الأمثلة:** [docs/source/](../source/) — الكود الكامل لكل دليل

أدلة خطوة بخطوة لتطبيقات حقيقية — استخدمها بعد قراءة الأساسيات وعندما تريد كودًا عمليًا.

---

### كيف تقرأ هذه الوثائق

1. ابدأ بقسمي **المقدمة** و **البدء** إذا كنت جديدًا على Pinoox.
2. اتبع **الأدلة العملية** — ابنِ JSON API وموقعًا بسيطًا خطوة بخطوة.
3. اقرأ **المفاهيم الأساسية** أثناء بناء المسارات والمتحكمات والعروض.
4. استخدم **قاعدة البيانات** و **Eloquent ORM** عند إضافة التخزين.
5. ارجع إلى **المواضيع المتقدمة** للمصادقة والملفات وPinker والخدمات المشتركة.
6. استخدم **الاختبار** قبل الإطلاق في الإنتاج.

كل كود التطبيقات يقع ضمن `apps/{package}/`. نواة الإطار هي `vendor/pinoox/pincore/` — لا تضع منطق العمل الخاص بالتطبيق هناك.

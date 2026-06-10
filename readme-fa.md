# مستندات پینوکس

مستندات رسمی توسعه اپلیکیشن روی پلتفرم پینوکس (PHP 8.1+، معماری HMVC).

هر راهنما **یک روش پیشنهادی** را با مثال عملی توضیح می‌دهد. از فهرست زیر یا جدول موضوعات شروع کنید.

**زبان‌ها:** [English](./readme.md) · [فارسی](./readme-fa.md)

---

### معرفی

#### [پینوکس چیست؟](./fa/introduction/what-is-pinoox.md)
#### [ویژگی‌های پینوکس](./fa/introduction/features-pinoox.md)
#### [مشارکت در توسعه](./fa/introduction/contributions.md)

### شروع به کار

#### [نصب و راه‌اندازی](./fa/start/installing-pinoox.md)
#### [ساخت اولین اپلیکیشن](./fa/start/your-first-app.md)
#### [ساختار پوشه‌بندی](./fa/start/structure.md)

### مفاهیم پایه

#### [Routers](./fa/basic/routers.md)
#### [Controllers](./fa/basic/controllers.md)
#### [Flows](./fa/basic/flows.md)
#### [Requests](./fa/basic/requests.md)
#### [Responses](./fa/basic/responses.md)
#### [URL](./fa/basic/url.md)
#### [Path](./fa/basic/path.md)
#### [Validation](./fa/basic/validation.md)
#### [Viewها](./fa/basic/views.md)
#### [Templates](./fa/basic/templates.md)
#### [Portal](./fa/basic/portal.md)
#### [Config](./fa/basic/config.md)
#### [Language](./fa/basic/language.md)

### مفاهیم پیشرفته

#### [Pinoox Baker (Pinker)](./fa/advanced/pinker.md)
#### [سرویس‌ها](./fa/advanced/services.md)
#### [Helperها](./fa/advanced/helpers.md)
#### [ایمیل](./fa/advanced/mail.md)
#### [Http Client](./fa/advanced/http-client.md)
#### [مدیریت کاربران](./fa/advanced/user-management.md)
#### [مدیریت فایل](./fa/advanced/file-management.md)
#### [مدیریت توکن](./fa/advanced/token-management.md)

### کار با دیتابیس

#### [شروع به کار](./fa/database/getting-started.md)
#### [Query Builder](./fa/database/query-builder.md)
#### [صفحه‌بندی](./fa/database/pagination.md)
#### [Migrations](./fa/database/migrations.md)

### Eloquent ORM

#### [شروع به کار](./fa/eloquent-orm/getting-started.md)
#### [روابط](./fa/eloquent-orm/relationships.md)
#### [مجموعه‌ها](./fa/eloquent-orm/collections.md)
#### [Mutatorها / Castها](./fa/eloquent-orm/mutators-casts.md)
#### [منابع API](./fa/eloquent-orm/api-resources.md)
#### [سریال‌سازی](./fa/eloquent-orm/serialization.md)
#### [Factoryها](./fa/eloquent-orm/factories.md)

### روش تست

#### [شروع به کار](./fa/test/getting-started.md)
#### [تست HTTP](./fa/test/http-tests.md)
#### [تست Console](./fa/test/console-tests.md)
#### [تست مرورگر](./fa/test/browser-tests.md)
#### [دیتابیس](./fa/test/database.md)
#### [سریال‌سازی](./fa/test/serialization.md)
#### [Mocking](./fa/test/mocking.md)

### سوالات متداول

#### [مشکلات رایج](./fa/faq/common-issues.md)
#### [تماس با پشتیبانی](./fa/faq/contact-support.md)

### ترتیب پیشنهادی مطالعه

1. **معرفی** و **شروع به کار** — اگر تازه با پینوکس آشنا شده‌اید.
2. **مفاهیم پایه** — هنگام ساخت روت، کنترلر و ویو.
3. **دیتابیس** و **Eloquent ORM** — برای ذخیره داده.
4. **مفاهیم پیشرفته** — احراز هویت، فایل، Pinker و سرویس‌های مشترک.
5. **روش تست** — قبل از انتشار در محیط واقعی.

کد اپلیکیشن در `apps/{package}/` قرار می‌گیرد. هسته فریمورک در `vendor/pinoox/pincore/` است — منطق اپ را آنجا ننویسید.

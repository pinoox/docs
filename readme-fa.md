

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

#### [مرجع CLI](./fa/start/cli-reference.md)

#### [مرجع app.php](./fa/start/app-manifest.md)

### نمونه‌های عملی

#### [اپ API یادداشت](./fa/examples/simple-api-app.md)

#### [اپ دفترچه تلفن (وب)](./fa/examples/phonebook-app.md)

#### [فرم تماس سایت](./fa/examples/contact-form-app.md)

#### [وبلاگ ساده](./fa/examples/blog-app.md)

#### [تابلوی کار (Todo)](./fa/examples/task-board-app.md)

#### [گالری تصاویر](./fa/examples/gallery-app.md)

#### [پنل Vue (SPA)](./fa/examples/vue-spa-app.md)

#### [پنل React (SPA)](./fa/examples/react-spa-app.md)

#### [نمونه vite (hybrid)](./fa/examples/vite-hybrid-app.md)

### مفاهیم پایه

#### [روتر - Routers](./fa/basic/routers.md)

#### [کنترلر - Controllers](./fa/basic/controllers.md)

#### [فلو - Flows](./fa/basic/flows.md)

#### [درخواست - Requests](./fa/basic/requests.md)

#### [پاسخ - Responses](./fa/basic/responses.md)

#### [آدرس - URL](./fa/basic/url.md)

#### [مسیر - Path](./fa/basic/path.md)

#### [اعتبارسنجی - Validation](./fa/basic/validation.md)

#### [آماده سازی Views](./fa/basic/views.md)

#### [قالب - Templates](./fa/basic/templates.md)

#### [پورتال - Portal](./fa/basic/portal.md)

#### [تنظیمات - Config](./fa/basic/config.md)

#### [زبان - Language](./fa/basic/language.md)

### مفاهیم پیشرفته

#### [پینکر - Pinoox Baker (Pinker)](./fa/advanced/pinker.md)

#### [سرویس‌ها](./fa/advanced/services.md)

#### [داده های کمکی - Helpers](./fa/advanced/helpers.md)

#### [ایمیل](./fa/advanced/mail.md)

#### [کلاینت HTTP - Http Client](./fa/advanced/http-client.md)

#### [مدیریت کاربران](./fa/advanced/user-management.md)

#### [مدیریت فایل](./fa/advanced/file-management.md)

#### [مدیریت توکن](./fa/advanced/token-management.md)

#### [دسترسی و مجوز - Access](./fa/advanced/access-permissions.md)

#### [بوت و رویدادها](./fa/advanced/boot-and-events.md)

#### [زمان‌بندی - Schedule / Cron](./fa/advanced/schedule.md)

### کار با دیتابیس

#### [شروع به کار](./fa/database/getting-started.md)

#### [کوئری‌بیلدر - Query Builder](./fa/database/query-builder.md)

#### [صفحه‌بندی](./fa/database/pagination.md)

#### [مایگریشن - Migrations](./fa/database/migrations.md)

#### [به‌روزرسانی داده - Patch](./fa/database/patches.md)

### کار با Eloquent ORM

#### [شروع به کار](./fa/eloquent-orm/getting-started.md)

#### [روابط](./fa/eloquent-orm/relationships.md)

#### [مجموعه‌ها](./fa/eloquent-orm/collections.md)

#### [تغییر دهنده ها - Mutators & casts](./fa/eloquent-orm/mutators-casts.md)

#### [منابع API](./fa/eloquent-orm/api-resources.md)

#### [سریال‌سازی](./fa/eloquent-orm/serialization.md)

#### [داده اولیه - Factories](./fa/eloquent-orm/factories.md)

### روش تست

#### [شروع به کار](./fa/test/getting-started.md)

#### [تست HTTP](./fa/test/http-tests.md)

#### [تست Console](./fa/test/console-tests.md)

#### [تست مرورگر](./fa/test/browser-tests.md)

#### [دیتابیس](./fa/test/database.md)

#### [سریال‌سازی](./fa/test/serialization.md)

#### [شبیه‌سازی - Mocking](./fa/test/mocking.md)

### سوالات متداول

#### [مشکلات رایج](./fa/faq/common-issues.md)

#### [تماس با پشتیبانی](./fa/faq/contact-support.md)

---

### سورس کد

**سورس نمونه‌ها:** [docs/source/](./source/) — کد کامل هر walkthrough

راهنمای گام‌به‌گام برای ساخت اپ واقعی — مناسب وقتی مفهوم تئوری را خوانده‌اید و می‌خواهید دست به کد شوید.

---

### ترتیب پیشنهادی مطالعه

1. **معرفی** و **شروع به کار** — اگر تازه با پینوکس آشنا شده‌اید.
2. **نمونه‌های عملی** — یک API و یک سایت ساده را مرحله‌به‌مرحله بسازید.
3. **مفاهیم پایه** — هنگام ساخت روت، کنترلر و ویو.
4. **دیتابیس** و **Eloquent ORM** — برای ذخیره داده.
5. **مفاهیم پیشرفته** — احراز هویت، فایل، Pinker و سرویس‌های مشترک.
6. **روش تست** — قبل از انتشار در محیط واقعی.

کد اپلیکیشن در `apps/{package}/` قرار می‌گیرد. هسته فریمورک در `vendor/pinoox/pincore/` است — منطق اپ را آنجا ننویسید.


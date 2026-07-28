# مستندات پینوکس

مستندات رسمی توسعه اپلیکیشن روی پلتفرم پینوکس (PHP 8.2+، معماری HMVC).

هر راهنما **یک روش پیشنهادی** را با مثال عملی توضیح می‌دهد. از فهرست زیر یا جدول موضوعات شروع کنید.

**زبان‌ها:** [English](../en/README.md) · [فارسی](./README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### معرفی

#### [پینوکس چیست؟](./introduction/what-is-pinoox.md)
#### [ویژگی‌های پینوکس](./introduction/features-pinoox.md)
#### [مشارکت در توسعه پینوکس](./introduction/contributions.md)

### شروع به کار

#### [نصب و راه‌اندازی پینوکس](./start/installing-pinoox.md)
#### [ساخت اولین اپلیکیشن](./start/your-first-app.md)
#### [ساختار پوشه‌بندی](./start/structure.md)
#### [مرجع CLI پینوکس](./start/cli-reference.md)
#### [Pinx CLI (پروژه‌های تک‌اپ)](./start/pinx-cli.md)
#### [مرجع app.php](./start/app-manifest.md)
#### [وابستگی اپ‌ها](./start/app-depends.md)
#### [CLI وابستگی‌ها (`deps`)](./start/deps-cli.md)
#### [قاعده نام‌گذاری پکیج](./start/package-naming.md)

### نمونه‌های عملی

#### [نمونه عملی: اپ API یادداشت](./examples/simple-api-app.md)
#### [نمونه عملی: اپ دفترچه تلفن (وب)](./examples/phonebook-app.md)
#### [نمونه عملی: فرم تماس سایت](./examples/contact-form-app.md)
#### [نمونه عملی: وبلاگ ساده](./examples/blog-app.md)
#### [نمونه عملی: تابلوی کار (Todo)](./examples/task-board-app.md)
#### [نمونه عملی: گالری تصاویر](./examples/gallery-app.md)
#### [نمونه عملی: پنل Vue (SPA)](./examples/vue-spa-app.md)
#### [نمونه عملی: پنل React (SPA)](./examples/react-spa-app.md)
#### [نمونه عملی: Vite hybrid (Twig + ویجت JS)](./examples/vite-hybrid-app.md)

### مفاهیم پایه

#### [روتر (Router)](./basic/routers.md)
#### [کنترلر (Controller)](./basic/controllers.md)
#### [Flow (میان‌افزار)](./basic/flows.md)
#### [Request (درخواست HTTP)](./basic/requests.md)
#### [پاسخ HTTP (Response)](./basic/responses.md)
#### [URL و لینک‌سازی](./basic/url.md)
#### [مسیر فایل (Path)](./basic/path.md)
#### [اعتبارسنجی (Validation)](./basic/validation.md)
#### [ویو (View)](./basic/views.md)
#### [قالب Twig](./basic/templates.md)
#### [کانتکست تم](./basic/theme-contexts.md)
#### [مانیفست تم (`theme.php`)](./basic/theme-manifest.md)
#### [فرانت‌اند و Vite](./basic/frontend-vite.md)
#### [@pinooxhq/vite-plugin](./basic/vite-plugin.md)
#### [Portal (فاساد)](./basic/portal.md)
#### [پیکربندی (Config)](./basic/config.md)
#### [زبان و ترجمه (Lang)](./basic/language.md)
#### [تاریخ و تقویم](./basic/date-and-calendar.md)

### مفاهیم پیشرفته

#### [Pinker و Cache](./advanced/pinker.md)
#### [Patch (به‌روزرسانی داده)](./advanced/patches.md)

#### [سرویس‌های اپ (Component + Portal)](./advanced/services.md)
#### [توابع کمکی سراسری (Helpers)](./advanced/helpers.md)
#### [ارسال ایمیل](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [Rate Limiter](./advanced/rate-limiter.md)
#### [مدیریت کاربران](./advanced/user-management.md)
#### [مدیریت فایل](./advanced/file-management.md)
#### [پروتکل Pinion](./advanced/pinion.md)
#### [مروری بر Pinroll](./advanced/pinroll.md)
#### [مدیریت توکن](./advanced/token-management.md)
#### [دسترسی و مجوز (Access)](./advanced/access-permissions.md)
#### [ترنسپورت (Transport) — منابع مشترک](./advanced/transport.md)
#### [Kernel و pipeline بوت](./advanced/kernel.md)
#### [boot.php و رویدادها](./advanced/boot-and-events.md)
#### [زمان‌بندی (Schedule / Cron)](./advanced/schedule.md)

### دیپلوی (Deploy)

#### [Pinroll — انتشار و دیپلوی](./deploy/pinroll.md)

### کار با دیتابیس

#### [شروع کار با دیتابیس](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [صفحه‌بندی (Pagination)](./database/pagination.md)
#### [Migrations](./database/migrations.md)

### کار با Eloquent ORM

#### [شروع به کار Eloquent ORM](./eloquent-orm/getting-started.md)
#### [روابط Eloquent](./eloquent-orm/relationships.md)
#### [مجموعه‌های Eloquent (Collections)](./eloquent-orm/collections.md)
#### [Mutatorها و Castها](./eloquent-orm/mutators-casts.md)
#### [منابع API (ApiResource)](./eloquent-orm/api-resources.md)
#### [سریال‌سازی Model](./eloquent-orm/serialization.md)
#### [داده آزمایشی — Seeder](./eloquent-orm/factories.md)

### روش تست

#### [شروع تست در پینوکس](./test/getting-started.md)
#### [تست HTTP در پینوکس](./test/http-tests.md)
#### [تست Console در پینوکس](./test/console-tests.md)
#### [تست مرورگر (HTML) در پینوکس](./test/browser-tests.md)
#### [تست دیتابیس در پینوکس](./test/database.md)
#### [تست سریال‌سازی در پینوکس](./test/serialization.md)
#### [Mocking در پینوکس](./test/mocking.md)

### سوالات متداول

#### [مشکلات رایج](./faq/common-issues.md)
#### [تماس با پشتیبانی](./faq/contact-support.md)

---

### سورس کد
**سورس نمونه‌ها:** [docs/source/](../source/) — کد کامل هر walkthrough

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

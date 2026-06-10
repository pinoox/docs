# ویژگی‌های پینوکس

[← بازگشت به فهرست](../../readme-fa.md)

پینوکس ۳.x برای ساخت اکوسیستم PHP ماژولار طراحی شده: چند اپ مستقل روی یک هسته مشترک، CLI برای scaffolding، و ابزارهای آماده برای HTTP، دیتابیس، تم و احراز هویت.

---

## معماری HMVC و اپ مستقل

هر اپ در `apps/{package}/` ساختار MVC کامل دارد:

| لایه | مسیر نمونه |
|------|------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

افزودن یا غیرفعال کردن یک اپ، سایر اپ‌ها را تحت تأثیر قرار نمی‌دهد.

---

## CLI و توسعه سریع

از ریشه پروژه:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI ساختار استاندارد پوشه‌ها، `app.php` و فایل‌های route اولیه را می‌سازد.

---

## سیستم مسیر و Named Action

مسیر URL و handler منطقی از هم جدا می‌شوند:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

این الگو refactor و تست را ساده‌تر می‌کند.

---

## Flow (میان‌افزار)

قبل از رسیدن درخواست به کنترلر، Flow اجرا می‌شود — برای احراز هویت، دسترسی، لاگ و …:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Flow در `app.php` با alias ثبت می‌شود.

---

## View و تم

- قالب Twig در `theme/{themeName}/`
- رندر با `View::render()` یا helper `view()`
- پشتیبانی از SPA با Vite در تم (Vue/React)

---

## دیتابیس و Eloquent

- Query Builder و Eloquent از طریق Portal `DB`
- migration و seeder در `database/migrations/` هر اپ
- پیشوند جدول با نام پکیج (مثلاً `com_acme_blog_posts`)

---

## API و پاسخ JSON

```php
return response()->json(['ok' => true, 'data' => $items]);
```

---

## چندزبانگی

فایل‌های ترجمه در `lang/{locale}/*.lang.php` — مناسب برای اپ‌های فارسی و چندزبانه.

---

## مستندات مرتبط

- [پینوکس چیست؟](./what-is-pinoox.md)
- [نصب و راه‌اندازی](../start/installing-pinoox.md)
- [روتر](../basic/routers.md)
- [فلو — Flow](../basic/flows.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

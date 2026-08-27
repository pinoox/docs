# URL و لینک‌سازی

[← بازگشت به فهرست](../README.md)

در پینوکس ۳.x برای ساخت آدرس‌های داخلی از **`url()`** استفاده کنید. این helper از **`Url::link()`** استفاده می‌کند و از دامنه، مسیر نصب (subfolder) و segment اپ جاری آگاه است.

> از **`Url::get()`** و **`Url::app()`** استفاده نکنید. به‌جای آن **`url()`**، **`Url::link()`** و **`Url::forApp()`** را به‌کار ببرید.

---

## PHP — تابع `url()`

```php
// لینک نسبی به اپ فعال
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// accessor بدون آرگومان
$accessor = url();
echo $accessor->app;               // base URL اپ
echo $accessor->site;              // origin + مسیر پروژه
echo $accessor->api;               // پیشوند API

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // همان url('products')
echo Url::forApp('com_acme_shop'); // URL پایه اپ مشخص
echo Url::current();               // URL صفحه جاری
echo Url::origin();                // https://example.com/pinoox
```

پیشوند `^` یا `~` برای لینک خارج از base اپ:

```php
echo url('^about');                // از ریشه پروژه
echo Url::link('^config/app.php');
```

---

## لینک دانلود فایل ذخیره‌شده

`url()->file()` / `Url::file()` / `file_url()` لینک یک فایل ذخیره‌شده را می‌سازند (`file_id`، `hash_id` یا `FileModel`). دیسک به‌صورت خودکار تشخیص داده می‌شود: دیسک باز/عمومی → URL مستقیم `/storage/…` (یا ریموت)؛ دیسک قفل‌شده → دیسپچر اپ مالک `{app}/file/{hash}`.

```php
echo url()->file($fileId);
echo Url::file($fileId);
echo file_url($fileId);            // همان File::url()

echo url()->fileThumb($fileId);
echo file_thumb($fileId);

echo url()->temporaryFile($fileId, 1800);
echo file_temporary_url($fileId, 1800);
```

```twig
<a href="{{ url().file(invoice.file_id) }}">دانلود</a>
```

جزئیات دیسک و سیاست‌ها: [مدیریت فایل](../advanced/file-management.md).

---

## Twig — accessor `url()`

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        AREA: '{{ url().app }}', {# URL مطلق محیط فعال؛ bootstrap ممکن است path کانتکست را اضافه کند #}
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

`window.__PINOOX__.url` از `pinoox_bootstrap()` همیشه **`AREA`** دارد: URL مطلق محیط UI فعال. با `path` کانتکست تم (مثلاً `panel`)، مقدار `AREA` می‌شود `APP` + آن path (`https://domain.com/panel`) و `BASE` فقط path می‌ماند (`/panel`). [کانتکست تم](./theme-contexts.md) را ببینید.

| متد accessor | کاربرد |
|--------------|--------|
| `url().site` | origin + مسیر پروژه |
| `url().app` | origin + segment اپ |
| `url().api` | پیشوند API (پیش‌فرض `api/v1/`) |
| `url().file($id)` | لینک دانلود فایل ذخیره‌شده (تشخیص خودکار دیسک) |
| `url().fileThumb($id)` | لینک بندانگشتی فایل |
| `url().resource('resources/logo.png')` | فایل استاتیک داخل `apps/{package}/` |
| `url('profile')` | لینک route داخل اپ |

---

## نام route — route()

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## فایل‌های تم — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL فایل در theme فعال
```

---

## مثال منو در کنترلر

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'خانه', 'href' => url('/')],
    ['label' => 'محصولات', 'href' => url('products')],
    ['label' => 'پنل', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## اطلاعات درخواست

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## نکات

- لینک hard-code نکنید؛ همیشه `url()` یا `Url::link()`
- فایل‌های `apps/{package}/resources/` با `url().resource()` یا `asset()`؛ فایل‌های theme با **`assets()`**
- base URL در config دستی نیست؛ از HTTP request تشخیص داده می‌شود

---

## مستندات مرتبط

- [مسیر فایل (Path)](path.md)
- [مدیریت فایل](../advanced/file-management.md)
- [View — ویو](views.md)
- [قالب Twig](templates.md)
- [روتر](routers.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

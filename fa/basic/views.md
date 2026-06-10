# ویو (View)

در پینوکس ۳.x صفحات HTML با موتور **Twig** در پوشه theme رندر می‌شوند. روش استاندارد در کنترلر: **`View::render()`** از Portal.

---

## ساختار theme

```
apps/com_acme_shop/
├── app.php                 # 'theme' => 'default'
└── theme/default/
    ├── main.twig
    ├── layout.twig
    └── pages/
        └── home.twig
```

---

## رندر در کنترلر (استاندارد)

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'title' => 'فروشگاه',
        'products' => ProductModel::latest()->take(6)->get(),
    ]);
}
```

پسوند `.twig` را ننویسید؛ View خودش فایل را پیدا می‌کند.

تابع **`view()`** هم وجود دارد و `View::ready()` را برمی‌گرداند، اما در کنترلر **`View::render()`** را ترجیح دهید:

```php
// معادل کمکی — برای set/exists روی engine
view('pages/home', ['title' => 'فروشگاه']);
return view()->getContentReady();  // نادر
```

---

## خروجی رشته (بدون Response)

```php
$html = render('emails/welcome', ['name' => 'علی']);
// ارسال ایمیل، PDF، …
```

helper **`render()`** مستقیماً `View::render()` را صدا می‌زند.

---

## View::response()

```php
return View::response('pages/home', ['title' => 'خانه']);
```

محتوای Twig را داخل `Response` HTTP برمی‌گرداند.

---

## داده سراسری برای همه ویوها

```php
View::set('siteName', config('app.name'));
// یا
view()->set('siteName', config('app.name'));
```

در Twig:

```twig
<title>{{ siteName }} — {{ title }}</title>
```

---

## SEO (پینوکس ۳.x)

```php
View::shareSeo([
    'title' => 'محصولات',
    'description' => 'لیست محصولات فروشگاه',
    'canonical' => url('products'),
    'image' => assets('dist/og-cover.jpg'),
]);

return View::render('pages/products');
```

در `partials/head.twig`:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — shell با Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

جزئیات در [قالب Twig](templates.md).

---

## بررسی وجود ویو

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## نکات

- منطق business در Controller/Component بماند؛ Twig فقط نمایش
- theme فعال از `app.php` → `'theme'` خوانده می‌شود
- برای JSON خالص از `response()->json()` یا `ApiController` استفاده کنید

---

## مستندات مرتبط

- [قالب Twig](templates.md)
- [URL و assets](url.md)
- [پاسخ HTTP](responses.md)
- [Portal](portal.md)
- [ساختار پروژه](../start/structure.md)

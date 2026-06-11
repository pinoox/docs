# العروض (Views)

[← العودة إلى الفهرس](../README.md)

في Pinoox 3.x تُعرَض صفحات HTML بـ **Twig** في مجلد القالب. الأسلوب المعياري في المتحكمات: **`View::render()`** من Portal.

---

## بنية القالب

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

## العرض في متحكم (معياري)

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'title' => 'Shop',
        'products' => ProductModel::latest()->take(6)->get(),
    ]);
}
```

لا تضمّن امتداد `.twig`؛ View يحل الملف تلقائيًا.

يوجد أيضًا المساعد **`view()`** ويُرجع `View::ready()`، لكن يُفضّل **`View::render()`** في المتحكمات:

```php
// Helper equivalent — mainly for set/exists on the engine
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // rare
```

---

## إخراج نصي (بدون Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

المساعد **`render()`** يستدعي `View::render()` مباشرة.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

يُرجع محتوى Twig داخل `Response` HTTP.

---

## بيانات عامة لكل العروض

```php
View::set('siteName', config('app.name'));
// or
view()->set('siteName', config('app.name'));
```

في Twig:

```twig
<title>{{ siteName }} — {{ title }}</title>
```

---

## SEO (Pinoox 3.x)

```php
View::shareSeo([
    'title' => 'Products',
    'description' => 'Shop product list',
    'canonical' => url('products'),
    'image' => assets('dist/og-cover.jpg'),
]);

return View::render('pages/products');
```

في `partials/head.twig`:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — غلاف مع Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

راجع [قوالب Twig](./templates.md) للتفاصيل.

---

## التحقق من وجود عرض

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## نصائح

- احتفظ بمنطق الأعمال في Controller/Component؛ Twig للعرض فقط
- القالب النشط يأتي من `app.php` → `'theme'`
- لـ JSON خالص استخدم `response()->json()` أو `ApiController`

---

## وثائق ذات صلة

- [قوالب Twig](./templates.md)
- [URL والأصول](./url.md)
- [استجابة HTTP (Response)](./responses.md)
- [Portal](./portal.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)

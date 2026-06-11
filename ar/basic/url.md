# URL وبناء الروابط

[← العودة إلى الفهرس](../README.md)

في Pinoox 3.x استخدم **`url()`** لبناء عناوين URL داخلية. يستخدم هذا المساعد **`Url::link()`** وهو مدرك للنطاق ومسار التثبيت (المجلد الفرعي) وجزء التطبيق الحالي.

> لا تستخدم **`Url::get()`** أو **`Url::app()`**. استخدم **`url()`** و**`Url::link()`** و**`Url::forApp()`** بدلًا منهما.

---

## PHP — مساعد `url()`

```php
// Relative link inside the active app
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// Accessor without arguments
$accessor = url();
echo $accessor->app;               // app base URL
echo $accessor->site;              // origin + project path
echo $accessor->api;               // API prefix

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // same as url('products')
echo Url::forApp('com_acme_shop'); // base URL of a specific app
echo Url::current();               // current page URL
echo Url::origin();                // https://example.com/pinoox
```

البادئة `^` أو `~` لروابط خارج قاعدة التطبيق:

```php
echo url('^about');                // from project root
echo Url::link('^config/app.php');
```

---

## Twig — مُكوّن `url()`

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| دالة المُكوّن | الغرض |
|-----------------|---------|
| `url().site` | origin + مسار المشروع |
| `url().app` | origin + جزء التطبيق |
| `url().api` | بادئة API (افتراضي `api/v1/`) |
| `url().resource('resources/logo.png')` | ملف ثابت تحت `apps/{package}/` |
| `url('profile')` | رابط مسار داخل التطبيق |

---

## اسم المسار — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## أصول القالب — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL of file in the active theme
```

---

## مثال قائمة في متحكم

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## معلومات الطلب

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## نصائح

- لا تثبّت الروابط؛ استخدم دائمًا `url()` أو `Url::link()`
- الملفات في `apps/{package}/resources/` تستخدم `url().resource()` أو `asset()`؛ ملفات القالب تستخدم **`assets()`**
- عنوان URL الأساسي لا يُضبط يدويًا في الإعدادات؛ يُكتشف من طلب HTTP

---

## وثائق ذات صلة

- [مسار الملفات (File Path)](./path.md)
- [العروض (Views)](./views.md)
- [قوالب Twig](./templates.md)
- [المُوجّه (Router)](./routers.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)

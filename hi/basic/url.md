# URL and Link Building

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में internal URLs बनाने के लिए **`url()`** उपयोग करें। यह helper **`Url::link()`** उपयोग करता है और domain, install path (subfolder), और current app segment से aware है।

> **`Url::get()`** या **`Url::app()`** उपयोग न करें। **`url()`**, **`Url::link()`**, और **`Url::forApp()`** उपयोग करें।

---

## PHP — `url()` helper

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

App base के बाहर links के लिए prefix `^` या `~`:

```php
echo url('^about');                // from project root
echo Url::link('^config/app.php');
```

---

## Twig — `url()` accessor

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

| Accessor method | Purpose |
|-----------------|---------|
| `url().site` | origin + project path |
| `url().app` | origin + app segment |
| `url().api` | API prefix (default `api/v1/`) |
| `url().resource('resources/logo.png')` | static file under `apps/{package}/` |
| `url('profile')` | route link inside the app |

---

## Route name — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Theme assets — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL of file in the active theme
```

---

## Controller में menu example

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

## Request information

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Tips

- Links hard-code न करें; हमेशा `url()` या `Url::link()` उपयोग करें
- `apps/{package}/resources/` की files के लिए `url().resource()` या `asset()`; theme files के लिए **`assets()`**
- Base URL config में manually set नहीं होता; HTTP request से detect होता है

---

## संबंधित docs

- [File Path](./path.md)
- [Views](./views.md)
- [Twig Templates](./templates.md)
- [Router](./routers.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

# URL ve bağlantı oluşturma

[← Dizine dön](../README.md)

Pinoox 3.x'te dahili URL'ler oluşturmak için **`url()`** kullanın. Bu helper **`Url::link()`** kullanır ve domain, kurulum yolu (alt klasör) ile mevcut uygulama segmentinin farkındadır.

> **`Url::get()`** veya **`Url::app()`** kullanmayın. Bunun yerine **`url()`**, **`Url::link()`** ve **`Url::forApp()`** kullanın.

---

## PHP — `url()` helper'ı

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

Uygulama tabanı dışındaki bağlantılar için `^` veya `~` öneki:

```php
echo url('^about');                // from project root
echo Url::link('^config/app.php');
```

---

## Twig — `url()` erişimcisi

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

| Erişimci metodu | Amaç |
|-----------------|---------|
| `url().site` | origin + proje yolu |
| `url().app` | origin + uygulama segmenti |
| `url().api` | API öneki (varsayılan `api/v1/`) |
| `url().resource('resources/logo.png')` | `apps/{package}/` altındaki statik dosya |
| `url('profile')` | uygulama içi route bağlantısı |

---

## Route adı — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Tema asset'leri — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL of file in the active theme
```

---

## Controller'da menü örneği

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

## İstek bilgisi

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## İpuçları

- Bağlantıları sabit kodlamayın; her zaman `url()` veya `Url::link()` kullanın
- `apps/{package}/resources/` içindeki dosyalar için `url().resource()` veya `asset()`; tema dosyaları için **`assets()`**
- Temel URL config'de manuel ayarlanmaz; HTTP isteğinden algılanır

---

## İlgili dokümantasyon

- [Dosya yolu](./path.md)
- [View](./views.md)
- [Twig şablonları](./templates.md)
- [Router](./routers.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

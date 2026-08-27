# URL and Link Building

[← Back to index](../README.md)

In Pinoox 3.x use **`url()`** to build internal URLs. This helper uses **`Url::link()`** and is aware of the domain, install path (subfolder), and current app segment.

> Do not use **`Url::get()`** or **`Url::app()`**. Use **`url()`**, **`Url::link()`**, and **`Url::forApp()`** instead.

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

Prefix `^` or `~` for links outside the app base:

```php
echo url('^about');                // from project root
echo Url::link('^config/app.php');
```

---

## Stored file download URL

`url()->file()` / `Url::file()` / `file_url()` resolve a stored file (`file_id`, `hash_id`, or `FileModel`). The disk is detected automatically: unlocked/public disks get a direct `/storage/…` (or remote) URL; locked disks use the owning app dispatcher `{app}/file/{hash}`.

```php
echo url()->file($fileId);
echo Url::file($fileId);
echo file_url($fileId);            // same resolver as File::url()

echo url()->fileThumb($fileId);
echo file_thumb($fileId);

echo url()->temporaryFile($fileId, 1800);
echo file_temporary_url($fileId, 1800);
```

```twig
<a href="{{ url().file(invoice.file_id) }}">Download</a>
```

Full disk and policy details: [File Management](../advanced/file-management.md).

---

## Twig — `url()` accessor

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        AREA: '{{ url().app }}', {# absolute active area; bootstrap may append theme-context path #}
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

`window.__PINOOX__.url` from `pinoox_bootstrap()` always includes **`AREA`**: absolute URL of the active UI area. With a theme-context `path` (e.g. `panel`), `AREA` becomes `APP` + that path (`https://domain.com/panel`) while `BASE` stays path-only (`/panel`). See [Theme contexts](./theme-contexts.md).

| Accessor method | Purpose |
|-----------------|---------|
| `url().site` | origin + project path |
| `url().app` | origin + app segment |
| `url().api` | API prefix (default `api/v1/`) |
| `url().file($id)` | stored file download URL (auto public/private disk) |
| `url().fileThumb($id)` | stored file thumbnail URL |
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

## Menu example in a controller

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

- Do not hard-code links; always use `url()` or `Url::link()`
- Files in `apps/{package}/resources/` use `url().resource()` or `asset()`; theme files use **`assets()`**
- Base URL is not set manually in config; it is detected from the HTTP request

---

## Related docs

- [File Path](./path.md)
- [File Management](../advanced/file-management.md)
- [Views](./views.md)
- [Twig Templates](./templates.md)
- [Router](./routers.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)

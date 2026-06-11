# URL 및 링크 생성

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 내부 URL은 **`url()`**로 생성합니다. 이 helper는 **`Url::link()`**를 사용하며 domain, install path(하위 폴더), 현재 앱 segment를 인식합니다.

> **`Url::get()`** 또는 **`Url::app()`**는 사용하지 마세요. 대신 **`url()`**, **`Url::link()`**, **`Url::forApp()`**를 사용하세요.

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

앱 base 밖 링크에는 `^` 또는 `~` 접두사:

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

## Controller에서 menu 예제

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

## Request 정보

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Tips

- 링크를 하드코딩하지 말고 항상 `url()` 또는 `Url::link()`를 사용하세요
- `apps/{package}/resources/`의 파일은 `url().resource()` 또는 `asset()`; theme 파일은 **`assets()`**
- Base URL은 config에서 수동 설정하지 않습니다; HTTP request에서 감지됩니다

---

## 관련 문서

- [File Path](./path.md)
- [Views](./views.md)
- [Twig Templates](./templates.md)
- [Router](./routers.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)

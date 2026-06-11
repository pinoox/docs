# URL y construcción de enlaces

[← Volver al índice](../README.md)

En Pinoox 3.x usa **`url()`** para construir URLs internas. Este helper utiliza **`Url::link()`** y conoce el dominio, la ruta de instalación (subcarpeta) y el segmento de la app actual.

> No uses **`Url::get()`** ni **`Url::app()`**. Usa **`url()`**, **`Url::link()`** y **`Url::forApp()`** en su lugar.

---

## PHP — helper `url()`

```php
// Enlace relativo dentro de la app activa
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// Accessor sin argumentos
$accessor = url();
echo $accessor->app;               // URL base de la app
echo $accessor->site;              // origen + ruta del proyecto
echo $accessor->api;               // prefijo de la API

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // igual que url('products')
echo Url::forApp('com_acme_shop'); // URL base de una app específica
echo Url::current();               // URL de la página actual
echo Url::origin();                // https://example.com/pinoox
```

Prefija con `^` o `~` para enlaces fuera de la base de la app:

```php
echo url('^about');                // desde la raíz del proyecto
echo Url::link('^config/app.php');
```

---

## Twig — accessor `url()`

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

| Método del accessor | Propósito |
|-----------------|---------|
| `url().site` | origen + ruta del proyecto |
| `url().app` | origen + segmento de la app |
| `url().api` | prefijo de la API (`api/v1/` por defecto) |
| `url().resource('resources/logo.png')` | archivo estático bajo `apps/{package}/` |
| `url('profile')` | enlace de ruta dentro de la app |

---

## Nombre de ruta — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Assets del tema — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL del archivo en el tema activo
```

---

## Ejemplo de menú en un controller

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

## Información de la petición

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Consejos

- No escribas los enlaces a mano; usa siempre `url()` o `Url::link()`
- Los archivos en `apps/{package}/resources/` usan `url().resource()` o `asset()`; los archivos del tema usan **`assets()`**
- La URL base no se configura manualmente en la configuración; se detecta a partir de la petición HTTP

---

## Documentación relacionada

- [Rutas de archivos (Path)](./path.md)
- [Vistas](./views.md)
- [Plantillas Twig](./templates.md)
- [Router](./routers.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)

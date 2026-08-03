# Router

[← Volver al índice](../README.md)

El enrutamiento de Pinoox 3.x tiene dos capas: **Named Actions** (manejadores lógicos) y **Routes** (rutas de URL y métodos HTTP). Cada app define sus rutas en la carpeta **`routes/`** y las registra en **`app.php`**.

> No uses **`Pinoox\Portal\Router::get`**. Importa las funciones del router desde el namespace **`Pinoox\Router`**.

---

## Registrar archivos de rutas en app.php

```php
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## Importar las funciones del router

```php
use function Pinoox\Router\{
    get, post, put, patch, delete, query,
    options, head, purge, trace, connect, any,
    route_match, action, group, collection, routes, collect, route
};
```

Use **`collection()`** to mount nested route files under a path prefix (optional shared flows).

### Route facade (alternative)

```php
use Pinoox\Portal\Route;

Route::get('/', '@welcome')->name('home');
Route::any('/webhook', [WebhookController::class, 'handle'])->name('webhook');
Route::match(['GET', 'POST'], '/form', 'submit')->name('form.submit');
```

> Do **not** use **`Pinoox\Portal\Router::get`** for route definitions. Use **`Pinoox\Router`** helpers or **`Pinoox\Portal\Route`** instead.

---

## Named Actions

Define un manejador una vez en `routes/actions.php`:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## Asignar URLs en web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — referencia a una action registrada
- `{id}` — parámetro dinámico (se pasa al controller o mediante `$request->parametersOne('id')`)

---

## Métodos HTTP

```php
use function Pinoox\Router\{
    get, post, put, patch, delete, query,
    options, head, purge, trace, connect, any, route_match
};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
query('search', [SearchController::class, 'run'])->name('search');

options('/api/preflight', [CorsController::class, 'preflight'])->name('cors.preflight');
head('/status', [HealthController::class, 'head'])->name('health.head');
trace('/debug', [DebugController::class, 'trace'])->name('debug.trace');
connect('/tunnel', [TunnelController::class, 'connect'])->name('tunnel.connect');
purge('/cache/{key}', [CacheController::class, 'purge'])->name('cache.purge');
```

| Helper | HTTP method |
|--------|-------------|
| `get()` | GET |
| `post()` | POST |
| `put()` | PUT |
| `patch()` | PATCH |
| `delete()` | DELETE |
| `query()` | QUERY (Pinoox extension) |
| `options()` | OPTIONS |
| `head()` | HEAD |
| `purge()` | PURGE |
| `trace()` | TRACE |
| `connect()` | CONNECT |

### Match multiple methods

```php
route_match(['GET', 'POST'], '/resource', [ResourceController::class, 'handle'])
    ->name('resource.handle');
```

### Any method (all supported methods)

```php
any('/webhook', [WebhookController::class, 'handle'])->name('webhook');
```

In manifest/config: `'method' => 'any'`, `'all'`, or `'*'`.

---

## Grupos de rutas

Usa `group()` para compartir un prefijo y un Flow en varias rutas:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

Rutas resultantes: `/admin/dashboard`, `/admin/orders`

---

## Flow en una sola ruta

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

El alias correspondiente debe estar registrado en `app.php` bajo `'alias'`.

---

## Archivo de rutas de API — `routes()` y `collect()`

```php
<?php
// routes/api.php

use App\com_acme_shop\Controller\ProductApiController;
use function Pinoox\Router\{collect, get, post, routes};

return routes([
    'version' => 'v1',
    'prefix' => '',
    'routes' => collect(function () {
        get('/products', [ProductApiController::class, 'index'])->name('products.index');
        post('/products', [ProductApiController::class, 'store'])->name('products.store');
        get('/products/{id}', [ProductApiController::class, 'show'])->name('products.show');
    }),
]);
```

`collect()` recopila las rutas dentro de un manifiesto de API. Devuelve el manifiesto final con **`routes([...])`**.

---

## URL a partir del nombre de la ruta

```php
use function Pinoox\Router\route;

echo route('home');                    // URL de la ruta activa
echo route('product.show', ['id' => 5]);
```

---

## Fallback (404)

```php
use Pinoox\Portal\View;
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
```

---

## Selección de la app activa (nivel de proyecto)

Qué app maneja una petición se configura en `config/app-router.config.php` (prefijo de URL) o `config/domain.config.php` (dominio):

```php
// config/app-router.config.php
return [
    '/' => 'com_pinoox_welcome',
    '/shop' => 'com_acme_shop',
];
```

CLI:

```bash
php pinoox app:router set /shop com_acme_shop
```

---

## Documentación relacionada

- [Flow](./flows.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Tu primera app](../start/your-first-app.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)

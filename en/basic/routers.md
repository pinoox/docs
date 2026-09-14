# Router

[← Back to index](../README.md)

Pinoox 3.x routing has two layers: **Named Actions** (logical handlers) and **Routes** (URL paths and HTTP methods). Each app defines its routes in the **`routes/`** folder and registers them in **`app.php`**.

> Do not use **`Pinoox\Portal\Router::get`**. Import router functions from the **`Pinoox\Router`** namespace.

---

## Register route files in app.php

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

## Import router functions

```php
use function Pinoox\Router\{
    get, post, put, patch, delete, query,
    options, head, purge, trace, connect, any,
    route_match, action, group, collection, routes, collect, route
};
```

Use **`collection()`** to mount a nested route file or callback under a path prefix (with optional shared flows). For multi-theme apps, optional `context:` resolves path and `theme.*` flow from `theme-contexts` — see [Theme contexts](./theme-contexts.md) (not required for single-theme apps).

### Route facade (alternative)

Laravel-style syntax — same API as the helpers above:

```php
use Pinoox\Portal\Route;

Route::get('/', '@welcome')->name('home');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::any('/webhook', [WebhookController::class, 'handle'])->name('webhook');
Route::match(['GET', 'POST'], '/form', 'submit')->name('form.submit');
```

> Do **not** use **`Pinoox\Portal\Router::get`** for route definitions — that portal is the runtime router engine. Use **`Pinoox\Router`** helpers or **`Pinoox\Portal\Route`** instead.

---

## Named Actions

Define a handler once in `routes/actions.php`:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## Map URLs in web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — reference to a registered action
- `{id}` — dynamic parameter (`$request->route('id')` or controller injection)

Expressive parameters (optional, catch-all, types, enums, custom patterns) → see [Route Parameters](../advanced/route-parameters.md).

```php
get('/users/{id?:int}', [UserController::class, 'show']);
get('/docs/{path*}', [DocsController::class, 'page']);
get('/orders/{status:pending|paid}', [OrderController::class, 'byStatus']);
```

---

## HTTP methods

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

Register one handler for every supported HTTP method — useful for webhooks, proxies, or catch-all endpoints:

```php
any('/webhook', [WebhookController::class, 'handle'])->name('webhook');
```

In manifest/config entries, use `'method' => 'any'`, `'all'`, or `'*'`.

### QUERY method (RFC 10008)

`QUERY` is a safe, idempotent method with a request body — useful for complex filters or search payloads that do not fit in a URL (like `GET`) and must not change server state (unlike `POST`).

```php
use function Pinoox\Router\query;
use Pinoox\Component\Http\Request;

query('/products/search', [ProductApiController::class, 'search'])
    ->name('products.search');

// Controller — read the body like POST/JSON
public function search(Request $request)
{
    $filters = $request->getPayload()->all();
    // ...
}
```

Manifest form:

```php
[
    'method' => 'QUERY',
    'path' => '/products/search',
    'action' => [ProductApiController::class, 'search'],
    'name' => 'products.search',
]
```

---

## Route groups

Use `group()` for a shared prefix and Flow on multiple routes:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

Resulting paths: `/admin/dashboard`, `/admin/orders`

---

## Flow on a single route

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

The corresponding alias must be registered in `app.php` under `'alias'`.

---

## API route file — `routes()` and `collect()`

```php
<?php
// routes/api.php

use App\com_acme_shop\Controller\ProductApiController;
use function Pinoox\Router\{collect, get, post, query, routes};

return routes([
    'version' => 'v1',
    'prefix' => '',
    'routes' => collect(function () {
        get('/products', [ProductApiController::class, 'index'])->name('products.index');
        post('/products', [ProductApiController::class, 'store'])->name('products.store');
        get('/products/{id}', [ProductApiController::class, 'show'])->name('products.show');
        query('/products/search', [ProductApiController::class, 'search'])->name('products.search');
    }),
]);
```

`collect()` gathers routes inside an API manifest. Return the final manifest with **`routes([...])`**.

---

## URL from route name

```php
use function Pinoox\Router\route;

echo route('home');                    // URL of the active route
echo route('product.show', ['id' => 5]);
```

---

## Fallback (404)

```php
use Pinoox\Portal\View;
use function Pinoox\Router\{fallback, get};

// Preferred API (all methods, Flow-ready):
fallback(fn () => View::render('errors/404'))->name('fallback');

// Legacy equivalent:
get('*', fn () => View::render('errors/404'))->name('fallback');
```

Scoped fallbacks (API / admin) — see [Fallback Routes](../advanced/fallback-routes.md):

```php
group(['prefix' => '/api'], function () {
    fallback(fn () => response()->json(['message' => 'Not Found'], 404));
});
```

---

## Active app selection (project level)

Which app handles a request is configured in `config/app-router.config.php` (URL prefix) or `config/domain.config.php` (domain):

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

## Related docs

- [Flow](./flows.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Your first app](../start/your-first-app.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)

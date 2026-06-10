# Router

[← Back to index](../../readme.md)

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
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

The **`collection()`** function does not exist in pincore 3.x.

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
- `{id}` — dynamic parameter (passed to the controller or via `$request->parametersOne('id')`)

---

## HTTP methods

```php
use function Pinoox\Router\{get, post, put, patch, delete};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
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
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
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

[← Back to index](../../readme.md)

# Router

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x routing में दो layers हैं: **Named Actions** (logical handlers) और **Routes** (URL paths और HTTP methods)। हर app अपने routes **`routes/`** folder में define करती है और **`app.php`** में register करती है।

> **`Pinoox\Portal\Router::get`** उपयोग न करें। Router functions **`Pinoox\Router`** namespace से import करें।

---

## app.php में route files register करें

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

## Router functions import करें

```php
use function Pinoox\Router\{
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

pincore 3.x में **`collection()`** function मौजूद नहीं है।

---

## Named Actions

Handler को `routes/actions.php` में एक बार define करें:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## web.php में URLs map करें

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — registered action का reference
- `{id}` — dynamic parameter (controller को pass या `$request->parametersOne('id')` के ज़रिए)

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

कई routes पर shared prefix और Flow के लिए `group()` उपयोग करें:

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

## Single route पर Flow

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

Corresponding alias `app.php` में `'alias'` के अंतर्गत register होना चाहिए।

---

## API route file — `routes()` और `collect()`

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

`collect()` API manifest के अंदर routes gather करता है। Final manifest **`routes([...])`** से return करें।

---

## Route name से URL

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

कौन सी app request handle करे यह `config/app-router.config.php` (URL prefix) या `config/domain.config.php` (domain) में configure होता है:

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

## संबंधित docs

- [Flow](./flows.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Your first app](../start/your-first-app.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

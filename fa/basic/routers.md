# روتر (Router)

[← بازگشت به فهرست](../../readme-fa.md)

سیستم routing پینوکس ۳.x دو لایه دارد: **Named Action** (handler منطقی) و **Route** (URL و متد HTTP). هر اپ مسیرهای خود را در پوشه **`routes/`** تعریف و در **`app.php`** ثبت می‌کند.

> از **`Pinoox\Portal\Router::get`** استفاده نکنید. توابع router از namespace **`Pinoox\Router`** import می‌شوند.

---

## ثبت فایل‌های route در app.php

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

## import توابع router

```php
use function Pinoox\Router\{
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

تابع **`collection()`** در pincore ۳.x وجود ندارد.

---

## Named Action

Handler را یک‌بار در `routes/actions.php` تعریف کنید:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## اتصال URL در web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` → ارجاع به action ثبت‌شده
- `{id}` → پارامتر داینامیک (به کنترلر یا `$request->parametersOne('id')` می‌رسد)

---

## متدهای HTTP

```php
use function Pinoox\Router\{get, post, put, patch, delete};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
```

---

## گروه‌بندی مسیرها (group)

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

مسیرها: `/admin/dashboard`, `/admin/orders`

---

## Flow روی route

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

alias مربوطه باید در `app.php` → `'alias'` ثبت شده باشد.

---

## فایل API — routes() و collect()

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

`collect()` مسیرهای داخل manifest API را جمع می‌کند. manifest نهایی با **`routes([...])`** برگردانده می‌شود.

---

## URL از نام route

```php
use function Pinoox\Router\route;

echo route('home');                    // URL route فعال
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

## انتخاب اپ فعال (سطح پروژه)

کدام اپ درخواست را handle کند در `config/app-router.config.php` (prefix مسیر) یا `config/domain.config.php` (دامنه) تنظیم می‌شود:

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

## مستندات مرتبط

- [فلو — Flow](./flows.md)
- [کنترلر](./controllers.md)
- [درخواست — Request](./requests.md)
- [ساخت اولین اپ](../start/your-first-app.md)
- [نمونه API](../examples/simple-api-app.md) · [نمونه دفترچه تلفن](../examples/phonebook-app.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

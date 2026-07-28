# روتر (Router)

[← بازگشت به فهرست](../README.md)

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
    get, post, put, patch, delete, query,
    options, head, purge, trace, connect, any,
    route_match, action, group, collection, routes, collect, route
};
```

از **`collection()`** برای mount کردن فایل یا callback تو در تو زیر یک prefix (با flow مشترک اختیاری) استفاده کنید.

### Route facade (جایگزین)

سینتکس شبیه Laravel — همان API توابع بالا:

```php
use Pinoox\Portal\Route;

Route::get('/', '@welcome')->name('home');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::any('/webhook', [WebhookController::class, 'handle'])->name('webhook');
Route::match(['GET', 'POST'], '/form', 'submit')->name('form.submit');
```

> برای **تعریف route** از **`Pinoox\Portal\Router::get`** استفاده نکنید — آن portal موتور runtime روتر است. از helperهای **`Pinoox\Router`** یا **`Pinoox\Portal\Route`** استفاده کنید.

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

| Helper | متد HTTP |
|--------|----------|
| `get()` | GET |
| `post()` | POST |
| `put()` | PUT |
| `patch()` | PATCH |
| `delete()` | DELETE |
| `query()` | QUERY (افزونه پینوکس) |
| `options()` | OPTIONS |
| `head()` | HEAD |
| `purge()` | PURGE |
| `trace()` | TRACE |
| `connect()` | CONNECT |

### چند متد با `route_match`

```php
route_match(['GET', 'POST'], '/resource', [ResourceController::class, 'handle'])
    ->name('resource.handle');
```

### همه متدها با `any`

یک handler برای تمام متدهای HTTP پشتیبانی‌شده — مناسب webhook، proxy یا endpoint عمومی:

```php
any('/webhook', [WebhookController::class, 'handle'])->name('webhook');
```

در manifest/config می‌توانید `'method' => 'any'`، `'all'` یا `'*'` بگذارید.

### متد QUERY (RFC 10008)

`QUERY` یک متد امن و idempotent با body است — مناسب فیلتر/جستجوی پیچیده که در URL جا نمی‌شود (مثل `GET`) و نباید state سرور را عوض کند (برخلاف `POST`).

```php
use function Pinoox\Router\query;
use Pinoox\Component\Http\Request;

query('/products/search', [ProductApiController::class, 'search'])
    ->name('products.search');

// کنترلر — بدنه را مثل POST/JSON بخوانید
public function search(Request $request)
{
    $filters = $request->getPayload()->all();
    // ...
}
```

فرم manifest:

```php
[
    'method' => 'QUERY',
    'path' => '/products/search',
    'action' => [ProductApiController::class, 'search'],
    'name' => 'products.search',
]
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
use function Pinoox\Router\{fallback, get};

// API پیشنهادی (همه متدها + Flow):
fallback(fn () => View::render('errors/404'))->name('fallback');

// معادل قدیمی:
get('*', fn () => View::render('errors/404'))->name('fallback');
```

Fallback محدود به گروه — [Fallback Routes](../advanced/fallback-routes.md):

```php
group(['prefix' => '/api'], function () {
    fallback(fn () => response()->json(['message' => 'Not Found'], 404));
});
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

[← بازگشت به فهرست](../README.md)

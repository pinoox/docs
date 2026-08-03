# Router

[← Dizine dön](../README.md)

Pinoox 3.x routing iki katmana sahiptir: **Named Action'lar** (mantıksal handler'lar) ve **Route'lar** (URL yolları ve HTTP metotları). Her uygulama route'larını **`routes/`** klasöründe tanımlar ve **`app.php`** içinde kaydeder.

> **`Pinoox\Portal\Router::get`** kullanmayın. Router fonksiyonlarını **`Pinoox\Router`** namespace'inden import edin.

---

## Route dosyalarını app.php'de kaydetme

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

## Router fonksiyonlarını import etme

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

## Named Action'lar

Handler'ı `routes/actions.php` içinde bir kez tanımlayın:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## URL'leri web.php'de eşleme

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — kayıtlı bir action'a referans
- `{id}` — dinamik parametre (controller'a veya `$request->parametersOne('id')` ile geçirilir)

---

## HTTP metotları

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

## Route grupları

Birden fazla route için paylaşılan önek ve Flow için `group()` kullanın:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

Ortaya çıkan yollar: `/admin/dashboard`, `/admin/orders`

---

## Tek route'ta Flow

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

İlgili takma ad `app.php` içinde `'alias'` altında kayıtlı olmalıdır.

---

## API route dosyası — `routes()` ve `collect()`

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

`collect()` API manifest'i içindeki route'ları toplar. Son manifest'i **`routes([...])`** ile döndürün.

---

## Route adından URL

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

## Aktif uygulama seçimi (proje düzeyinde)

Hangi uygulamanın isteği işleyeceği `config/app-router.config.php` (URL öneki) veya `config/domain.config.php` (domain) içinde yapılandırılır:

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

## İlgili dokümantasyon

- [Flow](./flows.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [İlk uygulamanız](../start/your-first-app.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

# 路由（Router）

[← 返回索引](../README.md)

Pinoox 3.x 的路由分为两层：**命名 Action**（逻辑处理器）和 **Route**（URL 路径与 HTTP 方法）。每个应用在 **`routes/`** 文件夹中定义路由，并在 **`app.php`** 中注册。

> 不要使用 **`Pinoox\Portal\Router::get`**。请从 **`Pinoox\Router`** 命名空间导入路由函数。

---

## 在 app.php 中注册路由文件

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

## 导入路由函数

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

## 命名 Action

在 `routes/actions.php` 中一次性定义处理器：

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## 在 web.php 中映射 URL

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — 对已注册 Action 的引用
- `{id}` — 动态参数（传递给控制器，或通过 `$request->parametersOne('id')` 获取）

---

## HTTP 方法

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

## 路由分组

使用 `group()` 为多个路由设置共享前缀和 Flow：

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

生成的路径：`/admin/dashboard`、`/admin/orders`

---

## 单个路由上的 Flow

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

对应的别名必须在 `app.php` 的 `'alias'` 中注册。

---

## API 路由文件 — `routes()` 与 `collect()`

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

`collect()` 用于在 API 清单内收集路由。最终的清单通过 **`routes([...])`** 返回。

---

## 通过路由名生成 URL

```php
use function Pinoox\Router\route;

echo route('home');                    // 当前激活路由的 URL
echo route('product.show', ['id' => 5]);
```

---

## 兜底路由（404）

```php
use Pinoox\Portal\View;
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
```

---

## 激活应用的选择（项目级）

由哪个应用处理请求，是在 `config/app-router.config.php`（URL 前缀）或 `config/domain.config.php`（域名）中配置的：

```php
// config/app-router.config.php
return [
    '/' => 'com_pinoox_welcome',
    '/shop' => 'com_acme_shop',
];
```

CLI：

```bash
php pinoox app:router set /shop com_acme_shop
```

---

## 相关文档

- [Flow](./flows.md)
- [控制器（Controllers）](./controllers.md)
- [请求（Request）](./requests.md)
- [你的第一个应用](../start/your-first-app.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

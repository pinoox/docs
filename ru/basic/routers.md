# Роутер (Router)

[← Вернуться к оглавлению](../README.md)

Маршрутизация в Pinoox 3.x имеет два слоя: **именованные действия (Named Actions)** (логические обработчики) и **маршруты (Routes)** (URL-пути и HTTP-методы). Каждое приложение определяет свои маршруты в папке **`routes/`** и регистрирует их в **`app.php`**.

> Не используйте **`Pinoox\Portal\Router::get`**. Импортируйте функции роутера из пространства имён **`Pinoox\Router`**.

---

## Регистрация файлов маршрутов в app.php

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

## Импорт функций роутера

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

## Именованные действия (Named Actions)

Определите обработчик один раз в `routes/actions.php`:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## Привязка URL в web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — ссылка на зарегистрированное действие
- `{id}` — динамический параметр (передаётся в контроллер или через `$request->parametersOne('id')`)

---

## HTTP-методы

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

## Группы маршрутов

Используйте `group()` для общего префикса и Flow на нескольких маршрутах:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

Результирующие пути: `/admin/dashboard`, `/admin/orders`

---

## Flow на отдельном маршруте

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

Соответствующий псевдоним должен быть зарегистрирован в `app.php` в секции `'alias'`.

---

## Файл API-маршрутов — `routes()` и `collect()`

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

`collect()` собирает маршруты внутри API-манифеста. Возвращайте итоговый манифест через **`routes([...])`**.

---

## URL по имени маршрута

```php
use function Pinoox\Router\route;

echo route('home');                    // URL активного маршрута
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

## Выбор активного приложения (на уровне проекта)

Какое приложение обрабатывает запрос, настраивается в `config/app-router.config.php` (префикс URL) или `config/domain.config.php` (домен):

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

## Связанные документы

- [Flow](./flows.md)
- [Контроллеры (Controllers)](./controllers.md)
- [Запрос (Request)](./requests.md)
- [Ваше первое приложение](../start/your-first-app.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)

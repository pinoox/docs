# Router

[← 索引に戻る](../README.md)

Pinoox 3.x のルーティングには 2 つのレイヤーがあります: **Named Actions**（論理ハンドラー）と **Routes**（URL パスと HTTP メソッド）。各アプリは **`routes/`** フォルダでルートを定義し、**`app.php`** に登録します。

> **`Pinoox\Portal\Router::get`** は使用しないでください。Router 関数は **`Pinoox\Router`** 名前空間から import してください。

---

## app.php にルートファイルを登録

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

## Router 関数を import

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

`routes/actions.php` でハンドラーを 1 回定義します。

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## web.php で URL をマッピング

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — 登録済みアクションへの参照
- `{id}` — 動的パラメータ（Controller に渡される、または `$request->parametersOne('id')` 経由）

---

## HTTP メソッド

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

## ルートグループ

複数ルートに共通プレフィックスと Flow を適用するには `group()` を使用します。

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

結果のパス: `/admin/dashboard`、`/admin/orders`

---

## 単一ルートへの Flow

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

対応するエイリアスは `app.php` の `'alias'` に登録する必要があります。

---

## API ルートファイル — `routes()` と `collect()`

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

`collect()` は API マニフェスト内のルートを収集します。最終マニフェストは **`routes([...])`** で返します。

---

## ルート名から URL

```php
use function Pinoox\Router\route;

echo route('home');                    // アクティブルートの URL
echo route('product.show', ['id' => 5]);
```

---

## Fallback（404）

```php
use Pinoox\Portal\View;
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
```

---

## アクティブアプリの選択（プロジェクトレベル）

どのアプリがリクエストを処理するかは、`config/app-router.config.php`（URL プレフィックス）または `config/domain.config.php`（ドメイン）で設定します。

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

## 関連ドキュメント

- [Flow](./flows.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [最初のアプリ](../start/your-first-app.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)

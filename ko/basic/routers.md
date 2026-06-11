# Router

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x routing은 두 계층으로 구성됩니다: **Named Actions**(논리적 handler)와 **Routes**(URL 경로 및 HTTP method). 각 앱은 **`routes/`** 폴더에 route를 정의하고 **`app.php`**에 등록합니다.

> **`Pinoox\Portal\Router::get`**는 사용하지 마세요. **`Pinoox\Router`** namespace에서 router function을 import하세요.

---

## app.php에 route 파일 등록

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

## Router function import

```php
use function Pinoox\Router\{
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

pincore 3.x에는 **`collection()`** function이 없습니다.

---

## Named Actions

`routes/actions.php`에서 handler를 한 번 정의합니다:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## web.php에서 URL 매핑

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — 등록된 action 참조
- `{id}` — 동적 parameter (Controller에 전달되거나 `$request->parametersOne('id')`로 접근)

---

## HTTP method

```php
use function Pinoox\Router\{get, post, put, patch, delete};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
```

---

## Route group

`group()`으로 여러 route에 공유 prefix와 Flow를 적용합니다:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

결과 경로: `/admin/dashboard`, `/admin/orders`

---

## 단일 route에 Flow 적용

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

해당 alias는 `app.php`의 `'alias'`에 등록되어 있어야 합니다.

---

## API route 파일 — `routes()`와 `collect()`

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

`collect()`는 API manifest 내부 route를 수집합니다. 최종 manifest는 **`routes([...])`**로 반환하세요.

---

## Route name으로 URL 생성

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

## 활성 앱 선택 (프로젝트 수준)

어떤 앱이 request를 처리하는지는 `config/app-router.config.php`(URL prefix) 또는 `config/domain.config.php`(domain)에서 설정합니다:

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

## 관련 문서

- [Flow](./flows.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [첫 번째 앱 만들기](../start/your-first-app.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)

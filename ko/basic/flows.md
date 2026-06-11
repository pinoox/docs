# Flow (미들웨어)

[← 색인으로 돌아가기](../README.md)

Flow는 Pinoox의 미들웨어 계층입니다. Controller action 전에 실행됩니다. bootstrapping, 인증, 권한 등 cross-cutting concern에 사용하세요.

---

## 앱 전역 Flow — `before()` method

boot 및 setup(session, 전역 View data 등)에는 **`before(Request $request)`**를 사용하세요:

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Flow\Flow;
use Pinoox\Component\Http\Request;
use Pinoox\Portal\View;

class BootFlow extends Flow
{
    protected function before(Request $request): void
    {
        View::set('siteName', config('app.name'));
    }
}
```

경로: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth Flow — `AuthFlow` 확장

로그인 guard는 **`Pinoox\Flow\AuthFlow`**를 확장하고 **`exit()`**를 구현하세요:

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Router\Route;
use Pinoox\Flow\AuthFlow;
use Pinoox\Portal\Auth;

class ShopAuthFlow extends AuthFlow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }

    protected function exit(Request $request, Route $route)
    {
        return redirect(url('login'));
    }
}
```

사용자가 guest일 때 `AuthFlow`가 `exit()`를 호출합니다. API에서는 JSON 오류를 반환할 수 있습니다:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## `handle()`을 사용하는 Custom Flow

필요하면 **`handle(Request $request, Closure $next)`**를 직접 override하세요:

```php
protected function handle(Request $request, \Closure $next)
{
    if (!$this->userCanAccess($request)) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    return $next($request);
}
```

---

## app.php에 alias 등록

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // runs on every route in the app
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — 앱 전역 Flow (항상 실행)
- **`alias`** — route에서 사용할 짧은 이름

---

## 중첩 alias (manager 패턴)

Flow alias는 그룹화를 위해 중첩할 수 있습니다:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

API manifest에서는 **`manager.auth`** key를 사용합니다:

```php
// routes/api/private.php
[
    'method' => 'GET',
    'uri' => '/user/get',
    'action' => [UserController::class, 'get'],
    'name' => 'user.get',
    'flow' => ['manager.auth'],
],
```

---

## Route에 Flow 적용

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

여러 Flow:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Route group에 Flow 적용

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Chain 중단

Flow가 HTTP response(redirect, error JSON 등)를 반환하면 Controller action은 실행되지 않습니다.

---

## 가이드라인

- Flow는 pincore가 아닌 앱의 `Flow/` 폴더에 둡니다
- alias는 항상 `app.php`에 등록하세요
- 앱 전역 boot: `flow` manifest의 `before()`
- 로그인 guard: `AuthFlow` + `exit()`

---

## 관련 문서

- [Router](./routers.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)

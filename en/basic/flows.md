# Flow (middleware)

[← Back to index](../README.md)

Flow is Pinoox's middleware layer: it runs before the controller action. Use it for bootstrapping, authentication, authorization, and similar cross-cutting concerns.

---

## App-wide Flow — `before()` method

For boot and setup (session, global View data, etc.) use **`before(Request $request)`**:

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

Path: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth Flow — extend `AuthFlow`

For login guards, extend **`Pinoox\Flow\AuthFlow`** and implement **`exit()`**:

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

When the user is a guest, `AuthFlow` calls `exit()`. For APIs you can return a JSON error:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Custom Flow with `handle()`

When needed, override **`handle(Request $request, Closure $next)`** directly:

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

## Register aliases in app.php

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

- **`flow`** — app-wide Flows (always executed)
- **`alias`** — short names for use on routes

---

## Nested aliases (manager pattern)

Flow aliases can be nested for grouping:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

In the API manifest use the key **`manager.auth`**:

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

## Apply Flow to a route

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Multiple Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow on a route group

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Rate limiting (`throttle:…`)

Core alias **`throttle`** accepts a named limiter after a colon (see [Rate Limiter](../advanced/rate-limiter.md)):

```php
post('login', [AuthController::class, 'login'])
    ->flow('throttle:login');

group(['flows' => ['auth', 'throttle:api']], function () {
    // ...
});
```

Define the named limiter with `RateLimiter::define(...)` before requests hit the route.

---

## CORS (`cors:…`)

Core alias **`cors`** accepts a policy name after a colon (see [CORS](../advanced/cors.md)):

```php
group(['flows' => ['cors:api', 'auth']], function () {
    // ...
});
```

Register policies with `Cors::define(...)`. Preflight `OPTIONS` returns **204** without running the controller.

---

## Stopping the chain

If a Flow returns an HTTP response (redirect, error JSON, etc.), the controller action does not run.

---

## Guidelines

- Flows belong in the app's `Flow/` folder — not in pincore
- Always register aliases in `app.php`
- App-wide boot: `before()` in the `flow` manifest
- Login guard: `AuthFlow` + `exit()`

---

## Related docs

- [Router](./routers.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Rate Limiter](../advanced/rate-limiter.md)
- [CORS](../advanced/cors.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)

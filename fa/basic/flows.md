# Flow (میان‌افزار)

[← بازگشت به فهرست](../README.md)

Flow در پینوکس معادل Middleware است: قبل از اجرای action کنترلر اجرا می‌شود. برای boot، احراز هویت، کنترل دسترسی و … استفاده می‌شود.

---

## Flow سراسری — متد before()

برای boot و آماده‌سازی (session، داده سراسری View و …) از **`before(Request $request)`** استفاده کنید:

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

مسیر: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Flow احراز هویت — extend AuthFlow

برای guard ورود، از **`Pinoox\Flow\AuthFlow`** extend کنید و متد **`exit()`** را پیاده کنید:

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

`AuthFlow` در صورت guest بودن کاربر، `exit()` را صدا می‌زند. برای API می‌توانید JSON خطا برگردانید:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'دسترسی مجاز نیست.', status: 401);
}
```

---

## Flow سفارشی با handle()

در صورت نیاز می‌توانید مستقیم **`handle(Request $request, Closure $next)`** را override کنید:

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

## ثبت alias در app.php

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // روی همه routeهای اپ
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — Flow سراسری اپ (همیشه اجرا می‌شود)
- **`alias`** — نام کوتاه برای استفاده در route

---

## alias تو در تو (الگوی manager)

برای گروه‌بندی Flowها، alias می‌تواند nested باشد:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

در manifest API از کلید **`manager.auth`** استفاده می‌شود:

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

## اعمال Flow روی route

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

چند Flow:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow روی گروه route

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## محدودیت نرخ (`throttle:…`)

alias هستهٔ **`throttle`** نام limiter را بعد از دو نقطه می‌گیرد (جزئیات: [Rate Limiter](../advanced/rate-limiter.md)):

```php
post('login', [AuthController::class, 'login'])
    ->flow('throttle:login');

group(['flows' => ['auth', 'throttle:api']], function () {
    // ...
});
```

limiter را با `RateLimiter::define(...)` قبل از رسیدن درخواست تعریف کنید.

---

## CORS (`cors:…`)

alias هستهٔ **`cors`** نام سیاست را بعد از دو نقطه می‌گیرد (جزئیات: [CORS](../advanced/cors.md)):

```php
group(['flows' => ['cors:api', 'auth']], function () {
    // ...
});
```

سیاست‌ها را با `Cors::define(...)` ثبت کنید. preflight با پاسخ **۲۰۴** و بدون اجرای کنترلر انجام می‌شود.

---

## Route Resolver (`resolve`)

alias هستهٔ **`resolve`** bindingهای پارامتر را قبل از کنترلر اجرا می‌کند (جزئیات: [Route Resolver](../advanced/route-resolver.md)):

```php
use Pinoox\Portal\Route;

Route::resolve('user', User::class);

get('users/{user}', [UserController::class, 'show'])
    ->flow(['resolve', 'auth']);
```

---

## توقف زنجیره

اگر Flow پاسخ HTTP برگرداند (redirect، JSON خطا، …)، action کنترلر اجرا نمی‌شود.

---

## نکات

- Flowها در `Flow/` اپ — نه در pincore
- alias را حتماً در `app.php` ثبت کنید
- boot سراسری: `before()` در `flow` manifest
- guard ورود: `AuthFlow` + `exit()`

---

## مستندات مرتبط

- [روتر](./routers.md)
- [کنترلر](./controllers.md)
- [درخواست — Request](./requests.md)
- [Rate Limiter](../advanced/rate-limiter.md)
- [CORS](../advanced/cors.md)
- [Route Resolver](../advanced/route-resolver.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

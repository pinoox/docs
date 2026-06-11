# Flow (middleware)

[← Dizine dön](../README.md)

Flow, Pinoox'un middleware katmanıdır: controller action'ından önce çalışır. Bootstrapping, kimlik doğrulama, yetkilendirme ve benzeri çapraz kesen konular için kullanın.

---

## Uygulama genelinde Flow — `before()` metodu

Boot ve kurulum için (session, global View verisi vb.) **`before(Request $request)`** kullanın:

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

Yol: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth Flow — `AuthFlow`'u genişletme

Giriş korumaları için **`Pinoox\Flow\AuthFlow`**'u genişletin ve **`exit()`** uygulayın:

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

Kullanıcı misafir olduğunda `AuthFlow`, `exit()` çağırır. API'ler için JSON hatası döndürebilirsiniz:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## `handle()` ile özel Flow

Gerektiğinde doğrudan **`handle(Request $request, Closure $next)`** geçersiz kılın:

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

## Takma adları app.php'de kaydetme

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

- **`flow`** — uygulama genelinde Flow'lar (her zaman çalışır)
- **`alias`** — route'larda kullanım için kısa adlar

---

## İç içe takma adlar (manager deseni)

Flow takma adları gruplama için iç içe olabilir:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

API manifest'inde **`manager.auth`** anahtarını kullanın:

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

## Route'a Flow uygulama

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Birden fazla Flow:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Route grubunda Flow

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

---

## Zinciri durdurma

Bir Flow HTTP yanıtı döndürürse (yönlendirme, hata JSON'u vb.), controller action çalışmaz.

---

## Yönergeler

- Flow'lar uygulamanın `Flow/` klasöründe olmalı — pincore'da değil
- Takma adları her zaman `app.php`'de kaydedin
- Uygulama genelinde boot: `flow` manifest'inde `before()`
- Giriş koruması: `AuthFlow` + `exit()`

---

## İlgili dokümantasyon

- [Router](./routers.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

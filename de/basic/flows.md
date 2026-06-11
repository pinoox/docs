# Flow (Middleware)

[← Zurück zur Übersicht](../README.md)

Flow ist die Middleware-Schicht von Pinoox: Sie läuft vor der Controller-Action. Verwenden Sie sie für Bootstrapping, Authentifizierung, Autorisierung und ähnliche Querschnittsaufgaben.

---

## App-weiter Flow — die `before()`-Methode

Für Boot und Setup (Session, globale View-Daten usw.) verwenden Sie **`before(Request $request)`**:

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

Pfad: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth-Flow — `AuthFlow` erweitern

Für Login-Guards erweitern Sie **`Pinoox\Flow\AuthFlow`** und implementieren **`exit()`**:

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

Wenn der Benutzer ein Gast ist, ruft `AuthFlow` `exit()` auf. Für APIs können Sie einen JSON-Fehler zurückgeben:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Eigener Flow mit `handle()`

Bei Bedarf überschreiben Sie **`handle(Request $request, Closure $next)`** direkt:

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

## Aliase in app.php registrieren

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // läuft bei jeder Route der App
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — app-weite Flows (werden immer ausgeführt)
- **`alias`** — Kurznamen zur Verwendung auf Routen

---

## Verschachtelte Aliase (Manager-Muster)

Flow-Aliase können zur Gruppierung verschachtelt werden:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Im API-Manifest verwenden Sie den Schlüssel **`manager.auth`**:

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

## Flow auf eine Route anwenden

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Mehrere Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow auf einer Routengruppe

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Die Kette abbrechen

Wenn ein Flow eine HTTP-Antwort zurückgibt (Redirect, Fehler-JSON usw.), wird die Controller-Action nicht ausgeführt.

---

## Richtlinien

- Flows gehören in den `Flow/`-Ordner der App — nicht in pincore
- Registrieren Sie Aliase immer in `app.php`
- App-weiter Boot: `before()` im `flow`-Manifest
- Login-Guard: `AuthFlow` + `exit()`

---

## Verwandte Dokumente

- [Router](./routers.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zur Übersicht](../README.md)

# Flow (middleware)

[← Volver al índice](../README.md)

Flow es la capa de middleware de Pinoox: se ejecuta antes de la acción del controller. Úsalo para el arranque (bootstrapping), autenticación, autorización y otras preocupaciones transversales similares.

---

## Flow a nivel de app — método `before()`

Para el arranque y la configuración (sesión, datos globales de View, etc.) usa **`before(Request $request)`**:

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

Ruta: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Flow de autenticación — extiende `AuthFlow`

Para guards de inicio de sesión, extiende **`Pinoox\Flow\AuthFlow`** e implementa **`exit()`**:

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

Cuando el usuario es un invitado, `AuthFlow` llama a `exit()`. Para APIs puedes devolver un error JSON:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Flow personalizado con `handle()`

Cuando sea necesario, sobrescribe **`handle(Request $request, Closure $next)`** directamente:

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

## Registrar alias en app.php

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // se ejecuta en cada ruta de la app
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — Flows a nivel de app (siempre se ejecutan)
- **`alias`** — nombres cortos para usar en las rutas

---

## Alias anidados (patrón del manager)

Los alias de Flow pueden anidarse para agruparlos:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

En el manifiesto de la API usa la clave **`manager.auth`**:

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

## Aplicar un Flow a una ruta

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Múltiples Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow en un grupo de rutas

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Detener la cadena

Si un Flow devuelve una respuesta HTTP (redirección, JSON de error, etc.), la acción del controller no se ejecuta.

---

## Pautas

- Los Flows pertenecen a la carpeta `Flow/` de la app — no a pincore
- Registra siempre los alias en `app.php`
- Arranque a nivel de app: `before()` en el manifiesto `flow`
- Guard de inicio de sesión: `AuthFlow` + `exit()`

---

## Documentación relacionada

- [Router](./routers.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)

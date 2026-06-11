# Gestión de usuarios

[← Volver al índice](../README.md)

La autenticación en Pinoox 3.x está centralizada en **pincore**. Las apps solo configuran `auth` y `transport.user` en `app.php` y usan los portales `Auth` y `User` — no dupliques la lógica de login en la app.

---

## Portales y helpers

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## Configuración app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // compartir UserModel con la plataforma
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // nombre cookie / clave SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // o PINOOX_JWT_SECRET en .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| Valor transport | Significado |
|-----------------|---------|
| `platform` | Usuarios compartidos (`pinx_user` / `pincore_user`) |
| `local` | Tabla de usuarios solo de esta app |
| `{package}` | Usar UserModel de otra app |

---

## `Auth::boot()` en BootFlow

```php
<?php
namespace App\com_acme_portal\Flow;

use Pinoox\Component\Flow\Flow;
use Pinoox\Component\Http\Request;
use Pinoox\Portal\Auth;

class BootFlow extends Flow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }
}
```

`Auth::boot()` aplica la configuración `auth` de la app activa al guard. Sin ello, `attempt()` y `check()` pueden no funcionar correctamente.

---

## Login

Los nuevos controllers API extienden `ApiController` (pincore) y usan `ok()` / `fail()`:

```php
<?php

namespace App\com_acme_portal\Controller;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;
use Pinoox\Portal\Auth;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        Auth::boot();

        if (!Auth::guest()) {
            return $this->fail('ACCESS_DENIED', 'user.already_logged_in', status: 401);
        }

        $input = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $result = Auth::attemptResult([
            'username' => $input['username'],
            'password' => $input['password'],
        ], remember: (bool) ($input['remember'] ?? false));

        if (!$result->success) {
            return $this->fail('ACCESS_DENIED', $result->message ?? 'user.invalid_credentials');
        }

        return $this->ok(['token' => $result->token], 'user.logged_in_successfully');
    }
}
```

La app del sistema `com_pinoox_manager` usa la misma lógica pero llama a `$this->error()` y `$this->message()` (clase base legacy) — ambos mapean al sobre JSON estándar.

Campos de credencial permitidos: `username`, `email` o `login`.

---

## Proteger rutas con Flow

### API — manifiesto en `routes/api/*.php`

```php
return [
    [
        'method' => 'GET',
        'uri' => '/orders',
        'action' => [OrderController::class, 'index'],
        'name' => 'orders.index',
        'flow' => ['auth'],
    ],
];
```

Para un alias agrupado (como manager):

```php
// app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],

// routes/api/private.php
'flow' => ['manager.auth'],
```

### Web — `group` con flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` detiene la acción cuando `Auth::guest()` es true. Para APIs puedes sobrescribir `exit()`:

```php
class ManagerAuthFlow extends AuthFlow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }

    protected function exit(Request $request, Route $route)
    {
        return ApiResponse::error('ACCESS_DENIED', 'Access denied!', status: 401);
    }
}
```

---

## Métodos API comunes

| Método | Propósito |
|--------|---------|
| `Auth::check()` / `guest()` | Estado de sesión |
| `Auth::user()` / `id()` | Usuario actual |
| `Auth::profile()` | DTO de perfil para API |
| `Auth::updateProfile($id, $data)` | Actualizar nombre/email |
| `Auth::changePassword($id, $old, $new)` | Cambiar contraseña |
| `Auth::create($data)` / `remove($id)` | CRUD de usuario |
| `Auth::revokeSessions($userId)` | Revocar todos los tokens |

---

## Eventos

| Evento | Nombre |
|-------|------|
| `UserAuthenticated` | `user.authenticated` |
| `UserLoggedOut` | `user.logged_out` |
| `UserLoginFailed` | `user.login_failed` |

```php
use Pinoox\Portal\Event;
use Pinoox\Component\User\Event\UserAuthenticated;

Event::listen(UserAuthenticated::$eventName, function (UserAuthenticated $event) {
    // $event->user
});
```

---

## Arquitectura

```
app.php (auth + transport.user)
        ↓
Portal.Auth / Portal.User
        ↓
Manager → Guard → AuthSession (cookie/session/jwt)
        ↓
UserModel (pincore_user) + TokenModel (pincore_token)
```

---

## Consejos HMVC

1. La lógica de auth vive en pincore — no la dupliques en apps.
2. Llama a `Auth::boot()` globalmente en BootFlow.
3. Protege rutas con alias Flow (`->flows()` o `'flow'` en manifiesto API), no `if (!isLogin())` en cada controller.
4. Para apps consumidoras, `'transport' => ['user' => 'platform']` suele ser suficiente.

---

## Documentación relacionada

- [Gestión de tokens](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← Volver al índice](../README.md)

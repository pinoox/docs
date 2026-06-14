# User Management

[← Back to index](../README.md)

Authentication in Pinoox 3.x is centralized in **pincore**. Apps only configure `auth` and `transport.user` in `app.php` and use the `Auth` and `User` portals — do not duplicate login logic in the app.

---

## Portals and helpers

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## app.php configuration

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // share UserModel with the platform
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // cookie name / SPA key
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // or PINOOX_JWT_SECRET in .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| transport value | Meaning |
|-----------------|---------|
| `platform` | Shared users (`pinx_user` / `pincore_user`) |
| `local` | User table scoped to this app only |
| `{package}` | Use another app's UserModel |

---

## `Auth::boot()` in BootFlow

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

`Auth::boot()` applies the active app's `auth` settings to the guard. Without it, `attempt()` and `check()` may not work correctly.

---

## Login

New API controllers extend `ApiController` (pincore) and use `ok()` / `fail()`:

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

The system app `com_pinoox_manager` uses the same logic but calls `$this->error()` and `$this->message()` (legacy base class) — both map to the standard JSON envelope.

Allowed credential fields: `username`, `email`, or `login`.

---

## Protect routes with Flow

### API — manifest in `routes/api/*.php`

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

For a grouped alias (like manager):

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

### Web — `group` with flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` stops the action when `Auth::guest()` is true. For APIs you can override `exit()`:

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

## Common API methods

| Method | Purpose |
|--------|---------|
| `Auth::check()` / `guest()` | Login state |
| `Auth::user()` / `id()` | Current user |
| `Auth::profile()` | Profile DTO for API |
| `Auth::updateProfile($id, $data)` | Update name/email |
| `Auth::changePassword($id, $old, $new)` | Change password |
| `Auth::create($data)` / `remove($id)` | User CRUD |
| `Auth::revokeSessions($userId)` | Revoke all tokens |

---

## Events

| Event | Name |
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

## Architecture

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

## HMVC tips

1. Auth logic lives in pincore — do not duplicate it in apps.
2. Call `Auth::boot()` globally in BootFlow.
3. Protect routes with Flow aliases (`->flows()` or `'flow'` in API manifest), not `if (!isLogin())` in every controller.
4. For consumer apps, `'transport' => ['user' => 'platform']` is often enough.

---

## CLI (terminal)

Manage users from the project root. Commands use the app's `transport.user` scope (pick `platform` or an app package when prompted).

| Command | Purpose |
|---------|---------|
| `user:list {package}` | List users; `--status=active`, `--json` |
| `user:show {user}` | Show one user (id, username, or email) |
| `user:create` | Create user (options or interactive) |
| `user:update {user}` | Update profile (`--set fname=Ali`, `--meta theme=dark`) |
| `user:delete {user}` | Remove user |
| `user:password {user}` | Set password |
| `user:status {user}` | Change status (`active`, `inactive`, `suspend`, `pending`) |
| `user:role {user}` | Attach or detach roles |

Alias: `users` → `user:list`.

```bash
php pinoox user:list com_my_shop --json
php pinoox user:create com_my_shop --username=demo --email=demo@example.test
php pinoox user:password 1 --password=secret
php pinoox user:role 1 --attach=editor
```

Full list: [CLI reference](../start/cli-reference.md).

---

## Related docs

- [Token management](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← Back to index](../README.md)

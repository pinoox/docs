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

API controllers extend `ApiController` (pincore) and use `ok()` / `fail()`:

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
            return $this->fail('ALREADY_AUTHENTICATED', 'Already logged in', status: 401);
        }

        $input = $this->validated($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $result = Auth::attemptResult([
            'username' => $input['username'],
            'password' => $input['password'],
        ], remember: (bool) ($input['remember'] ?? false));

        if (!$result->success) {
            return $this->fail(
                'INVALID_CREDENTIALS',
                $result->message ?? 'Invalid username or password',
                status: 401,
            );
        }

        return $this->ok([
            'token' => $result->token,
            'user' => Auth::clientUser($result->user),
        ], 'Logged in successfully');
    }

    public function get()
    {
        Auth::boot();

        if (!Auth::check()) {
            return $this->fail('UNAUTHORIZED', 'You must login', status: 401);
        }

        return $this->ok(Auth::clientUser());
    }

    public function logout()
    {
        Auth::boot();
        Auth::logout();

        return $this->ok(null, 'Logged out');
    }
}
```

Do **not** rebuild the user payload by hand in each app. Use `Auth::clientUser()`.

The system app `com_pinoox_manager` uses the same login flow but may call `$this->error()` / `$this->message()` (legacy base) — both map to the standard JSON envelope. Prefer `ALREADY_AUTHENTICATED` as the error code when the server session is already active so `@pinooxhq/auth` can recover (logout cookie + retry login).

Allowed credential fields: `username`, `email`, or `login`.

---

## `Auth::clientUser()` — SPA user envelope

Stable shape for login / `me` responses consumed by `@pinooxhq/auth` and themes:

```php
Auth::clientUser(?UserModel $user = null): ?array
```

| Field | Notes |
|-------|--------|
| `id` / `user_id` | Same numeric id |
| `name` | Full name, else username |
| `username`, `email`, `fname`, `lname`, `mobile` | Profile fields |
| `group_key`, `status` | Access / account state |
| `abilities` | From `Access::abilitiesFor($user)` |
| `avatar` / `avatar_url` | Thumb + full URL |

```php
// After login
'user' => Auth::clientUser($result->user)

// Current session (me)
Auth::clientUser()          // uses Auth::user()
Auth::clientUser($model)    // explicit UserModel
```

Use **`Auth::profile()`** for panel / lock-screen DTOs (avatar focus, lock state). Use **`Auth::clientUser()`** for SPA auth contracts (`token` + `user` / `me`).

---

## SPA client — `@pinooxhq/auth`

Independent apps configure `auth` (and optional `auth.client`) in `app.php`, then create auth once in the theme:

```js
import { createAuth, createHttp } from '@pinooxhq/auth'

export const auth = createAuth({
  strategy: 'local',
  mode: 'jwt',
  key: 'acme_pinoox',           // must match app.php auth.key
  loginUrl: '/acme/login',
  endpoints: {
    login: 'auth/login',
    logout: 'auth/logout',
    me: 'auth/get',
  },
})
```

### HttpOnly JWT cookie vs localStorage

In `jwt` mode the server stores the JWT in an **HttpOnly** cookie named `auth.key`. JavaScript cannot read that cookie. `@pinooxhq/auth` therefore:

1. Keeps a copy in `localStorage` when login returns `token` (for `Authorization: Bearer …`).
2. Always calls `me()` with `credentials: 'include'` — even when local storage is empty — so a valid HttpOnly cookie still hydrates the session.
3. If `login` returns `ALREADY_AUTHENTICATED` (cookie alive, SPA storage empty), clears the server session via `logout` and **retries** the login once with the submitted credentials.

HTTP clients should send cookies on same-origin API calls:

```js
axios.create({ withCredentials: true })
```

Do not add per-app “resume session” hacks in controllers or `main.js` — keep AuthController thin and let `@pinooxhq/auth` own cookie ↔ storage sync.

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
| `Auth::user()` / `id()` | Current user model / id |
| `Auth::clientUser($user = null)` | SPA envelope (`id`, `name`, `abilities`, avatar…) |
| `Auth::profile()` | Panel / lock-screen profile DTO |
| `Auth::token()` | Current JWT (after login / setToken) |
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

1. Auth logic lives in pincore — do not duplicate user payloads or resume hacks in apps.
2. Call `Auth::boot()` globally in BootFlow.
3. Protect routes with Flow aliases (`->flows()` or `'flow'` in API manifest), not `if (!isLogin())` in every controller.
4. For consumer apps, `'transport' => ['user' => 'platform']` is often enough.
5. SPA login/me: return `token` + `Auth::clientUser()`; use `@pinooxhq/auth` for storage and cookie hydrate.

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
| `user:login` | Issue a session/JWT token (see below) |
| `user:logout` | End the current auth session |

Alias: `users` → `user:list`.

```bash
php pinoox user:list com_my_shop --json
php pinoox user:create com_my_shop --username=demo --email=demo@example.test
php pinoox user:password 1 --password=secret
php pinoox user:role 1 --attach=editor
php pinoox user:login com_my_shop --id=1
php pinoox user:login --id=1 --force
php pinoox user:logout --force
```

Full list: [CLI reference](../start/cli-reference.md).

---

## Local / dev auto-login (`.env`)

Two optional env keys help during local development. They are **independent** of normal cookie / JWT / session client auth.

| Key | Who sets it | Purpose |
|-----|-------------|---------|
| `PINOOX_LOGIN` | **You** (manual) | Declarative auto-login: resolve a user by field and force them for that app |
| `PINOOX_LOGIN_TOKEN` | CLI with `--force` | Store the token from `user:login` for server-side request auth |

### `PINOOX_LOGIN` (manual)

Format: `package:field:value`

Supported fields: `id`, `user_id`, `personal_id`, `username`, `email`, `login`, `mobile`.

```env
PINOOX_LOGIN=com_pinoox_manager:id:1
PINOOX_LOGIN=com_pinoox_account:username:yoosef
PINOOX_LOGIN=com_pinoox_shop:mobile:09122220000
```

Rules:

- Multiple lines are allowed (one per app). The active app package selects the matching line.
- Empty / `null` values are ignored (e.g. `…:mobile:` never logs anyone in).
- Duplicate field values pick the first row by `user_id`.
- CLI **does not** write this key. Edit `.env` yourself when you want it.

### `PINOOX_LOGIN_TOKEN` (CLI token)

`user:login` always prints a token for browser apply / Inspector. With `--force`, it also writes that token to `.env`:

```bash
php pinoox user:login com_pinoox_manager --id=1 --force
# → PINOOX_LOGIN_TOKEN=<token>

php pinoox user:logout --force
# → removes PINOOX_LOGIN_TOKEN
```

Without `--force`, `.env` is left unchanged. `PINOOX_LOGIN` is never modified by these commands.

Pinx / Inspector “Login as user” uses the same token flow and typically passes `--force` so local auto-login stays in sync.

See also `.env.example` in the project root.

---

## Related docs

- [Token management](./token-management.md)
- [Access & permissions](./access-permissions.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)
- Package: [`@pinooxhq/auth`](https://github.com/pinoox/auth)

---

[← Back to index](../README.md)

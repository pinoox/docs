# User Management

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x 인증은 **pincore**에 중앙화됩니다. 앱은 `app.php`에서 `auth`와 `transport.user`만 설정하고 `Auth`, `User` portal 사용 — 앱에 login logic 중복 금지.

---

## Portal과 helper

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

## BootFlow의 `Auth::boot()`

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

`Auth::boot()`가 활성 앱의 `auth` 설정을 guard에 적용. 없으면 `attempt()`와 `check()`가 올바르게 동작하지 않을 수 있음.

---

## Login

새 API Controller는 `ApiController`(pincore)를 확장하고 `ok()` / `fail()` 사용:

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

시스템 앱 `com_pinoox_manager`는 동일 logic이지만 `$this->error()`와 `$this->message()` 호출(legacy base class) — 둘 다 표준 JSON envelope에 매핑.

허용 credential field: `username`, `email`, 또는 `login`.

---

## Flow로 route 보호

### API — `routes/api/*.php`의 manifest

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

Grouped alias(manager 등):

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

### Web — flows가 있는 `group`

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow`는 `Auth::guest()`일 때 action 중단. API에서는 `exit()` override:

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

## 자주 쓰는 API method

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

## Event

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

1. Auth logic은 pincore에 — 앱에 중복 금지.
2. BootFlow에서 전역 `Auth::boot()` 호출.
3. route 보호는 Flow alias(`->flows()` 또는 API manifest의 `'flow'`), Controller마다 `if (!isLogin())` 아님.
4. Consumer app에는 `'transport' => ['user' => 'platform']`만으로 충분한 경우 많음.

---

## 관련 문서

- [Token management](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← 색인으로 돌아가기](../README.md)

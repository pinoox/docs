# User Management

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x में authentication **pincore** में centralized है। Apps केवल `app.php` में `auth` और `transport.user` configure करती हैं और `Auth` तथा `User` portals का उपयोग करती हैं — app में login logic duplicate न करें।

---

## Portals और helpers

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

| transport value | अर्थ |
|-----------------|------|
| `platform` | Shared users (`pinx_user` / `pincore_user`) |
| `local` | User table scoped to this app only |
| `{package}` | Use another app's UserModel |

---

## BootFlow में `Auth::boot()`

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

`Auth::boot()` active app की `auth` settings guard पर लागू करता है। इसके बिना `attempt()` और `check()` सही काम नहीं कर सकते।

---

## Login

नए API controllers `ApiController` (pincore) extend करते हैं और `ok()` / `fail()` का उपयोग करते हैं:

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

System app `com_pinoox_manager` वही logic उपयोग करती है लेकिन `$this->error()` और `$this->message()` (legacy base class) call करती है — दोनों standard JSON envelope में map होते हैं।

अनुमत credential fields: `username`, `email`, या `login`।

---

## Flow से routes protect करें

### API — `routes/api/*.php` में manifest

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

Grouped alias के लिए (manager जैसा):

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

### Web — flows के साथ `group`

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` action रोक देता है जब `Auth::guest()` true हो। APIs के लिए `exit()` override कर सकते हैं:

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

## सामान्य API methods

| Method | उद्देश्य |
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

1. Auth logic pincore में रहती है — apps में duplicate न करें।
2. BootFlow में globally `Auth::boot()` call करें।
3. Routes को Flow aliases से protect करें (`->flows()` या API manifest में `'flow'`), हर controller में `if (!isLogin())` न लिखें।
4. Consumer apps के लिए `'transport' => ['user' => 'platform']` अक्सर पर्याप्त है।

---

## संबंधित docs

- [Token management](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

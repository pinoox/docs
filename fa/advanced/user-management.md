# مدیریت کاربران

[← بازگشت به فهرست](../../readme-fa.md)

احراز هویت در پینوکس 3.x در **pincore** متمرکز است. اپ‌ها فقط `auth` و `transport.user` را در `app.php` تنظیم می‌کنند و از Portalهای `Auth` و `User` استفاده می‌کنند — منطق لاگین را در اپ تکرار نکنید.

---

## Portalها و Helperها

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## تنظیم app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // اشتراک UserModel با پلتفرم
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // نام cookie / کلید SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // یا PINOOX_JWT_SECRET در .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| مقدار transport | معنی |
|-----------------|------|
| `platform` | کاربران مشترک (`pinx_user` / `pincore_user`) |
| `local` | جدول کاربر فقط برای این اپ |
| `{package}` | استفاده از UserModel اپ دیگر |

---

## Auth::boot() در BootFlow

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

`Auth::boot()` تنظیمات `auth` اپ فعال را روی Guard اعمال می‌کند. بدون آن، `attempt()` و `check()` ممکن است درست کار نکنند.

---

## ورود (Login)

کنترلر API از `ApiController` (pincore) و متدهای `ok()` / `fail()` استفاده می‌کند:

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

اپ سیستمی `com_pinoox_manager` همان منطق را دارد اما از `$this->error()` و `$this->message()` (کلاس پایه legacy) استفاده می‌کند — هر دو به envelope استاندارد JSON map می‌شوند.

فیلدهای مجاز credentials: `username`, `email`, یا `login`.

---

## محافظت route با Flow

### API — manifest در `routes/api/*.php`

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

برای alias گروهی (مثل manager):

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

### Web — `group` با flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` وقتی `Auth::guest()` باشد، اجرای اکشن را متوقف می‌کند. برای API می‌توانید `exit()` را override کنید:

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

## APIهای پرکاربرد

| متد | کاربرد |
|-----|--------|
| `Auth::check()` / `guest()` | وضعیت ورود |
| `Auth::user()` / `id()` | کاربر جاری |
| `Auth::profile()` | DTO پروفایل برای API |
| `Auth::updateProfile($id, $data)` | ویرایش نام/ایمیل |
| `Auth::changePassword($id, $old, $new)` | تغییر رمز |
| `Auth::create($data)` / `remove($id)` | CRUD کاربر |
| `Auth::revokeSessions($userId)` | ابطال همه توکن‌ها |

---

## رویدادها

| رویداد | نام |
|--------|-----|
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

## معماری

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

## نکات HMVC

1. منطق Auth در pincore است — در اپ duplicate نکنید.
2. `Auth::boot()` را در BootFlow سراسری صدا بزنید.
3. محافظت route با Flow alias (`->flows()` یا `'flow'` در manifest API)، نه `if (!isLogin())` در هر کنترلر.
4. برای اپ‌های مصرف‌کننده فقط `'transport' => ['user' => 'platform']` کافی است.

---

## مستندات مرتبط

- [مدیریت توکن](./token-management.md)
- [فلو — Flow](../basic/flows.md)
- [Transport — حمل API](../../pinoox%20docs/pinoox-transport.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

# إدارة المستخدمين

[← العودة إلى الفهرس](../README.md)

المصادقة (Authentication) في Pinoox 3.x مركزية في **pincore**. تكتفي التطبيقات بضبط `auth` و `transport.user` في `app.php` واستخدام بوابتي `Auth` و `User` — لا تكرّر منطق تسجيل الدخول داخل التطبيق.

---

## البوابات (Portals) والدوال المساعدة

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## إعدادات app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // مشاركة UserModel مع المنصة
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // اسم الكوكيز / مفتاح SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // أو PINOOX_JWT_SECRET في .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| قيمة transport | المعنى |
|-----------------|---------|
| `platform` | مستخدمون مشتركون (`pinx_user` / `pincore_user`) |
| `local` | جدول مستخدمين خاص بهذا التطبيق فقط |
| `{package}` | استخدام UserModel الخاص بتطبيق آخر |

---

## `Auth::boot()` في BootFlow

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

تطبّق `Auth::boot()` إعدادات `auth` الخاصة بالتطبيق النشط على الحارس (Guard). بدونها، قد لا تعمل `attempt()` و `check()` بشكل صحيح.

---

## تسجيل الدخول

ترث متحكمات API الجديدة من `ApiController` (في pincore) وتستخدم `ok()` / `fail()`:

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

يستخدم تطبيق النظام `com_pinoox_manager` المنطق نفسه لكنه يستدعي `$this->error()` و `$this->message()` (صنف أساس قديم) — وكلاهما يُحوَّل إلى غلاف JSON القياسي.

حقول الاعتماد المسموح بها: `username` أو `email` أو `login`.

---

## حماية المسارات بالتدفق (Flow)

### API — البيان في `routes/api/*.php`

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

لاسم بديل مجمَّع (كما في manager):

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

### الويب — `group` مع التدفقات

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

يوقف `Pinoox\Flow\AuthFlow` تنفيذ الإجراء عندما تكون `Auth::guest()` بقيمة true. ولواجهات API يمكنك تجاوز `exit()`:

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

## دوال API الشائعة

| الدالة | الغرض |
|--------|---------|
| `Auth::check()` / `guest()` | حالة تسجيل الدخول |
| `Auth::user()` / `id()` | المستخدم الحالي |
| `Auth::profile()` | كائن DTO للملف الشخصي عبر API |
| `Auth::updateProfile($id, $data)` | تحديث الاسم/البريد الإلكتروني |
| `Auth::changePassword($id, $old, $new)` | تغيير كلمة المرور |
| `Auth::create($data)` / `remove($id)` | عمليات CRUD للمستخدمين |
| `Auth::revokeSessions($userId)` | إلغاء جميع الرموز |

---

## الأحداث (Events)

| الحدث | الاسم |
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

## المعمارية

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

## نصائح HMVC

1. منطق المصادقة يوجد في pincore — لا تكرّره في التطبيقات.
2. استدعِ `Auth::boot()` بشكل عام في BootFlow.
3. احمِ المسارات بأسماء التدفقات البديلة (`->flows()` أو `'flow'` في بيان API)، وليس بـ `if (!isLogin())` في كل متحكم.
4. للتطبيقات المستهلكة، غالباً ما يكفي `'transport' => ['user' => 'platform']`.

---

## وثائق ذات صلة

- [إدارة الرموز (Tokens)](./token-management.md)
- [التدفقات (Flows)](../basic/flows.md)
- [النقل (Transport)](./transport.md)

---

[← العودة إلى الفهرس](../README.md)

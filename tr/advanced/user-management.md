# Kullanıcı yönetimi

[← Dizine dön](../README.md)

Pinoox 3.x'te kimlik doğrulama **pincore** içinde merkezileştirilmiştir. Uygulamalar yalnızca `app.php` içinde `auth` ve `transport.user` yapılandırır ve `Auth` ile `User` portal'larını kullanır — giriş mantığını uygulamada çoğaltmayın.

---

## Portal'lar ve helper'lar

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## app.php yapılandırması

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

| transport değeri | Anlam |
|-----------------|---------|
| `platform` | Paylaşımlı kullanıcılar (`pinx_user` / `pincore_user`) |
| `local` | Yalnızca bu uygulamaya özel kullanıcı tablosu |
| `{package}` | Başka bir uygulamanın UserModel'ini kullan |

---

## BootFlow'da `Auth::boot()`

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

`Auth::boot()`, aktif uygulamanın `auth` ayarlarını guard'a uygular. Olmadan `attempt()` ve `check()` doğru çalışmayabilir.

---

## Giriş

Yeni API controller'ları `ApiController`'ı (pincore) genişletir ve `ok()` / `fail()` kullanır:

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

Sistem uygulaması `com_pinoox_manager` aynı mantığı kullanır ancak `$this->error()` ve `$this->message()` çağırır (eski temel sınıf) — ikisi de standart JSON zarfına eşlenir.

İzin verilen kimlik bilgisi alanları: `username`, `email` veya `login`.

---

## Route'ları Flow ile koruma

### API — `routes/api/*.php` içinde manifest

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

Gruplandırılmış takma ad için (manager gibi):

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

### Web — flow'lu `group`

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow`, `Auth::guest()` true olduğunda action'ı durdurur. API'ler için `exit()` geçersiz kılabilirsiniz:

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

## Yaygın API metotları

| Metot | Amaç |
|--------|---------|
| `Auth::check()` / `guest()` | Giriş durumu |
| `Auth::user()` / `id()` | Mevcut kullanıcı |
| `Auth::profile()` | API için profil DTO |
| `Auth::updateProfile($id, $data)` | Ad/e-posta güncelle |
| `Auth::changePassword($id, $old, $new)` | Şifre değiştir |
| `Auth::create($data)` / `remove($id)` | Kullanıcı CRUD |
| `Auth::revokeSessions($userId)` | Tüm token'ları iptal et |

---

## Event'ler

| Event | Ad |
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

## Mimari

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

## HMVC ipuçları

1. Auth mantığı pincore'da — uygulamalarda çoğaltmayın.
2. `Auth::boot()`'u BootFlow'da global olarak çağırın.
3. Route'ları Flow takma adlarıyla koruyun (`->flows()` veya API manifest'inde `'flow'`), her controller'da `if (!isLogin())` değil.
4. Tüketici uygulamalar için `'transport' => ['user' => 'platform']` çoğu zaman yeterlidir.

---

## İlgili dokümantasyon

- [Token yönetimi](./token-management.md)
- [Flow'lar](../basic/flows.md)
- [Transport](./transport.md)

---

[← Dizine dön](../README.md)

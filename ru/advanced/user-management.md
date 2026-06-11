# Управление пользователями

[← Вернуться к оглавлению](../README.md)

Аутентификация в Pinoox 3.x централизована в **pincore**. Приложения только настраивают `auth` и `transport.user` в `app.php` и используют порталы `Auth` и `User` — не дублируйте логику входа в приложении.

---

## Порталы и хелперы

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## Конфигурация app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // общий UserModel с платформой
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // имя cookie / ключ SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // или PINOOX_JWT_SECRET в .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| Значение transport | Значение |
|-----------------|---------|
| `platform` | Общие пользователи (`pinx_user` / `pincore_user`) |
| `local` | Таблица пользователей только для этого приложения |
| `{package}` | UserModel другого приложения |

---

## `Auth::boot()` в BootFlow

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

`Auth::boot()` применяет настройки `auth` активного приложения к guard. Без этого `attempt()` и `check()` могут работать некорректно.

---

## Вход

Новые API-контроллеры наследуют `ApiController` (pincore) и используют `ok()` / `fail()`:

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

Системное приложение `com_pinoox_manager` использует ту же логику, но вызывает `$this->error()` и `$this->message()` (устаревший базовый класс) — оба варианта формируют стандартную JSON-обёртку.

Допустимые поля учётных данных: `username`, `email` или `login`.

---

## Защита маршрутов через Flow

### API — манифест в `routes/api/*.php`

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

Для группового алиаса (как в manager):

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

### Web — `group` с flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` останавливает действие, когда `Auth::guest()` возвращает true. Для API можно переопределить `exit()`:

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

## Частые методы API

| Метод | Назначение |
|--------|---------|
| `Auth::check()` / `guest()` | Статус входа |
| `Auth::user()` / `id()` | Текущий пользователь |
| `Auth::profile()` | DTO профиля для API |
| `Auth::updateProfile($id, $data)` | Обновление имени/email |
| `Auth::changePassword($id, $old, $new)` | Смена пароля |
| `Auth::create($data)` / `remove($id)` | CRUD пользователя |
| `Auth::revokeSessions($userId)` | Отзыв всех токенов |

---

## События

| Событие | Имя |
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

## Архитектура

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

## Советы по HMVC

1. Логика auth живёт в pincore — не дублируйте её в приложениях.
2. Вызывайте `Auth::boot()` глобально в BootFlow.
3. Защищайте маршруты через алиасы Flow (`->flows()` или `'flow'` в API-манифесте), а не `if (!isLogin())` в каждом контроллере.
4. Для потребительских приложений часто достаточно `'transport' => ['user' => 'platform']`.

---

## Связанные документы

- [Управление токенами](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← Вернуться к оглавлению](../README.md)

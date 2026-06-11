# 用户管理（User Management）

[← 返回索引](../README.md)

Pinoox 3.x 的身份认证集中在 **pincore** 中。应用只需在 `app.php` 中配置 `auth` 和 `transport.user`，并使用 `Auth` 与 `User` Portal —— 不要在应用中重复实现登录逻辑。

---

## Portal 与辅助函数

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## app.php 配置

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // 与平台共享 UserModel
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // cookie 名称 / SPA 键
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // 或在 .env 中设置 PINOOX_JWT_SECRET
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| transport 值 | 含义 |
|-----------------|---------|
| `platform` | 共享用户（`pinx_user` / `pincore_user`） |
| `local` | 用户表仅限当前应用 |
| `{package}` | 使用另一个应用的 UserModel |

---

## 在 BootFlow 中调用 `Auth::boot()`

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

`Auth::boot()` 将当前活动应用的 `auth` 设置应用到 guard。如果不调用它，`attempt()` 和 `check()` 可能无法正常工作。

---

## 登录

新的 API 控制器继承 `ApiController`（pincore）并使用 `ok()` / `fail()`：

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

系统应用 `com_pinoox_manager` 使用相同的逻辑，但调用 `$this->error()` 和 `$this->message()`（旧版基类）—— 二者都会映射到标准的 JSON 信封结构。

允许的凭据字段：`username`、`email` 或 `login`。

---

## 使用 Flow 保护路由

### API —— `routes/api/*.php` 中的清单（manifest）

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

分组别名（如 manager）：

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

### Web —— 带 flows 的 `group`

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

当 `Auth::guest()` 为 true 时，`Pinoox\Flow\AuthFlow` 会中止动作。对于 API，可以覆盖 `exit()`：

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

## 常用 API 方法

| 方法 | 用途 |
|--------|---------|
| `Auth::check()` / `guest()` | 登录状态 |
| `Auth::user()` / `id()` | 当前用户 |
| `Auth::profile()` | 用于 API 的 Profile DTO |
| `Auth::updateProfile($id, $data)` | 更新姓名/邮箱 |
| `Auth::changePassword($id, $old, $new)` | 修改密码 |
| `Auth::create($data)` / `remove($id)` | 用户增删改查 |
| `Auth::revokeSessions($userId)` | 撤销全部令牌 |

---

## 事件（Events）

| 事件 | 名称 |
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

## 架构

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

## HMVC 提示

1. 认证逻辑位于 pincore —— 不要在应用中重复实现。
2. 在 BootFlow 中全局调用 `Auth::boot()`。
3. 使用 Flow 别名保护路由（`->flows()` 或 API 清单中的 `'flow'`），而不是在每个控制器里写 `if (!isLogin())`。
4. 对于消费方应用，通常配置 `'transport' => ['user' => 'platform']` 就足够了。

---

## 相关文档

- [令牌管理（Token management）](./token-management.md)
- [Flow](../basic/flows.md)
- [Transport](./transport.md)

---

[← 返回索引](../README.md)

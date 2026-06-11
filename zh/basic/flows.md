# Flow（中间件）

[← 返回索引](../README.md)

Flow 是 Pinoox 的中间件层：它在控制器 Action 之前运行。可用于初始化、认证、授权等横切关注点。

---

## 应用级 Flow — `before()` 方法

对于启动和初始化（会话、全局 View 数据等），使用 **`before(Request $request)`**：

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Flow\Flow;
use Pinoox\Component\Http\Request;
use Pinoox\Portal\View;

class BootFlow extends Flow
{
    protected function before(Request $request): void
    {
        View::set('siteName', config('app.name'));
    }
}
```

路径：`apps/com_acme_shop/Flow/BootFlow.php`

---

## 认证 Flow — 继承 `AuthFlow`

对于登录守卫，请继承 **`Pinoox\Flow\AuthFlow`** 并实现 **`exit()`**：

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Router\Route;
use Pinoox\Flow\AuthFlow;
use Pinoox\Portal\Auth;

class ShopAuthFlow extends AuthFlow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }

    protected function exit(Request $request, Route $route)
    {
        return redirect(url('login'));
    }
}
```

当用户是访客时，`AuthFlow` 会调用 `exit()`。对于 API，可以返回 JSON 错误：

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## 使用 `handle()` 的自定义 Flow

需要时可以直接重写 **`handle(Request $request, Closure $next)`**：

```php
protected function handle(Request $request, \Closure $next)
{
    if (!$this->userCanAccess($request)) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    return $next($request);
}
```

---

## 在 app.php 中注册别名

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // 在应用的每个路由上运行
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — 应用级 Flow（总是执行）
- **`alias`** — 供路由使用的短名称

---

## 嵌套别名（管理器模式）

Flow 别名可以嵌套以实现分组：

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

在 API 清单中使用键 **`manager.auth`**：

```php
// routes/api/private.php
[
    'method' => 'GET',
    'uri' => '/user/get',
    'action' => [UserController::class, 'get'],
    'name' => 'user.get',
    'flow' => ['manager.auth'],
],
```

---

## 将 Flow 应用到路由

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

多个 Flow：

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## 路由分组上的 Flow

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## 中断调用链

如果某个 Flow 返回了 HTTP 响应（重定向、错误 JSON 等），控制器 Action 就不会执行。

---

## 指导原则

- Flow 应放在应用的 `Flow/` 文件夹中 — 而不是 pincore 中
- 始终在 `app.php` 中注册别名
- 应用级启动逻辑：在 `flow` 清单中使用 `before()`
- 登录守卫：`AuthFlow` + `exit()`

---

## 相关文档

- [路由（Router）](./routers.md)
- [控制器（Controllers）](./controllers.md)
- [请求（Request）](./requests.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

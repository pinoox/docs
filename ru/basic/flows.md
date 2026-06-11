# Flow (middleware)

[← Вернуться к оглавлению](../README.md)

Flow — это слой middleware в Pinoox: он выполняется перед действием контроллера. Используйте его для начальной загрузки, аутентификации, авторизации и подобных сквозных задач.

---

## Flow на уровне приложения — метод `before()`

Для начальной загрузки и настройки (сессия, глобальные данные View и т.д.) используйте **`before(Request $request)`**:

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

Путь: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth Flow — наследование от `AuthFlow`

Для защиты входа наследуйтесь от **`Pinoox\Flow\AuthFlow`** и реализуйте **`exit()`**:

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

Когда пользователь — гость, `AuthFlow` вызывает `exit()`. Для API можно вернуть JSON-ошибку:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Пользовательский Flow с `handle()`

При необходимости переопределяйте **`handle(Request $request, Closure $next)`** напрямую:

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

## Регистрация псевдонимов в app.php

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // выполняется на каждом маршруте приложения
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — Flows на уровне приложения (выполняются всегда)
- **`alias`** — короткие имена для использования на маршрутах

---

## Вложенные псевдонимы (паттерн менеджера)

Псевдонимы Flow можно вкладывать для группировки:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

В API-манифесте используйте ключ **`manager.auth`**:

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

## Применение Flow к маршруту

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Несколько Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow на группе маршрутов

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Остановка цепочки

Если Flow возвращает HTTP-ответ (перенаправление, JSON-ошибку и т.д.), действие контроллера не выполняется.

---

## Рекомендации

- Flows находятся в папке `Flow/` приложения — не в pincore
- Всегда регистрируйте псевдонимы в `app.php`
- Загрузка на уровне приложения: `before()` в манифесте `flow`
- Защита входа: `AuthFlow` + `exit()`

---

## Связанные документы

- [Роутер (Router)](./routers.md)
- [Контроллеры (Controllers)](./controllers.md)
- [Запрос (Request)](./requests.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)

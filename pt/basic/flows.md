# Flow (middleware)

[← Voltar ao índice](../README.md)

Flow é a camada de middleware do Pinoox: executa antes da action do controller. Use para boot, autenticação, autorização e outras preocupações transversais.

---

## Flow em todo o app — método `before()`

Para boot e configuração (sessão, dados globais da View, etc.) use **`before(Request $request)`**:

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

Caminho: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Flow de autenticação — estender `AuthFlow`

Para guards de login, estenda **`Pinoox\Flow\AuthFlow`** e implemente **`exit()`**:

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

Quando o usuário é visitante, `AuthFlow` chama `exit()`. Para APIs você pode retornar um erro JSON:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Flow personalizado com `handle()`

Quando necessário, sobrescreva **`handle(Request $request, Closure $next)`** diretamente:

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

## Registrar aliases em app.php

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // executa em toda rota do app
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — Flows em todo o app (sempre executados)
- **`alias`** — nomes curtos para uso nas rotas

---

## Aliases aninhados (padrão manager)

Aliases de Flow podem ser aninhados para agrupamento:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

No manifesto da API use a chave **`manager.auth`**:

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

## Aplicar Flow a uma rota

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Vários Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow em um grupo de rotas

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Interromper a cadeia

Se um Flow retornar uma resposta HTTP (redirect, JSON de erro, etc.), a action do controller não é executada.

---

## Diretrizes

- Flows ficam na pasta `Flow/` do app — não no pincore
- Sempre registre aliases em `app.php`
- Boot em todo o app: `before()` no manifesto `flow`
- Guard de login: `AuthFlow` + `exit()`

---

## Documentação relacionada

- [Router](./routers.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

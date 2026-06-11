# Gerenciamento de Usuários

[← Voltar ao índice](../README.md)

A autenticação no Pinoox 3.x é centralizada no **pincore**. Os apps apenas configuram `auth` e `transport.user` no `app.php` e usam os portals `Auth` e `User` — não duplique a lógica de login no app.

---

## Portals e helpers

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## Configuração no app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // compartilhar o UserModel com a plataforma
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // nome do cookie / chave da SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // ou PINOOX_JWT_SECRET no .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| Valor do transport | Significado |
|-----------------|---------|
| `platform` | Usuários compartilhados (`pinx_user` / `pincore_user`) |
| `local` | Tabela de usuários com escopo apenas neste app |
| `{package}` | Usar o UserModel de outro app |

---

## `Auth::boot()` no BootFlow

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

`Auth::boot()` aplica as configurações de `auth` do app ativo ao guard. Sem ele, `attempt()` e `check()` podem não funcionar corretamente.

---

## Login

Os novos controllers de API estendem `ApiController` (pincore) e usam `ok()` / `fail()`:

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

O app do sistema `com_pinoox_manager` usa a mesma lógica, mas chama `$this->error()` e `$this->message()` (classe base legada) — ambos mapeiam para o envelope JSON padrão.

Campos de credenciais permitidos: `username`, `email` ou `login`.

---

## Proteger rotas com Flow

### API — manifest em `routes/api/*.php`

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

Para um alias agrupado (como no manager):

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

### Web — `group` com flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` interrompe a action quando `Auth::guest()` é verdadeiro. Para APIs você pode sobrescrever `exit()`:

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

## Métodos comuns da API

| Método | Finalidade |
|--------|---------|
| `Auth::check()` / `guest()` | Estado de login |
| `Auth::user()` / `id()` | Usuário atual |
| `Auth::profile()` | DTO de perfil para a API |
| `Auth::updateProfile($id, $data)` | Atualizar nome/e-mail |
| `Auth::changePassword($id, $old, $new)` | Trocar a senha |
| `Auth::create($data)` / `remove($id)` | CRUD de usuários |
| `Auth::revokeSessions($userId)` | Revogar todos os tokens |

---

## Events

| Event | Nome |
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

## Arquitetura

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

## Dicas de HMVC

1. A lógica de auth vive no pincore — não a duplique nos apps.
2. Chame `Auth::boot()` globalmente no BootFlow.
3. Proteja as rotas com aliases de Flow (`->flows()` ou `'flow'` no manifest da API), e não com `if (!isLogin())` em cada controller.
4. Para apps consumidores, `'transport' => ['user' => 'platform']` costuma ser suficiente.

---

## Documentação relacionada

- [Gerenciamento de tokens](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← Voltar ao índice](../README.md)

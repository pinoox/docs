# Gestion des utilisateurs

[← Retour à l'index](../README.md)

L'authentification dans Pinoox 3.x est centralisée dans **pincore**. Les applications se contentent de configurer `auth` et `transport.user` dans `app.php` et d'utiliser les portails `Auth` et `User` — ne dupliquez pas la logique de connexion dans l'application.

---

## Portails et helpers

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## Configuration app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // partager UserModel avec la plateforme
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // nom du cookie / clé SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // ou PINOOX_JWT_SECRET dans .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| Valeur de transport | Signification |
|-----------------|---------|
| `platform` | Utilisateurs partagés (`pinx_user` / `pincore_user`) |
| `local` | Table utilisateur limitée à cette application uniquement |
| `{package}` | Utiliser le UserModel d'une autre application |

---

## `Auth::boot()` dans BootFlow

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

`Auth::boot()` applique les paramètres `auth` de l'application active au guard. Sans cela, `attempt()` et `check()` peuvent ne pas fonctionner correctement.

---

## Connexion

Les nouveaux contrôleurs API étendent `ApiController` (pincore) et utilisent `ok()` / `fail()` :

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

L'application système `com_pinoox_manager` utilise la même logique mais appelle `$this->error()` et `$this->message()` (classe de base héritée) — les deux correspondent à l'enveloppe JSON standard.

Champs d'identification autorisés : `username`, `email` ou `login`.

---

## Protéger les routes avec un Flow

### API — manifeste dans `routes/api/*.php`

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

Pour un alias groupé (comme manager) :

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

### Web — `group` avec flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` interrompt l'action lorsque `Auth::guest()` est vrai. Pour les API, vous pouvez surcharger `exit()` :

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

## Méthodes API courantes

| Méthode | Rôle |
|--------|---------|
| `Auth::check()` / `guest()` | État de connexion |
| `Auth::user()` / `id()` | Utilisateur courant |
| `Auth::profile()` | DTO de profil pour l'API |
| `Auth::updateProfile($id, $data)` | Mettre à jour le nom/email |
| `Auth::changePassword($id, $old, $new)` | Changer le mot de passe |
| `Auth::create($data)` / `remove($id)` | CRUD utilisateur |
| `Auth::revokeSessions($userId)` | Révoquer tous les tokens |

---

## Événements

| Événement | Nom |
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

## Conseils HMVC

1. La logique d'authentification réside dans pincore — ne la dupliquez pas dans les applications.
2. Appelez `Auth::boot()` globalement dans BootFlow.
3. Protégez les routes avec des alias de Flow (`->flows()` ou `'flow'` dans le manifeste API), pas avec des `if (!isLogin())` dans chaque contrôleur.
4. Pour les applications consommatrices, `'transport' => ['user' => 'platform']` suffit souvent.

---

## Documentation associée

- [Gestion des tokens](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← Retour à l'index](../README.md)

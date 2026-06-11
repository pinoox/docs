# Flow (middleware)

[← Retour à l'index](../README.md)

Flow est la couche middleware de Pinoox : elle s'exécute avant l'action du contrôleur. Utilisez-la pour le boot, l'authentification, l'autorisation et les préoccupations transversales similaires.

---

## Flow global à l'app — méthode `before()`

Pour le boot et la configuration (session, données View globales, etc.), utilisez **`before(Request $request)`** :

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

Chemin : `apps/com_acme_shop/Flow/BootFlow.php`

---

## Flow d'authentification — étendre `AuthFlow`

Pour les gardes de connexion, étendez **`Pinoox\Flow\AuthFlow`** et implémentez **`exit()`** :

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

Lorsque l'utilisateur est invité, `AuthFlow` appelle `exit()`. Pour les API, vous pouvez renvoyer une erreur JSON :

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Flow personnalisé avec `handle()`

Si nécessaire, surchargez directement **`handle(Request $request, Closure $next)`** :

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

## Enregistrer les alias dans app.php

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // s'exécute sur chaque route de l'app
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — Flows globaux à l'app (toujours exécutés)
- **`alias`** — noms courts pour les routes

---

## Alias imbriqués (modèle manager)

Les alias Flow peuvent être imbriqués pour le regroupement :

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Dans le manifeste API, utilisez la clé **`manager.auth`** :

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

## Appliquer un Flow à une route

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

Plusieurs Flows :

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow sur un groupe de routes

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## Arrêter la chaîne

Si un Flow renvoie une réponse HTTP (redirection, JSON d'erreur, etc.), l'action du contrôleur ne s'exécute pas.

---

## Recommandations

- Les Flows appartiennent au dossier `Flow/` de l'app — pas dans pincore
- Enregistrez toujours les alias dans `app.php`
- Boot global à l'app : `before()` dans le manifeste `flow`
- Garde de connexion : `AuthFlow` + `exit()`

---

## Documentation associée

- [Router](./routers.md)
- [Contrôleurs](./controllers.md)
- [Request](./requests.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)

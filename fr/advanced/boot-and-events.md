# boot.php et événements

[← Retour à l'index](../README.md)

En plus de `routes/`, vous pouvez enregistrer des routes, des points de terminaison API, des flows, des planifications (schedules) et des écouteurs (listeners) dans **`boot.php`** — utile pour les **plugins**, les micro-modules ou les hooks dans une application hôte (par ex. manager).

Chaque app peut fournir `apps/{package}/boot.php`. Le fichier retourne une closure qui reçoit `AppRegister` et s'exécute **avant** le traitement de la requête.

---

## Cycle de vie

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Étapes du pipeline

| Étape | Rôle |
|-------|------|
| `boot.global` | Boot des apps `boot-global => true` à chaque requête |
| `app.boot` | Boot de l'app active (+ extenders via `extends`) |

### Événements de boot

| Nom | Quand |
|-----|-------|
| `app.booting` / `app.booting.{package}` | Avant le commit |
| `app.booted` / `app.booted.{package}` | Après integrate |
| `app.routes` / `app.routes.{package}` | Lors de l'application des routes web |
| `app.api` / `app.api.{package}` | Lors de la construction du registry API |

Écouter depuis `boot.php` :

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Événements de requête (noyau)

Dispatchés automatiquement à chaque requête HTTP par le framework (`AppCoreEventSubscriber`) :

| Nom | Quand | Variante package | Canal nommé |
|-----|-------|------------------|-------------|
| `app.route.matched` | Après match de route | `app.route.matched.{package}` | `app.route.{routeName}` ou `app.api.{routeName}` |
| `app.controller` | Avant le controller | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | Avant envoi de la réponse | `app.response.{package}` | — |
| `app.exception` | Exception non gérée | `app.exception.{package}` | — |
| `app.terminate` | Après envoi de la réponse | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

Utilisez les **watches** (`onRoute`, `onApi`, …) pour des hooks simples ; **listen** sur les événements noyau pour un contrôle total ou des plugins cross-app.

---

## Trois modes d'application

| Mode | Configuration | Comportement |
|------|--------|----------|
| **Route uniquement** | `router.routes` uniquement | S'exécute lorsque l'URL de l'application est active |
| **Boot global** | `boot-global => true` | Démarre à chaque requête HTTP |
| **Boot + Route** | `boot.php` + routes | Squelette par défaut |

Plugin sur une application hôte :

```php
'extends' => ['com_host_app'],
```

Votre plugin ne démarre que lorsque l'hôte démarre (plus léger que le mode global).

---

## clés `app.php` pour le boot

Ces clés dans `apps/{package}/app.php` contrôlent **si** `boot.php` s'exécute, **quand**, et si le résultat est mis en cache. Elles configurent la pipeline de boot — elles ne remplacent pas `boot.php`.

### Fichier boot (`boot`)

| Valeur | Défaut | Effet |
|--------|--------|-------|
| `true` | oui | Exécuter `boot.php` quand l'app boot |
| `false` | | Pas de boot — routes seulement |
| `'path/custom.php'` | | Autre fichier relatif à la racine de l'app |

Le fichier doit **retourner un callable** `fn (AppRegister $register) => …`. S'il manque avec `true`, le boot continue sans erreur.

### Plugin global (`boot-global`)

| Valeur | Défaut | Effet |
|--------|--------|-------|
| `false` | oui | Boot seulement quand cette app est active |
| `true` | | Boot à **chaque requête HTTP** |

### Plugin hôte (`extends`)

| Valeur | Défaut | Effet |
|--------|--------|-------|
| `[]` | oui | App normale |
| `['com_host_app']` | | Boot **avant** l'hôte quand il devient actif |

### Enregistrement extra (`startup`)

Callable optionnel dans `app.php`, exécuté **après** `boot.php` avec la même API `AppRegister`.

### Cache boot (`cache`)

Opt-in : `cache.enabled` doit être `true`.

| Clé | Défaut | Effet |
|-----|--------|-------|
| `cache.enabled` | `false` | Interrupteur principal |
| `cache.stores.boot` | `true` | Cache des enregistrements boot |
| `cache.stores.routes` | `true` | Cache des manifests de routes |
| `cache.stores.api` | `true` | Cache des listes API |

Après déploiement : `php pinoox cache:build {package}`.

### Choix rapide

| Besoin | Réglage |
|--------|---------|
| App normale | `'boot' => true` |
| Routes seules | `'boot' => false` |
| Plugin site-wide | `'boot-global' => true` |
| Plugin sur un hôte | `'extends' => ['com_host_app']` |
| Boot prod plus rapide | `'cache.enabled' => true` |

---

## boot.php basique

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## AppRegister — méthodes courantes

| Méthode | Rôle |
|--------|---------|
| `web(callable)` | Enregistrer des routes via le builder |
| `route([...])` | Route web unique |
| `api([manifest])` | Manifeste API complet |
| `apiRoute([...])` | Point de terminaison API unique |
| `action('name', handler)` | Action nommée |
| `flowAlias(['auth' => AuthFlow::class])` | Alias de flow |
| `schedule(callable)` | Tâche planifiée |
| `listen('event', listener)` | Écouteur d'événement |
| `subscribe(SubscriberClass::class)` | Subscriber Symfony |
| `when('com_host', fn)` | Hook lorsqu'une autre application démarre |

---

## Theme — contextes, héritage et hooks boot

Dossiers sous `apps/{package}/theme/{name}/`. Theme actif dans **`app.php`** ; hooks dans **`boot.php`**.

### Clés `app.php`

| Clé | Rôle |
|-----|------|
| `theme` | Dossier theme actif |
| `theme-context` / `theme-contexts` | Plusieurs themes |
| `theme-extends` | Héritage |
| `path-theme` | Chemin custom |
| `frontend` | Vite profile, entry, manifest |

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Routes : `flows: ['auth', 'theme.panel']`. Dans `theme/{name}/` : `theme.php`, Twig, `functions.php`, `frontend.config.php`, `src/` / `dist/`.

Voir [Views](../basic/views.md), [Twig](../basic/templates.md), [app.php](../start/app-manifest.md).

### Depuis `boot.php`

**`onTheme`** ou **listen** / **watch** :

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

Controller : `View::changeTheme('panel')`, `ThemeContext::activate('panel')`, `within_theme(...)`.

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

---

## Portail Event

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Consultez [Email](./mail.md) pour découpler l'envoi de mails des contrôleurs.

**Flow** = avant le contrôleur (middleware). **Event** = après une action (effets de bord).

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Cache de boot

`'boot' => true` sous `cache.stores` dans `app.php` met en cache le boot via Pinker — voir [Pinker](./pinker.md).

---

## Documentation associée

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Routeurs](../basic/routers.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)

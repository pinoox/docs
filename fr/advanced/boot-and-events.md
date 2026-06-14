# boot.php et événements

[← Retour à l'index](../README.md)

En plus de `routes/`, vous pouvez enregistrer des routes, des points de terminaison API, des flows, des planifications (schedules) et des écouteurs (listeners) dans **`boot.php`** — utile pour les **plugins**, les micro-modules ou les hooks dans une application hôte (par ex. manager).

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

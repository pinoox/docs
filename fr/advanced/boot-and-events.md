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
'extends' => ['com_pinoox_manager'],
```

Votre plugin ne démarre que lorsque l'hôte démarre (plus léger que le mode global).

---

## boot.php basique

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => response()->json(['ok' => true]),
        'name' => 'health',
    ]);

    $register->when('com_pinoox_manager', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => response()->json(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['manager.auth'],
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

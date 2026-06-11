# boot.php und Events

[← Zurück zur Übersicht](../README.md)

Neben `routes/` können Sie Routen, API-Endpunkte, Flows, Schedules und Listener auch in **`boot.php`** registrieren — nützlich für **Plugins**, Mikro-Module oder Hooks in eine Host-App (z. B. den Manager).

---

## Drei App-Modi

| Modus | Konfiguration | Verhalten |
|------|--------|----------|
| **Nur Route** | nur `router.routes` | Läuft, wenn die App-URL aktiv ist |
| **Boot global** | `boot-global => true` | Bootet bei jedem HTTP-Request |
| **Boot + Route** | `boot.php` + Routen | Standard-Scaffold |

Plugin auf einer Host-App:

```php
'extends' => ['com_pinoox_manager'],
```

Ihr Plugin bootet nur, wenn der Host bootet (leichtgewichtiger als global).

---

## Einfache boot.php

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

## AppRegister — häufige Methoden

| Methode | Zweck |
|--------|---------|
| `web(callable)` | Routen über den Builder registrieren |
| `route([...])` | Einzelne Web-Route |
| `api([manifest])` | Vollständiges API-Manifest |
| `apiRoute([...])` | Einzelner API-Endpunkt |
| `action('name', handler)` | Benannte Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow-Alias |
| `schedule(callable)` | Geplanter Task |
| `listen('event', listener)` | Event-Listener |
| `subscribe(SubscriberClass::class)` | Symfony-Subscriber |
| `when('com_host', fn)` | Hook, wenn eine andere App bootet |

---

## Event-Portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Siehe [E-Mail](./mail.md), um den Mail-Versand von Controllern zu entkoppeln.

**Flow** = vor dem Controller (Middleware). **Event** = nach einer Action (Seiteneffekte).

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot-Cache

`'boot' => true` unter `cache.stores` in `app.php` backt den Boot über Pinker — siehe [Pinker](./pinker.md).

---

## Verwandte Dokumente

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Router](../basic/routers.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zur Übersicht](../README.md)

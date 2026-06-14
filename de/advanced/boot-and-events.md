# boot.php und Events

[← Zurück zur Übersicht](../README.md)

Neben `routes/` können Sie Routen, API-Endpunkte, Flows, Schedules und Listener auch in **`boot.php`** registrieren — nützlich für **Plugins**, Mikro-Module oder Hooks in eine Host-App (z. B. den Manager).

Jede App kann `apps/{package}/boot.php` bereitstellen. Die Datei gibt eine Closure zurück, die `AppRegister` erhält und **vor** der Request-Verarbeitung läuft.

---

## Lebenszyklus

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Pipeline-Stufen

| Stufe | Zweck |
|-------|--------|
| `boot.global` | Boot von Apps mit `boot-global => true` bei jedem Request |
| `app.boot` | Boot der aktiven Route-App (+ Extender via `extends`) |

### Boot-Events

| Name | Wann |
|------|------|
| `app.booting` / `app.booting.{package}` | Vor Boot-Commit |
| `app.booted` / `app.booted.{package}` | Nach integrate |
| `app.routes` / `app.routes.{package}` | Beim Anwenden der Web-Routen |
| `app.api` / `app.api.{package}` | Beim Aufbau der API-Registry |

Ab `boot.php` lauschen:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Core-Request-Events

Automatisch bei jedem HTTP-Request durch das Framework (`AppCoreEventSubscriber`):

| Name | Wann | Package-Variante | Benannter Kanal |
|------|------|------------------|-----------------|
| `app.route.matched` | Nach Route-Match | `app.route.matched.{package}` | `app.route.{routeName}` oder `app.api.{routeName}` |
| `app.controller` | Vor Controller-Ausführung | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | Vor Senden der Response | `app.response.{package}` | — |
| `app.exception` | Bei unbehandelter Exception | `app.exception.{package}` | — |
| `app.terminate` | Nach gesendeter Response | `app.terminate.{package}` | — |

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

Für einfache Hooks **watches** (`onRoute`, `onApi`, …); für volle Kontrolle **listen** auf Core-Events.

---

## Drei App-Modi

| Modus | Konfiguration | Verhalten |
|------|--------|----------|
| **Nur Route** | nur `router.routes` | Läuft, wenn die App-URL aktiv ist |
| **Boot global** | `boot-global => true` | Bootet bei jedem HTTP-Request |
| **Boot + Route** | `boot.php` + Routen | Standard-Scaffold |

Plugin auf einer Host-App:

```php
'extends' => ['com_host_app'],
```

Ihr Plugin bootet nur, wenn der Host bootet (leichtgewichtiger als global).

---

## `app.php`-Schlüssel für Boot

Diese Schlüssel in `apps/{package}/app.php` steuern, **ob** `boot.php` läuft, **wann** es läuft und ob Boot-Ausgaben gecacht werden. Sie konfigurieren die Boot-Pipeline — sie ersetzen `boot.php` nicht.

### Boot-Datei (`boot`)

| Wert | Standard | Wirkung |
|------|----------|---------|
| `true` | ja | `boot.php` während der Boot-Pipeline ausführen |
| `false` | | Keine Boot-Datei — nur Routen |
| `'path/custom.php'` | | Andere Datei relativ zum App-Root |

```php
'boot' => true,
'boot' => false,
'boot' => 'setup/boot.php',
```

Die Datei muss **ein Callable** zurückgeben: `fn (AppRegister $register) => …`. Fehlt die Datei bei `true`, wird Boot still übersprungen.

### Globales Plugin (`boot-global`)

| Wert | Standard | Wirkung |
|------|----------|---------|
| `false` | ja | Boot nur wenn diese App aktiv ist |
| `true` | | Boot bei **jedem HTTP-Request** |

### Host-Plugin (`extends`)

| Wert | Standard | Wirkung |
|------|----------|---------|
| `[]` | ja | Normale App |
| `['com_host_app']` | | Boot **vor** dem Host, wenn dieser aktiv wird |

### Zusätzliche Registrierung (`startup`)

| Wert | Standard | Wirkung |
|------|----------|---------|
| `null` | ja | Kein zweiter Schritt |
| `fn (AppRegister $r) => …` | | Zusätzliche Registrierung in `app.php` **nach** `boot.php` |

### Boot-Cache (`cache`)

Opt-in: `cache.enabled` muss `true` sein.

| Schlüssel | Standard | Wirkung |
|-----------|----------|---------|
| `cache.enabled` | `false` | Hauptschalter |
| `cache.stores.boot` | `true` | Boot-Registrierungen cachen |
| `cache.stores.routes` | `true` | Routen-Manifeste cachen |
| `cache.stores.api` | `true` | API-Listen cachen |

Nach Deploy: `php pinoox cache:build {package}`. Siehe [Boot-Cache](#boot-cache).

### Schnellauswahl

| Ziel | Einstellung |
|------|-------------|
| Normale App | `'boot' => true` |
| Nur Routen | `'boot' => false` |
| Site-weites Plugin | `'boot-global' => true` |
| Plugin auf Host | `'extends' => ['com_host_app']` |
| Schnelleres Prod-Boot | `'cache.enabled' => true` + `cache:build` |

---

## Einfache boot.php

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

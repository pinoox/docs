# Projektstruktur

[← Zurück zur Übersicht](../README.md)

Pinoox verwendet die HMVC-Architektur: Jede App unter `apps/{package}/` ist ein vollständiges, unabhängiges MVC-Modul. Der Framework-Kern liegt in `vendor/pinoox/pincore/` und wird nur bearbeitet, wenn die Plattform selbst geändert wird.

---

## Projekt-Layout

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← Core (Composer-Paket)
├── apps/                    ← alle Apps
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← hochgeladene Dateien & App-Speicher
```

---

## App-Layout

```
apps/com_acme_shop/
├── app.php                  ← Manifest (erforderlich)
├── boot.php                 ← programmatische Routen/Events (optional)
├── schedule.php             ← Cron-Aufgaben (optional)
├── Controller/              ← HTTP-Handler
├── Model/                   ← Eloquent-Models
├── Flow/                    ← Middleware
├── Component/               ← Geschäftslogik
├── Portal/                  ← App-Facades (optional)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← Konstanten für Action-Namen (optional)
├── theme/default/           ← Twig + Assets
├── lang/en/                 ← Übersetzungen
├── config/                  ← App-Konfiguration
├── database/migrations/
└── pinker/                  ← Build-Spiegel
```

Views liegen nicht in einem separaten `View/`-Ordner — Templates befinden sich in `theme/{themeName}/`.

---

## app.php — wichtige Felder

```php
<?php

return [
    'package' => 'com_acme_shop',   // = Ordnername
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespaces

PSR-4: `App\` → `apps/`

| Datei | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Benennungsregeln

- Paket: `com_{vendor}_{name}` — z. B. `com_acme_shop`
- Ordnername = `package` in `app.php` = Namespace-Segment
- DB-Tabellenpräfix: `{package}_` (z. B. `com_acme_shop_orders`)

---

## Grenze zwischen App und Core

| Änderung | Ort |
|--------|----------|
| Neuer Endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| Framework-Bug | `pinoox/pincore` (Upstream) |
| UI | `apps/{package}/theme/` |

Halten Sie Apps unabhängig — verwenden Sie `Pinoox\Portal\*`-Facades, statt Apps aneinander zu koppeln.

---

## Verwandte Dokumente

- [Ihre erste App](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Zurück zur Übersicht](../README.md)

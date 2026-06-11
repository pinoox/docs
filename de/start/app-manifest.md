# app.php-Manifest-Referenz

[← Zurück zur Übersicht](../README.md)

`app.php` ist das Manifest Ihrer App. Die Standardwerte liegen in `vendor/pinoox/pincore/Component/Package/data/source.php` — überschreiben Sie nur, was Sie benötigen.

---

## Identität & Aktivierung

| Schlüssel | Zweck |
|-----|---------|
| `package` | Ordnername = Namespace (`com_acme_shop`) |
| `name` | Anzeigename |
| `enable` | App aktivieren / deaktivieren |
| `description`, `developer`, `icon` | Metadaten |
| `version-name`, `version-code` | App-Version |
| `sys-app`, `hidden`, `dock` | System-App / versteckt / Manager-Dock |
| `minpin` | Minimale Plattformversion |

---

## Router & Boot

| Schlüssel | Zweck |
|-----|---------|
| `router.routes` | `routes/*.php`-Dateien |
| `boot` | `boot.php` ausführen (Standard: true) |
| `boot-global` | Bei jeder HTTP-Anfrage booten |
| `extends` | Booten, wenn die Host-App bootet |
| `loader` | Zusätzliche Dateien (`func.php`) |
| `depends` | Erforderliche Apps |

Siehe [boot.php & Events](../advanced/boot-and-events.md).

---

## Flow & Sicherheit

| Schlüssel | Zweck |
|-----|---------|
| `flow` | Globale Flows (BootFlow) |
| `alias` | Name → Flow-Klasse |
| `auth` | mode, lifetime, JWT/Cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Benutzer/Dateien/Zugriff mit der Plattform teilen |

Siehe [Flows](../basic/flows.md), [Benutzerverwaltung](../advanced/user-management.md), [Zugriff](../advanced/access-permissions.md).

---

## UI & Theme

| Schlüssel | Zweck |
|-----|---------|
| `theme` | Aktiver Theme-Ordner |
| `theme-context`, `theme-contexts`, `theme-extends` | Multi-Kontext / Vererbung |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Standard-Locale |
| `open` | Öffnungsverhalten im Manager |

---

## Datenbank & Speicher

| Schlüssel | Zweck |
|-----|---------|
| `database` | DB-Verbindung überschreiben |
| `table.prefix` | Tabellenpräfix |
| `transport.user` / `file_storage` / `access` | Voreinstellungen oder granulare Schlüssel |
| `filesystem` | disk, thumbs, access |

---

## Laufzeit

| Schlüssel | Zweck |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Modus-Overrides |
| `cache` | Routen/API/Boot/Twig baken |
| `log`, `redis`, `date` | Overrides pro App |
| `container` | DI-Bindungen |

---

## Pinker / Pinx

| Schlüssel | Zweck |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | exclude/include für Pakete |

---

## Kombiniertes Beispiel

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## Verwandte Dokumente

- [Projektstruktur](./structure.md)
- [Konfiguration](../basic/config.md)

---

[← Zurück zur Übersicht](../README.md)

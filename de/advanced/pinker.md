# Pinker und Cache

[← Zurück zur Übersicht](../README.md)

**Pinker** ist die Bake-/Runtime-Schicht in Pinoox 3.x: Konfiguration und Cache werden aus den Quellen in PHP-Dateien kompiliert, die per `include` geladen werden können — für einen schnelleren Boot. Standardpfad pro App: **`pinker/apps/{package}/`**.

---

## Ordnerstruktur

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← gebackene app.php
        └── cache/
            ├── manifest.php     ← Checksumme + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← kompilierte Templates
```

Auf Projektebene:

```
pinker/config/          ← gebackene Konfiguration (nicht env-sensitiv)
pinker/state/config/    ← Overrides nach der Installation (z. B. database)
```

---

## CLI-Befehle

```bash
# Pinker für eine App neu bauen
php pinoox pinker:rebuild com_acme_shop

# Kurzer Alias
php pinoox bake com_acme_shop

# Status: Quelle mit gebackener Ausgabe vergleichen
php pinoox pinker:status com_acme_shop

# Cache bauen (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Nur Twig
php pinoox cache:build com_acme_shop --only=twig

# Nur Pinker
php pinoox cache:build com_acme_shop --only=pinker

# Cache leeren
php pinoox cache:clear com_acme_shop
```

---

## Wann neu bauen?

| Ereignis | Befehl |
|-------|---------|
| Änderung an `app.php` oder Konfiguration | `pinker:rebuild` |
| Änderung an Route / API | `cache:build` |
| Änderung an `.twig` in Produktion | `cache:build --only=twig` |
| Nach Server-Installation | `cache:build` + `pinker:rebuild` |
| Vor dem Bauen einer `.pinx` | `cache:build` (Cache im Paket) |

---

## Cache zur Laufzeit aktivieren

In `apps/{package}/app.php`:

```php
'cache' => [
    'enabled' => false,   // Standard — in Produktion bei Bedarf auf true setzen
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## App-Spiegel — `pinker/app.php`

Jede App kann einen gebackenen Spiegel haben:

```
apps/com_acme_shop/pinker/app.php   ← Quelle/Referenz im Repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← Runtime
```

---

## `pinker()`-Helper

Für manuelles Baken von Daten:

```php
pinker($data, ['lifetime' => 3600]);
```

In der Regel verwenden Sie stattdessen die CLI; im App-Code wird dies selten benötigt.

---

## Empfohlener Deploy-Workflow

```bash
# 1. Frontend bauen
php pinoox theme:frontend build com_acme_shop

# 2. Cache
php pinoox cache:build com_acme_shop

# 3. Pinker (umgebungsspezifisch)
php pinoox pinker:rebuild com_acme_shop
```

---

## Tipps

- Bearbeiten Sie `pinker/state/` nicht manuell — der Installer schreibt dorthin.
- In der Entwicklung ist der Runtime-Cache normalerweise deaktiviert; bauen Sie nur nach umfangreichen Änderungen neu.
- `.pinx` kann vorgebauten Cache enthalten; führen Sie auf dem Zielserver einmal `cache:build --only=pinker` aus.

---

## Verwandte Dokumente

- [Config](../basic/config.md)
- [Twig-Templates](../basic/templates.md)
- [CLI-Referenz](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← Zurück zur Übersicht](../README.md)

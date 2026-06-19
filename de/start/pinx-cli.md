# Pinx CLI (Single-App-Projekte)

[← Zurück zur Übersicht](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** ist die Entwickler-CLI für **Single-App**-Pinoox-Projekte — Gerüst erstellen, ausführen, migrieren, bauen und `.pinx`-Pakete ausliefern, ohne einen Multi-App-Manager anzufassen.

Sie baut auf `pinoox/pincore` und dem `pinoox/app`-Template auf. Ihr Projektstammverzeichnis **ist** die App: ein `app.php`, ein Paket, ein Workflow.

> Für klassische Multi-App-Plattform-Installationen verwenden Sie stattdessen [`php pinoox`](./cli-reference.md).

---

## Schnellstart

Installieren Sie Pinx einmalig, erstellen Sie eine neue App und starten Sie sie:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # schlägt com_my_shop vor — im Assistenten bestätigen oder anpassen
cd my-shop
cp .env.example .env          # DB_* setzen, falls Sie eine Datenbank verwenden
pinx setup                    # Plattform + App migrieren, Seeder ausführen
pinx dev                      # http://127.0.0.1:8000
```

Fügen Sie Composers globales `bin`-Verzeichnis zu Ihrem `PATH` hinzu, falls `pinx` nicht gefunden wird:

- Linux / macOS: `~/.composer/vendor/bin` oder `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| Schritt | Was er tut |
|------|--------------|
| `composer global require` | Installiert den `pinx`-Befehl auf Ihrem Rechner |
| `pinx new my-shop` | Erstellt das Gerüst aus `pinoox/app`; der Assistent schlägt ein dreiteiliges Paket vor (z. B. `com_my_shop`) |
| `.env` | Datenbank- und Projektpfade — von `.env.example` kopieren |
| `pinx setup` | In einem Schritt: Plattform-Migrationen → App-Migrationen → Seeder |
| `pinx dev` | PHP-Entwicklungsserver; startet auch Vite, wenn ein Frontend-Stack konfiguriert ist |

Paketnamen folgen `com_{vendor}_{name}` — z. B. `com_acme_shop`, `ir_yekdo_app`. Bereits in einem leeren Ordner? Verwenden Sie `pinx init` statt `pinx new`.

**Optionale Prüfung vor `setup`:** `pinx doctor` berichtet über PHP, Layout, Env, DB und Build-Bereitschaft.

---

## Alternative: `composer create-project`

Keine globale Installation — das Template liefert `bin/pinx` im Projekt mit:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## Was Single-App anders macht

Klassische Pinoox-Installationen halten viele Apps unter `apps/` und wählen zur Laufzeit eine aus. **Single-App** flacht das ab:

- `app.php` im Projektstammverzeichnis enthält die Paketidentität und Pinx-Einstellungen
- `Controller/`, `Model/`, `routes/`, `theme/` liegen im Stammverzeichnis — nicht in `apps/{package}/`
- `platform/` enthält lokales Routing und Launcher-Konfiguration (von `.pinx`-Builds ausgeschlossen)
- Pinx zielt immer auf **Ihre** App — keine Paketauswahl, keine Manager-UI

```
my-shop/                    ← Projektstamm = App-Stamm
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← Dev-Host + Deploy-Schicht (nur lokal)
├── bin/pinx                ← projektlokaler CLI-Einstieg
└── vendor/pinoox/pincore   ← Framework
```

---

## Installationsoptionen

| Wo | Wie | Wann verwenden |
|-------|-----|-------------|
| **Global** | `composer global require pinoox/pinx-cli` | Empfohlen — `pinx new` und `pinx init` von überall |
| **Pro Projekt** | Als `bin/pinx` in `pinoox/app` mitgeliefert | Nach `composer create-project` — keine globale Installation nötig |

```bash
pinx -v          # CLI-Version (z. B. pinx-cli 1.1.7)
pinx list        # gruppierte Befehlsübersicht
pinx help setup  # Details zu einem Befehl
```

---

## Täglicher Workflow

```bash
pinx dev                    # lokaler Server (+ Vite, wenn app.php → frontend.stack gesetzt ist)
pinx dev --open             # Browser nach dem Start öffnen
pinx dev --no-frontend      # nur PHP

pinx migrate                # App-Migrationen ausführen (--platform führt zuerst die Plattform aus)
pinx migrate:st             # Migrationsstatus
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # Named Actions auflisten (--validate, --json)
pinx test                   # App-Tests ausführen (Pest)
```

**Frontend** (wenn `theme/` Vue/React + Vite verwendet):

```bash
pinx fe:info                # Stack, npm-Skripte, Pfade
pinx fe:i                   # npm install
pinx fe:d                   # Vite-Entwicklungsserver
pinx fe:b                   # Produktions-Build
pinx fe:sc --stack=vue      # Starter-Dateien generieren
```

**Abhängigkeiten:**

```bash
pinx deps:st                # Composer- + npm-Status
pinx deps:i                 # alles installieren
pinx deps:up                # alles aktualisieren
```

**Pinker** (Build-Cache):

```bash
pinx pinker:st              # Cache vs. Quelle
pinx pinker:rb              # neu bauen
pinx pinker:df              # Diff
```

---

## In die Produktion ausliefern

Bauen Sie ein `.pinx`-Paket für die Installation auf einer vollständigen Pinoox-Plattform (Manager → Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # Version in app.php erhöhen + bauen
pinx release --sign         # signieren, wenn ein Schlüssel in app.php → pinx.sign konfiguriert ist
```

`pinx build` wendet sinnvolle Standardwerte an (schließt `vendor/`, `bin/`, `.env`, `platform/` und Dev-Tooling aus). Überschreiben Sie in `app.php` nur bei Bedarf:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor führt eine strukturierte Diagnose durch und schlägt Korrekturbefehle vor, wenn etwas fehlschlägt:

| Gruppe | Prüfungen |
|-------|--------|
| **Projekt** | `app.php`, Paketidentität, `platform/`-Layout |
| **Laufzeit** | PHP-Version (≥ 8.2), Erweiterungen, beschreibbare Pfade |
| **Abhängigkeiten** | Composer-Vendor, optional Node/npm |
| **Umgebung** | Vorhandensein der `.env` und Schlüsselvariablen |
| **Datenbank** | Verbindung (überspringbar mit `--skip-db`) |
| **Frontend** | Theme-Stack, `package.json` (überspringbar mit `--skip-frontend`) |
| **Build** | Export-Bereitschaft, Icon, Versionsfelder |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # CI-freundlicher Bericht
pinx doctor --no-fixes      # vorgeschlagene Befehle ausblenden
```

---

## Befehlsreferenz

Führen Sie `pinx list` für eine nach Abschnitten gegliederte Übersicht aus. Kurz-Aliase erscheinen in Klammern.

### Projekt

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `new` | — | Gerüst aus `pinoox/app` erstellen (Assistent oder Flags) |
| `init` | — | Aktuelles Verzeichnis initialisieren (`--force` zum Überschreiben) |
| `setup` | — | DB: Plattform + App migrieren, dann seeden |
| `doctor` | `dr` | Gesundheitsprüfung — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | Metadaten aus `app.php` anzeigen |

### Entwicklung

| Befehl | Beschreibung |
|---------|-------------|
| `dev` | Entwicklungsserver; Vite, wenn `frontend.stack` vue/react ist |

### Datenbank

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `migrate:run` | `migrate` | App-Migrationen ausführen (`--platform` führt zuerst die Plattform aus) |
| `migrate:status` | `migrate:st` | Migrationsstatus |
| `migrate:rollback` | `migrate:rb` | Letzten Batch zurückrollen (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Migrationsdatei erstellen |
| `migrate:platform` | `migrate:pl` | Nur Plattform-Migrationen |
| `seeder:run` | `seed` | Seeder ausführen (`-c` Klasse) |

### Patches

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `patch:run` | `patch` | Ausstehende Patches ausführen |
| `patch:status` | `patch:st` | Patch-Status |
| `patch:rollback` | `patch:rb` | Letzten Patch-Batch zurückrollen |

### Build & Release

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `build` | `bld` | `.pinx`-Paket bauen |
| `release` | `rel` | Versionserhöhung + Build (`--bump`, `--sign`) |

### Scaffolding

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Routen

| Befehl | Beschreibung |
|---------|-------------|
| `route:actions` / `routes` | Named Actions auflisten (`--validate`, `--json`) |

### Abhängigkeiten

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Composer- + npm-Status |
| `deps:install` | `deps:i` | Abhängigkeiten installieren |
| `deps:update` | `deps:up` | Abhängigkeiten aktualisieren |

### Frontend

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Theme-Stack und npm-Skripte |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Produktions-Build |
| `fe:dev` | `fe:d` | Vite-Entwicklungsserver |
| `fe:scaffold` | `fe:sc` | Starter-Dateien (`--stack=vue\|react\|twig`) |

### Schedule

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | Cron-Aufgaben aus `schedule.php` auflisten |
| `schedule:run` | `sched:run` | Fällige Aufgaben ausführen (`--dry-run`) |

### Pinion (wiederaufnahmefähige Uploads)

Weitergeleitet an `php pinoox pinion:*` — temporäre Chunk-Upload-Sessions verwalten.

| Befehl | Beschreibung |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

Siehe [Pinion-Protokoll](../advanced/pinion.md).

### Pinker

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Cache vs. Quelle |
| `pinker:rebuild` | `pinker:rb` | Cache neu bauen |
| `pinker:diff` | `pinker:df` | Unterschiede anzeigen |
| `pinker:clear` | `pinker:cl` | Cache leeren |
| `pinker:overrides` | `pinker:ov` | Overrides auflisten |

### Qualität & Doku

| Befehl | Beschreibung |
|---------|-------------|
| `test` / `pest` | App-Tests ausführen (`--unit`, `--feature`) |
| `api:docs` | REST-API-Dokumentation |
| `graphql:docs` | GraphQL-Schema-Dokumentation |

### Meta

| Befehl | Aliase | Beschreibung |
|---------|---------|-------------|
| `list` | — | Gruppierte Befehlsübersicht |
| `version` | `ver` | CLI-Version |

---

## App-Erkennung

Pinx wandert vom aktuellen Arbeitsverzeichnis nach oben, bis es ein gültiges Single-App-Projekt findet:

1. `app.php` existiert und gibt ein Array mit einem nicht leeren `package`-Schlüssel zurück
2. `pinoox/pincore` ist in der `composer.json` als Abhängigkeit eingetragen, oder `vendor/pinoox/pincore` ist vorhanden

Überschreiben Sie das erkannte Paket mit Umgebungsvariablen:

| Variable | Zweck |
|----------|---------|
| `PINX_PACKAGE` | CLI-Zielpaket erzwingen |
| `PINOOX_DEV_APP` | Alias für `PINX_PACKAGE` |
| `PINX_DEV=1` | Dev-Modus (wird von pinx automatisch gesetzt, wenn an pincore delegiert wird) |

---

## Voraussetzungen

- **PHP** ≥ 8.2 mit den von `pinoox/pincore` benötigten Erweiterungen
- **Composer** 2.x
- **Node.js** + npm — nur bei Verwendung von Vite/Vue/React-Frontends
- **Datenbank** — MySQL/MariaDB oder was Ihre `.env` konfiguriert (optional für statische/Twig-only-Apps)

---

## Verwandte Dokumente

- [Pinoox installieren](./installing-pinoox.md)
- [Pinoox-CLI-Referenz (Multi-App)](./cli-reference.md)
- [Ihre erste App](./your-first-app.md)
- [app.php-Manifest](./app-manifest.md)

---

[← Zurück zur Übersicht](../README.md)

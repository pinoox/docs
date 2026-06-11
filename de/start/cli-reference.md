# Pinoox-CLI-Referenz

[← Zurück zur Übersicht](../README.md)

Führen Sie jeden Befehl aus dem **Projektstammverzeichnis** aus:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Wenn ein Paket erforderlich ist und nicht angegeben wird, zeigt Pinoox eine interaktive Auswahl an.

> Für **Single-App**-Projekte verwenden Sie die eigenständige [Pinx CLI](./pinx-cli.md) (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Häufige Aliase

| Alias | Befehl |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## Apps

| Befehl | Zweck |
|---------|---------|
| `app:create {package}` | App-Gerüst erstellen (`--simple`, `--stack`, `--profile`) |
| `app:list` | Apps auflisten |
| `app:delete` | App entfernen |
| `app:router set /path {package}` | URL-Zuordnung |
| `app:domain` | Host → App-Zuordnung |
| `app:resolve` | Aktive App debuggen |

---

## Scaffolding

| Befehl | Ausgabe |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest-Klasse |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest-Datei |
| `theme:frontend` | Frontend-Tooling (Vue/React/Twig) |

---

## Datenbank

| Befehl | Zweck |
|---------|---------|
| `migrate {package}` | Migrationen ausführen (App, `platform`, `pincore`) |
| `migrate:create` | Neue Migrationsdatei |
| `migrate:status` / `migrate:rollback` | Status / Rollback |
| `seeder:run` | Seeder ausführen |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | Rohes SQL (Debug) |

---

## Cache & Pinker

| Befehl | Zweck |
|---------|---------|
| `cache:build` / `cache:clear` | Laufzeit-Cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + Konfiguration zurücksetzen |

---

## Schedule

| Befehl | Zweck |
|---------|---------|
| `schedule:list` | Cron-Aufgaben auflisten |
| `schedule:run` | Fällige Aufgaben ausführen |

Siehe [Schedule](../advanced/schedule.md).

---

## Router

| Befehl | Zweck |
|---------|---------|
| `route:actions {package}` | Named Actions auflisten |

---

## Pinx-Paketierung

| Befehl | Zweck |
|---------|---------|
| `pinx:build` | `.pinx`-Paket bauen |
| `pinx:install` | Paket installieren |
| `pinx:info` | Metadaten |
| `wizard:list` / `wizard:install` | Installationsassistent |

---

## Entwicklung

| Befehl | Zweck |
|---------|---------|
| `test` | Pest-Tests |
| `serve` | Eingebauter Entwicklungsserver |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm über alle Apps hinweg |
| `version` / `mode:show` | Version / Laufzeitmodus |

---

## Paket-Argument

| Wert | Bedeutung |
|-------|---------|
| `com_my_shop` | Bestimmte App |
| `platform` | Plattform-Migrationen/-Patches/-Seeder |
| `pincore` | Framework-Kern |
| `all` | Alle Apps (Cache/Pinker) |

---

## Verwandte Dokumente

- [Ihre erste App](./your-first-app.md)
- [Migrationen](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← Zurück zur Übersicht](../README.md)

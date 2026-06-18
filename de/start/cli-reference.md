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
| `users` | `user:list` |
| `roles` | `role:list` |
| `permissions` | `permission:list` |
| `tokens` | `token:list` |
| `files` | `file:list` |
| `pinion` | `pinion:list` |
| `databases` | `db:list` |
| `make:permission` | `permission:create` |

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

### Connection management (`db:*`)

Inspect and persist platform connections (Pinker `~database`) and per-app `database` blocks.

| Command | Purpose |
|---------|---------|
| `db:list` | List platform connections or app DB settings (`--all`, `--test`, `--json`) |
| `db:show {target}` | Connection details for `platform`, a connection name, or an app package |
| `db:test {target}` | Test connectivity; ad-hoc probe with `--host`, `--database`, `--username`, … |
| `db:create {name}` | Add a platform connection (interactive or `--set key=value`) |
| `db:update {target}` | Update platform or app database settings |
| `db:prefix {package} {prefix}` | Change app table prefix (`--use` to pick platform connection) |

```bash
php pinoox db:list --test
php pinoox db:show platform
php pinoox db:show com_my_shop --json
php pinoox db:test mysql
php pinoox db:prefix com_my_shop shop_
```

> CLI writes to **Pinker**. Runtime may still override values when `.env` defines `DB_*` keys (`env-over-pinker`).

See [Database getting started](../database/getting-started.md).

---

## Users, roles & permissions

Commands respect `transport.user` / access scope (usually `platform`). Omit `{package}` to pick from the interactive list.

| Command | Purpose |
|---------|---------|
| `user:list` / `user:show` / `user:create` / `user:update` / `user:delete` | User CRUD |
| `user:password` / `user:status` / `user:role` | Password, status, role assignment |
| `role:list` / `role:create` / `role:show` / `role:update` / `role:delete` | Role CRUD |
| `role:permission` | Attach or detach permissions on a role |
| `permission:list` / `permission:create` / `permission:show` / `permission:delete` | Permission CRUD |

```bash
php pinoox user:list com_my_shop --status=active --json
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox permission:create com_my_shop blog.posts.edit
php pinoox role:permission editor --attach=blog.posts.edit
```

See [User management](../advanced/user-management.md) and [Access & permissions](../advanced/access-permissions.md).

---

## Tokens

Manage `TokenModel` rows for the transport scope (`transport.session_token` in `app.php`).

| Command | Purpose |
|---------|---------|
| `token:list` / `token:show` | Inspect tokens (keys masked in list output) |
| `token:create` | Create token for a user (`--user`, `--lifetime`, `--unit`) |
| `token:update` / `token:delete` | Update metadata or remove one token |
| `token:revoke-user` | Revoke all tokens for a user (like `Auth::revokeSessions`) |
| `token:purge` | Delete expired tokens |

```bash
php pinoox token:list platform
php pinoox token:create com_my_shop --user=1 --lifetime=30 --unit=day
php pinoox token:revoke-user 1
```

See [Token management](../advanced/token-management.md).

---

## Files

Manage upload metadata and storage for the `FileModel` scope (`transport.file_storage`).

| Command | Purpose |
|---------|---------|
| `file:list` / `file:show` | List or inspect records (shows storage `present` / `missing`) |
| `file:update` | Update metadata, access, or links |
| `file:delete` | Remove DB row, storage, or both (`--db-only`, `--storage-only`, `--force`) |
| `file:purge` | Bulk cleanup of orphaned or old files |

```bash
php pinoox file:list com_my_shop
php pinoox file:show 12
php pinoox file:delete 12 --storage-only --force
```

See [File management](../advanced/file-management.md).

---

## Pinion (wiederaufnahmefähige Uploads)

Laufende Chunk-Upload-Sessions verwalten (Temp-Speicher unter `storage/pinion`):

| Befehl | Zweck |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

Siehe [Pinion-Protokoll](../advanced/pinion.md).

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

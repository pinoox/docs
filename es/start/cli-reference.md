# Referencia de la CLI de Pinoox

[← Volver al índice](../README.md)

Ejecuta todos los comandos desde la **raíz del proyecto**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Cuando se requiere un paquete y se omite, Pinoox muestra un selector interactivo.

> Para proyectos de **una sola app**, usa la [Pinx CLI](./pinx-cli.md) independiente (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Alias comunes

| Alias | Comando |
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

| Comando | Propósito |
|---------|---------|
| `app:create {package}` | Generar app (`--simple`, `--stack`, `--profile`) |
| `app:list` | Listar apps |
| `app:delete` | Eliminar app |
| `app:router set /path {package}` | Mapeo de URL |
| `app:domain` | Mapa host → app |
| `app:resolve` | Depurar la app activa |

---

## Scaffolding (generación de código)

| Comando | Salida |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | Clase FormRequest |
| `seeder:create` | `database/seed/` |
| `test:create` | Archivo Pest |
| `theme:frontend` | Herramientas de frontend (Vue/React/Twig) |

---

## Base de datos

| Comando | Propósito |
|---------|---------|
| `migrate {package}` | Ejecutar migraciones (app, `platform`, `pincore`) |
| `migrate:create` | Nuevo archivo de migración |
| `migrate:status` / `migrate:rollback` | Estado / rollback |
| `seeder:run` | Ejecutar seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | SQL directo (depuración) |

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

## Pinion (subidas reanudables)

Gestionar sesiones de subida por fragmentos en curso (almacenamiento temporal en `storage/pinion`):

| Comando | Propósito |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

Ver [protocolo Pinion](../advanced/pinion.md).

---

## Caché y Pinker

| Comando | Propósito |
|---------|---------|
| `cache:build` / `cache:clear` | Caché de runtime |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Restablecer Pinker + configuración |

---

## Schedule

| Comando | Propósito |
|---------|---------|
| `schedule:list` | Listar tareas cron |
| `schedule:run` | Ejecutar tareas pendientes |

Consulta [Schedule](../advanced/schedule.md).

---

## Router

| Comando | Propósito |
|---------|---------|
| `route:actions {package}` | Listar Named Actions |

---

## Empaquetado Pinx

| Comando | Propósito |
|---------|---------|
| `pinx:build` | Construir paquete `.pinx` |
| `pinx:install` | Instalar paquete |
| `pinx:info` | Metadatos |
| `wizard:list` / `wizard:install` | Asistente de instalación |

---

## Desarrollo

| Comando | Propósito |
|---------|---------|
| `test` | Pruebas con Pest |
| `serve` | Servidor de desarrollo integrado |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm en todas las apps |
| `version` / `mode:show` | Versión / modo de runtime |

---

## Argumento de paquete

| Valor | Significado |
|-------|---------|
| `com_my_shop` | App específica |
| `platform` | Migraciones/patches/seeders de la plataforma |
| `pincore` | Núcleo del framework |
| `all` | Todas las apps (cache/pinker) |

---

## Documentación relacionada

- [Tu primera app](./your-first-app.md)
- [Migraciones (Migrations)](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← Volver al índice](../README.md)

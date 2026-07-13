# Pinoox CLI reference

[← Back to index](../README.md)

Run every command from the **project root**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

When a package is required and omitted, Pinoox shows an interactive picker.

> For **single-app** projects, use the standalone [Pinx CLI](./pinx-cli.md) (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Common aliases

| Alias | Command |
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
| `databases` | `db:list` |
| `pinion` | `pinion:list` |
| `make:permission` | `permission:create` |

---

## Apps

| Command | Purpose |
|---------|---------|
| `app:create {package}` | Scaffold app (`--simple`, `--stack`, `--profile`) |
| `app:list` | List apps |
| `app:delete` | Remove app |
| `app:router set /path {package}` | URL mapping |
| `app:domain` | Host → app map |
| `app:resolve` | Debug active app |

---

## Scaffolding

| Command | Output |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest class |
| `seeder:create` | `database/seeders/` |
| `factory:create` | `database/factories/` |
| `test:create` | Pest file |
| `theme:frontend` | Frontend tooling (Vue/React/Vite) — see [Frontend & Vite](../basic/frontend-vite.md) |

---

## Database

| Command | Purpose |
|---------|---------|
| `migrate {package}` | Run migrations (app, `platform`, `pincore`) |
| `migrate:create` | New migration file |
| `migrate:status` / `migrate:rollback` | Status / rollback |
| `seeder:run` | Run seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | Raw SQL (debug; `--dry-run` to print without executing) |

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
| `user:login` / `user:logout` | Issue or clear session/JWT token; `--force` writes/clears `PINOOX_LOGIN_TOKEN` |
| `role:list` / `role:create` / `role:show` / `role:update` / `role:delete` | Role CRUD |
| `role:permission` | Attach or detach permissions on a role |
| `permission:list` / `permission:create` / `permission:show` / `permission:delete` | Permission CRUD |

```bash
php pinoox user:list com_my_shop --status=active --json
php pinoox user:login com_my_shop --id=1 --force
php pinoox user:logout --force
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox permission:create com_my_shop blog.posts.edit
php pinoox role:permission editor --attach=blog.posts.edit
```

See [User management](../advanced/user-management.md) (including local `PINOOX_LOGIN` / `PINOOX_LOGIN_TOKEN`) and [Access & permissions](../advanced/access-permissions.md).

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

## Pinion (resumable uploads)

Manage in-progress chunked upload sessions (temp storage under `storage/pinion`):

| Command | Purpose |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

See [Pinion protocol](../advanced/pinion.md).

---

## Pinroll (release & deploy)

Build packages and deploy to configured hosts. Requires `pinoox/pinroll` and project config from `pinroll:init`.

| Command | Purpose |
|---------|---------|
| `pinroll:init` | Scaffold `pinroll/pinroll.config.php` |
| `pinroll:connect` | Setup / verify host (`--reset` to redo) |
| `pinroll:apps` | Set `hosts.*.apps` |
| `pinroll:vendor` | Export `vendor/` for host install or core update |
| `pinroll:gate` | Build / upload PinGate |
| `pinroll:check` | Test host / PinGate |
| `pinroll:push` | Build and upload only |
| `pinroll:install` | Install staged release on host |
| `pinroll:deploy` | Push + install (go live) |
| `pinroll:rollback` | Rollback via PinGate or local re-push |
| `pinroll:cleanup` | Prune old archives (`--local`, `--dry-run`) |
| `pinroll:build` | Build only |
| `pinroll:status` | Rollout status |
| `pinroll:history` | Deploy history |
| `pinroll:pull` | Pull newer manifest from release server |

```bash
php pinoox pinroll:init
php pinoox pinroll:connect
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:deploy
```

See [Pinroll deploy guide](../deploy/pinroll.md).

---

## Cache & Pinker

| Command | Purpose |
|---------|---------|
| `cache:build` / `cache:clear` | Runtime cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Reset Pinker + config |

---

## Schedule

| Command | Purpose |
|---------|---------|
| `schedule:list` | List cron tasks |
| `schedule:run` | Run due tasks |

See [Schedule](../advanced/schedule.md).

---

## Router

| Command | Purpose |
|---------|---------|
| `route:actions {package}` | List Named Actions |

---

## Pinx packaging

| Command | Purpose |
|---------|---------|
| `pinx:build` | Build `.pinx` package |
| `pinx:install` | Install package |
| `pinx:info` | Metadata |
| `wizard:list` / `wizard:install` | Install wizard |

---

## Development

| Command | Purpose |
|---------|---------|
| `test` | Pest tests |
| `serve` | Built-in dev server |
| `theme:frontend` / `fe` | Vite dev, build, watch — [Frontend & Vite](../basic/frontend-vite.md) |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm across apps |
| `version` / `mode:show` | Version / runtime mode |

### `theme:frontend` (`fe`)

```bash
php pinoox fe info com_my_shop
php pinoox fe install com_my_shop
php pinoox fe dev com_my_shop              # PHP serve + Vite HMR (waits for Vite)
php pinoox dev com_my_shop                 # shortcut for fe dev
php pinoox fe dev com_my_shop --no-serve   # Vite only (MAMP / external PHP)
php pinoox fe dev com_my_shop --fix-vite   # Wire @pinooxhq/vite-plugin into vite.config.js
php pinoox fe dev:apps                     # Multi-app: one serve + Vite per package
php pinoox fe dev:apps --apps=com_pinoox_manager,com_pinoox_welcome
php pinoox fe build com_my_shop
php pinoox fe watch com_my_shop
php pinoox fe scaffold com_my_shop vue
php pinoox serve --app=com_my_shop@/manager # manifest only (PINOOX_VITE_HMR=0)
```

`fe dev` sets `PINOOX_VITE_HMR=1`, resolves `VITE_*` URLs from the app router, and injects missing values at runtime. Open the **PHP URL** in the terminal — not the Vite port. `php pinoox serve` always uses built manifest assets. See [Frontend & Vite](../basic/frontend-vite.md) and [@pinooxhq/vite-plugin](../basic/vite-plugin.md).

---

## Package argument

| Value | Meaning |
|-------|---------|
| `com_my_shop` | Specific app |
| `platform` | Platform migrations/patches/seeders |
| `pincore` | Framework core |
| `all` | All apps (cache/pinker) |

---

## Related docs

- [Frontend & Vite](../basic/frontend-vite.md)
- [Your first app](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)
- [User management](../advanced/user-management.md)
- [Access & permissions](../advanced/access-permissions.md)
- [Token management](../advanced/token-management.md)
- [File management](../advanced/file-management.md)

---

[← Back to index](../README.md)

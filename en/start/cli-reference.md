# Pinoox CLI reference

[← Back to index](../../README.md)

Run every command from the **project root**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

When a package is required and omitted, Pinoox shows an interactive picker.

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
| `seeder:create` | `database/seed/` |
| `test:create` | Pest file |
| `theme:frontend` | Frontend tooling (Vue/React/Twig) |

---

## Database

| Command | Purpose |
|---------|---------|
| `migrate {package}` | Run migrations (app, `platform`, `pincore`) |
| `migrate:create` | New migration file |
| `migrate:status` / `migrate:rollback` | Status / rollback |
| `seeder:run` | Run seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | Raw SQL (debug) |

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
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm across apps |
| `version` / `mode:show` | Version / runtime mode |

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

- [Your first app](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← Back to index](../../README.md)

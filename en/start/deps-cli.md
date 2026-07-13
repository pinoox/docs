# Dependencies CLI (`deps`)

[← Back to index](../README.md)

Install, update, and inspect **Composer** (PHP) and **npm** (theme frontend) dependencies across a Pinoox project from one command.

Run from the project root:

```bash
php pinoox deps status all
php pinoox deps install all
php pinoox deps install com_pinoox_manager
```

On Pinx single-app projects, the same actions are available as:

```bash
pinx deps:st
pinx deps:i
pinx deps:up
```

---

## What it manages

| Manifest | Typical path | Installed when |
|----------|--------------|----------------|
| `composer.json` (platform) | project root | `vendor/autoload.php` exists |
| `composer.json` (app) | `apps/{package}/composer.json` | `apps/{package}/vendor/autoload.php` exists |
| `package.json` (theme) | `apps/{package}/theme/{theme}/package.json` | `node_modules/` exists in that theme folder |

The command discovers targets automatically. You choose the **scope** (whole project, platform only, or one app).

---

## Actions

| Action | Purpose |
|--------|---------|
| `status` | Inventory table — installed vs missing |
| `install` | `composer install` and `npm ci` / `npm install` |
| `update` | `composer update` and `npm update` |

---

## Scopes

| Scope | Targets |
|-------|---------|
| `all` | Root composer + every app composer + every active theme `package.json` |
| `platform` | Root `composer.json` only |
| `com_my_shop` | That app's `composer.json` + active theme from `app.php` |

Interactive mode: omit the scope argument and pick from the numbered table.

---

## Step-by-step UI

`install` and `update` run as a **workflow**:

1. **Header** — action, scope, target count  
2. **Execution plan** — table of all steps (pending → running → done)  
3. **Progress bar** — overall step counter  
4. **Active step panel** — path, manifest, live filtered output  
5. **Collapsed summary** — each finished step becomes one `✔ done` line  
6. **Run summary** — final table with timing and exit state  

Live output is streamed while Composer/npm runs. By default only meaningful lines are shown (package operations, install progress, warnings, errors). Use Symfony verbose flags for full logs:

```bash
php pinoox deps install all -v
php pinoox deps install all -vv
```

For CI or scripts without panels:

```bash
php pinoox deps install platform --plain --no-interaction
```

---

## Options

| Option | Description |
|--------|-------------|
| `--composer-only` | Skip npm targets |
| `--npm-only` | Skip Composer targets |
| `--theme=spark` | Theme folder for a single app (default: `app.php` → `theme`) |
| `--all-themes` | Every theme under `apps/{package}/theme/` with `package.json` |
| `--production` | Composer: `--no-dev` + optimized autoloader |
| `--no-ci` | npm: always `npm install` (skip `npm ci` even when lockfile exists) |
| `--plain` | Simple sections, no step panels |
| `--continue-on-error` | Run remaining targets after a failure |
| `-v` / `-vv` | Verbose live output from underlying tools |

---

## Examples

### First-time project setup

```bash
php pinoox deps install platform
php pinoox deps install all --npm-only
```

### One app before build

```bash
php pinoox deps install com_pinoox_manager
php pinoox theme:frontend build com_pinoox_manager --no-install
```

`theme:frontend build` can skip npm when dependencies are already installed via `deps`.

### Production app package (no dev PHP deps)

```bash
php pinoox deps install com_my_shop --production --composer-only
```

### All themes in an app

```bash
php pinoox deps install com_pinoox_manager --all-themes --npm-only
```

### Check what is missing

```bash
php pinoox deps status all
```

---

## Behaviour notes

- **Composer binary** — uses `COMPOSER_BIN`, project `composer.phar`, or `composer` from `PATH` (same resolution as `pinx:build`).
- **npm on Windows** — runs `npm.cmd` automatically.
- **npm ci** — used when `package-lock.json` exists (unless `--no-ci`). Falls back to `npm install` if `ci` fails.
- **Failure** — stops on first failed step unless `--continue-on-error` is set.
- **Timeouts** — each target allows up to 15 minutes.

---

## Related commands

| Command | Overlap |
|---------|---------|
| `theme:frontend build` | Builds assets; can install npm internally (`--no-install` to skip) |
| `pinx:build` | Runs `composer install --no-dev` inside the app for distributable packages |
| `composer install` (manual) | Same as `php pinoox deps install platform` |

---

## Troubleshooting

| Problem | What to try |
|---------|-------------|
| `composer` not found | Install Composer globally or place `composer.phar` in project root |
| `npm` not found | Install Node.js LTS and ensure `npm` is on `PATH` |
| Step fails with no useful output | Re-run with `-vv` or check the **Run summary** tail |
| Windows path issues | Use `php pinoox` from project root; paths are normalized automatically |

---

## Related docs

- [CLI reference](./cli-reference.md)
- [Pinx CLI](./pinx-cli.md)
- [Frontend & Vite](../basic/frontend-vite.md)
- [App dependencies](./app-depends.md) — app-to-app `depends` / `use_app()` (different from Composer/npm)

---

[← Back to index](../README.md)

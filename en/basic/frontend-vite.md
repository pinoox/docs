# Frontend & Vite

[← Back to index](../README.md)

Pinoox themes can ship a **Vite** frontend (Vue, React, or vanilla JS). PHP renders Twig; Vite builds and serves client assets. The `php pinoox fe` command (alias `theme:frontend`) wires dev URLs, hot reload, and production manifests. Themes use the npm package [**@pinooxhq/vite-plugin**](./vite-plugin.md) in `vite.config.js`.

---

## Theme layout

```
apps/com_my_shop/theme/default/
├── frontend.config.php    # profile, stack, mount; entry/manifest auto-detected
├── package.json           # @pinooxhq/vite-plugin in devDependencies
├── vite.config.js         # pinoox() from @pinooxhq/vite-plugin
├── .env                   # optional Vite overrides (runtime injection by default)
├── .pinoox/
│   └── dev.json           # written by Vite in dev (HMR signal for PHP)
├── dist/
│   └── .vite/manifest.json
├── src/
└── partials/scripts.twig
```

App paths come from **AppEngine** (`apps/`, `apps.config.php`, single-app `~`, external registry, …) — the CLI resolves `{app}/theme/{theme}/` from there, not from a hard-coded folder name.

`frontend.config.php` is the PHP-side source of truth for profile, stack, mount point, and dev settings. **Entry** and **manifest** paths are inferred from the stack and build output when omitted. See [app manifest](../start/app-manifest.md) for the app-level `frontend` block. Theme metadata and inheritance live in [theme.php](./theme-manifest.md).

Minimal example (stack detection fills in the rest):

```php
<?php

return [
    'profile' => 'hybrid',
    'stack' => 'vue',
    'mount' => '#app',
];
```

---

## Frontend profiles

Choose **one profile** per theme. Profiles describe *how* the theme renders — not which JS library you use.

| Profile | Use case | Rendering | SEO | Node in production |
|---------|----------|-----------|-----|-------------------|
| **`twig`** | Landing, content, simple pages | Full Twig HTML | Excellent | No |
| **`hybrid`** | Public shop, blog, catalog | Twig + Vite islands | Excellent (meta in PHP) | No |
| **`spa`** | Admin panel, dashboard | Twig shell + client router | Not required (behind auth) | No |
| **`ssg`** | Static marketing pages | Pre-render at build time | Excellent | No (build-time only) |

Decision tree:

```text
Is it an admin panel?
  yes → spa
  no  → Is SEO important?
          no  → twig
          yes → Heavy client UI on public pages?
                  no  → twig
                  yes → Full SPA on public URLs?
                          yes → ssg (or hybrid if routes are few)
                          no  → hybrid
```

Set `'profile' => 'spa'` (etc.) in `frontend.config.php` or override from `app.php` → `frontend`.

---

## Multi-context themes

Site + panel (or more) can each have their own theme folder and per-context `frontend` block. Attach `theme.site` / `theme.panel` flows on HTML routes — see [Theme contexts](./theme-contexts.md).

---

## CLI — `php pinoox fe`

Run from the **project root**. The first argument is an **app package** (`com_my_shop`) or a **theme folder** (`spark`, `default`). When the theme name exists in one app only, the package is resolved automatically.

**Recommended order:** `{target} {action}` — legacy `{action} {target}` still works.

```bash
php pinoox fe spark dev              # theme folder → package auto-resolved
php pinoox dev spark                 # shortcut for fe spark dev
php pinoox fe com_my_shop dev        # explicit package
php pinoox fe dev spark              # legacy order
php pinoox dev platform              # full platform router (not single-app mount)
```

**Shortcuts:** `php pinoox dev {target}` → `fe {target} dev`; `php pinoox fe:install` / `php pinoox fe:build` open the theme wizard (same as `fe install` / `fe build` when `--theme` is omitted).

| Action | Command | Purpose |
|--------|---------|---------|
| `info` | `php pinoox fe spark info` | Stack, manifest, dev state, Vite wiring |
| `install` | `php pinoox fe spark install` | Install theme JS dependencies (`npm` / `bun` / … — see below) |
| `dev` | `php pinoox fe spark dev` | PHP `serve` + Vite HMR (waits until Vite is ready) |
| `dev` | `php pinoox dev spark` | Same as `fe spark dev` (shortcut) |
| `dev:apps` | `php pinoox fe dev:apps` | One PHP `serve` + Vite for **multiple** apps |
| `build` | `php pinoox fe spark build` | Production build (`dist/`) |
| `watch` | `php pinoox fe spark watch` | Rebuild on save (no HMR) |
| `scaffold` | `php pinoox fe spark scaffold vue` | Copy vue/react/vite stub into theme |

**Aliases:** `theme:frontend`, `frontend`.

### `--theme` (folder, context, or all)

Use when an app has **multiple theme folders** or **theme contexts** (site / panel / …):

```bash
php pinoox fe com_my_shop dev --theme=panel    # one context
php pinoox fe com_my_shop dev --theme=all      # every Vite context (separate Vite per theme)
php pinoox fe com_my_shop install --theme=all  # npm install in each context theme
```

Omit `--theme` to pick from an interactive list. Apps with multiple Vite contexts start **all** contexts by default in `fe dev` unless you pick one.

### `fe dev` options

| Option | Description |
|--------|-------------|
| `--no-serve` | Vite only; you run PHP yourself (MAMP, Apache, etc.) |
| `--serve-host` | PHP dev server host (default from `SERVER_HOST`) |
| `--serve-port` | PHP dev server port (default from `SERVER_PORT`) |
| `--serve-app` | Locked app for `php pinoox serve` (default: `package@/` for single-app dev; use `platform` for full router) |
| `--serve-domain` / `--domain` | Local hostname for browser URLs (PHP still binds `127.0.0.1`; e.g. `--domain=pinoox.test`) |
| `--no-fix-hosts` | Do not auto-update the system hosts file for `--domain` |
| `--network` / `-N` | Bind PHP + Vite on LAN (`0.0.0.0`) |
| `--vite-host` | Vite bind host (default `127.0.0.1`) |
| `--vite-network` | Bind Vite to `0.0.0.0` for LAN |
| `--verbose-vite` | Show full Vite startup URLs |
| `--fix-vite` | Auto-wire `@pinooxhq/vite-plugin` in `vite.config.js` |
| `--env-file` | Theme env file name (default `.env`) |
| `--no-install` | Skip JS dependency install |
| `--install` | Force JS dependency install |

### `fe dev:apps` — multiple apps

Use when you develop **more than one app** on the same platform (e.g. welcome at `/` and manager at `/manager`). One terminal runs a shared `php pinoox serve` plus a Vite dev server per app — each on its own port.

```bash
# Interactive — table of packages, then type numbers or names
php pinoox fe dev:apps

# Explicit package names (full com_* names only)
php pinoox fe dev:apps com_pinoox_manager,com_pinoox_welcome
php pinoox fe dev:apps --apps=com_pinoox_manager,com_pinoox_welcome
```

**Interactive input** (after the table):

| You type | Result |
|----------|--------|
| `1,7` | Apps by row number from the table |
| `com_pinoox_manager,com_pinoox_welcome` | Full package names (comma-separated) |
| `all` | Every app that has a frontend theme |

Short aliases like `manager` or `welcome` are **not** accepted — use full package names (`com_*`).

| Option | Description |
|--------|-------------|
| `--apps` | Comma-separated package list (for scripts / CI) |
| `--serve-host` | PHP dev server host |
| `--serve-port` | PHP dev server port (default `8000`) |
| `--fix-vite` | Auto-wire `vite.config.js` for each theme |
| `--no-install` | Skip npm install |

The CLI prints one URL per app and prefixes Vite logs (`[manager]`, `[welcome]`, …). **Ctrl+C** stops PHP and all Vite processes.

Assign a unique Vite port per theme in `frontend.config.php` when defaults collide:

```php
'dev' => ['port' => 5174],
```

`dev-stack` is a deprecated alias for `dev:apps`.

**Do not** run two `fe dev` commands without `--no-serve` — both try to bind port `8000` and only one app is routed. Prefer `fe dev:apps` or: one `php pinoox serve` plus `fe dev {package} --no-serve` per app.

**Workflow:** the CLI waits until Vite is ready, then prints URLs. Open the **PHP app URL** in the browser (e.g. `http://127.0.0.1:8000/manager` for platform router, or `http://127.0.0.1:8000/` for single-app `dev spark`), **not** the Vite port. PHP injects HMR tags when HMR mode is active and `.pinoox/dev.json` has a `viteUrl`.

**Single-app dev** mounts the package at `/` (`package@/`). **Platform dev** uses the full router — prefer `fe dev:apps` when multiple apps need HMR at once.

---

## HMR vs manifest (`serve` vs `fe dev`)

PHP chooses dev HMR or production manifest using `PINOOX_VITE_HMR` and runtime checks:

| Command | `PINOOX_VITE_HMR` | PHP serves | Twig `vite_tags()` |
|---------|-------------------|------------|-------------------|
| `php pinoox fe dev` / `php pinoox dev` | `1` | HMR via `.pinoox/dev.json` + Vite | Vite dev server |
| `php pinoox serve` | `0` | Built assets only | `dist/.vite/manifest.json` |
| `pinx dev` (single-app) | `1` when Vite stack is set | Same as `fe dev` | HMR |
| `pinx dev --no-frontend` | `0` | Manifest only | Built assets |

`php pinoox serve` never enables HMR — even if `.pinoox/dev.json` exists from a previous dev session. Use `fe dev` when you want live reload.

When `APP_ENV=production`, Pinoox always uses the manifest regardless of `.pinoox/dev.json`.

---

## Environment variables

### Project package manager (npm / bun / pnpm / yarn)

By default, theme frontend commands run **`npm run dev`**, **`npm install`**, and **`npm ci`** (when a lockfile exists). Set this in the **project root** `.env` to use another JS package manager:

```env
PINOOX_JS_PACKAGE_MANAGER=bun
```

Supported values: `npm` (default), `bun`, `pnpm`, `yarn`.

| Command | With `bun` |
|---------|------------|
| `php pinoox dev` / `pinx dev` | `bun run dev` (Vite HMR) |
| `php pinoox fe install` | `bun install` |
| `php pinoox fe build` | `bun run build` |
| `php pinoox deps install` (npm targets) | `bun install --frozen-lockfile` when `bun.lock` exists |

When the env var is **unset**, Pinoox auto-detects from lockfiles in the theme folder:

| Lockfile | Manager |
|----------|---------|
| `bun.lock` / `bun.lockb` | bun |
| `pnpm-lock.yaml` | pnpm |
| `yarn.lock` | yarn |
| (none) | npm |

**Bun setup:** install [Bun](https://bun.sh), run `bun install` once in the theme folder, then `php pinoox dev {theme}` or `pinx dev`. Theme `package.json` scripts stay the same — only the runner changes.

### Runtime (default)

On `fe dev`, Pinoox resolves dev URLs from the **app router** (mount path, proxy prefixes) and injects missing `VITE_*` values into the JS package-manager process (npm, bun, …). **The theme `.env` file is not modified on disk** unless you opt in with `ENV_SERVER_SYNC=true` (below). If `.env` is missing but `.env.example` exists, the CLI copies the example once.

Existing values in theme `.env` always win. Auto-resolved values fill only empty keys at runtime.

| Variable | Purpose |
|----------|---------|
| `VITE_SERVER_URL` | PHP app base URL (for Vite proxy) |
| `VITE_DEV_PORT` | Vite dev server port |
| `VITE_DEV_SERVER` | Full Vite origin URL |
| `VITE_DEV_PROXY` | Comma-separated mount paths to proxy |
| `VITE_DEV_REFRESH` | Extra watch globs (set by CLI for Flow, routes, Controller) |
| `VITE_DEV` | Set to `true` in dev by CLI |
| `PINOOX_CORE_PATH` | Path to pincore (for shared stubs) |

### Persist autogenerated block (opt-in)

Add to theme `.env`:

```env
ENV_SERVER_SYNC=true
```

When `true`, each `fe dev` run writes or updates a marked block in the env file:

```env
# @pinoox-fe-dev autogenerated
VITE_DEV=true
VITE_BUILD_OUT_DIR=dist
VITE_SERVER_URL=http://127.0.0.1:8000/manager
VITE_DEV_PORT=5173
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_DEV_PROXY=/manager
# @pinoox-fe-dev end
```

Default is `false` (or omit the key). Manual keys **outside** the block are never removed.

Use `--env-file=.env.local` to target another file.

---

## `frontend.config.php` dev overrides

Router-based detection is enough for most multi-app installs. Override when mounts or ports differ:

```php
<?php

return [
    'profile' => 'hybrid',
    'stack' => 'vue',
    'mount' => '#app',
    'dev' => [
        'port' => 5174,
        'prefer_manifest' => false,   // true: built manifest even when .pinoox/dev.json exists
        'force' => false,             // true: always HMR (ignores prefer_manifest)
        'server_url' => 'http://127.0.0.1:8000/my-shop',
        'proxy' => ['/my-shop', '/api'],       // replaces auto list
        'proxy_extra' => ['/uploads'],          // merged with auto list
    ],
];
```

| Key | Purpose |
|-----|---------|
| `mount` | DOM selector for SPA/hybrid mount (default `#app`) |
| `entry` / `entries` | Vite entry paths (default from stack, e.g. `src/main.js`) |
| `manifest` | Build manifest path (default `dist/.vite/manifest.json`) |
| `dev.prefer_manifest` | When `true` and a build exists, Twig uses manifest instead of the Vite dev server (unless `dev.force` or `VITE_DEV_FORCE`) |
| `dev.port` | Fixed Vite port; CLI allocates the next free port if taken |

`dev.server_url` and `dev.proxy` take precedence over router detection for Vite proxy targets. Env mirror: `VITE_PREFER_MANIFEST`, `VITE_DEV_FORCE`.

---

## `@pinooxhq/vite-plugin`

Install in the theme and call `pinoox()` from `vite.config.js`:

```js
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import pinoox from '@pinooxhq/vite-plugin';
import { pinooxVueTemplateOptions } from '@pinooxhq/vite-plugin/vue';

export default defineConfig({
    plugins: [
        pinoox(['src/main.js']),
        vue(pinooxVueTemplateOptions()),
    ],
});
```

`pinoox()` replaces the older pattern of importing `pinooxHot`, `pinooxServer`, and `pinooxRefresh` from a synced `vite.pinoox.mjs` file. Use `php pinoox fe dev --fix-vite` to migrate an old config.

Full API, stack examples (React, vanilla, multiple entries), and npm setup: [**@pinooxhq/vite-plugin**](./vite-plugin.md).

---

## Twig helpers

Registered globally in Twig views:

| Helper | Purpose |
|--------|---------|
| `vite_tags('src/main.js')` | Dev HMR or production `<script>` / `<link>` tags |
| `vite_tags(['src/a.js', 'src/b.scss'])` | Multiple entries |
| `vite_css_tags(...)` | Stylesheet tags only |
| `vite_js_tags(...)` | Script tags only |
| `vite_asset('src/logo.png')` | Versioned URL from manifest |

Example `main.twig`:

```twig
<head>
    {{ vite_tags('src/main.js')|raw }}
</head>
```

Hybrid pages (Twig + widget):

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/widgets/price-calculator.js')|raw }}
```

**Production:** run `php pinoox fe build {package}` so `dist/.vite/manifest.json` exists. Twig reads hashed filenames from the manifest.

**Development:** when `.pinoox/dev.json` has a `viteUrl` (written by Vite on start) and HMR mode is active, helpers point script tags at the Vite dev server.

---

## Dev vs production

| Mode | `APP_ENV` | Hot file | Manifest | Browser URL |
|------|-----------|----------|----------|-------------|
| Dev | `development` (etc.) | `.pinoox/dev.json` active | ignored when HMR resolves | PHP app URL |
| Prod | `production` | ignored | `dist/.vite/manifest.json` | PHP app URL |

When `APP_ENV=production`, Pinoox **never** enables Vite HMR — even if `.pinoox/dev.json` exists from a previous `fe dev` session. Built assets from the manifest are always used.

`php pinoox serve` sets `PINOOX_VITE_HMR=0` and serves built assets from the manifest — not the Vite dev server. Use `fe dev` for HMR.

---

## Mount paths and multi-app

For platform installs, `fe dev` reads each app’s router mount (e.g. `com_pinoox_manager` → `/manager`). `VITE_SERVER_URL` becomes `http://host:port/manager` and proxy prefixes include that path.

For **two or more apps at once**, use `php pinoox fe dev:apps` (see above). Each package gets its own `FrontendDevSession`, Vite port, and `.pinoox/dev.json` while PHP is served once through the full router.

Override with `frontend.config.php` or manual `.env` values when router detection is wrong.

---

## Related docs

- [@pinooxhq/vite-plugin](./vite-plugin.md)
- [Twig templates](./templates.md)
- [Theme contexts](./theme-contexts.md)
- [Theme manifest (`theme.php`)](./theme-manifest.md)
- [Views](./views.md)
- [Dependencies CLI (`deps`)](../start/deps-cli.md)
- [CLI reference — `theme:frontend`](../start/cli-reference.md)
- [Vite hybrid walkthrough](../examples/vite-hybrid-app.md)
- [Vue SPA walkthrough](../examples/vue-spa-app.md)

---

[← Back to index](../README.md)

# Frontend & Vite

[← Back to index](../README.md)

Pinoox themes can ship a **Vite** frontend (Vue, React, or vanilla JS). PHP renders Twig; Vite builds and serves client assets. The `php pinoox fe` command (alias `theme:frontend`) wires dev URLs, hot reload, and production manifests. Themes use the npm package [**@pinooxhq/vite-plugin**](./vite-plugin.md) in `vite.config.js`.

---

## Theme layout

```
apps/com_my_shop/theme/default/
├── frontend.config.php    # stack, manifest, dev overrides
├── package.json           # @pinooxhq/vite-plugin in devDependencies
├── vite.config.js         # pinoox() from @pinooxhq/vite-plugin
├── .env                   # optional Vite overrides (not modified by default)
├── dist/
│   ├── hot                # written by Vite in dev (HMR signal for PHP)
│   └── .vite/manifest.json
├── src/
└── partials/scripts.twig
```

`frontend.config.php` is the PHP-side source of truth for stack, entries, manifest path, and dev settings. See [app manifest](../start/app-manifest.md) for the app-level `frontend` block.

---

## CLI — `php pinoox fe`

Run from the **platform root**. Omit the package to pick from a list.

**Shortcut:** `php pinoox dev {package}` forwards to `fe {package} dev` with the same options (`--no-serve`, `--network`, `--fix-vite`, …).

| Action | Command | Purpose |
|--------|---------|---------|
| `info` | `php pinoox fe info {package}` | Stack, manifest, hot file, Vite wiring |
| `install` | `php pinoox fe install {package}` | `npm install` / `npm ci` in the theme |
| `dev` | `php pinoox fe dev {package}` | PHP `serve` + Vite HMR (waits until Vite is ready) |
| `dev` | `php pinoox dev {package}` | Same as `fe dev` (shortcut) |
| `dev:apps` | `php pinoox fe dev:apps` | One PHP `serve` + Vite for **multiple** apps |
| `build` | `php pinoox fe build {package}` | Production build (`dist/`) |
| `watch` | `php pinoox fe watch {package}` | Rebuild on save (no HMR) |
| `scaffold` | `php pinoox fe scaffold {package} vue` | Copy vue/react/vite stub into theme |

**Aliases:** `theme:frontend`, `frontend`.

### `fe dev` options

| Option | Description |
|--------|-------------|
| `--no-serve` | Vite only; you run PHP yourself (MAMP, Apache, etc.) |
| `--serve-host` | PHP dev server host (default from `SERVER_HOST`) |
| `--serve-port` | PHP dev server port (default from `SERVER_PORT`) |
| `--serve-app` | Locked app for `php pinoox serve` (default: `package@/` for single-app dev) |
| `--network` / `-N` | Bind PHP + Vite on LAN (`0.0.0.0`) |
| `--vite-host` | Vite bind host (default `127.0.0.1`) |
| `--vite-network` | Bind Vite to `0.0.0.0` for LAN |
| `--verbose-vite` | Show full Vite startup URLs |
| `--fix-vite` | Auto-wire `@pinooxhq/vite-plugin` in `vite.config.js` |
| `--env-file` | Theme env file name (default `.env`) |
| `--no-install` | Skip npm install |
| `--install` | Force npm install |

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

**Workflow:** the CLI waits until Vite is ready, then prints URLs. Open the **PHP app URL** in the browser (e.g. `http://127.0.0.1:8000/manager` for platform router, or `http://127.0.0.1:8000/` for single-app `fe dev com_pinoox_manager`), **not** the Vite port. PHP injects HMR tags when HMR mode is active and the hot file exists.

**Single-app dev** mounts the package at `/` (`package@/`). **Platform dev** uses the full router — prefer `fe dev:apps` when multiple apps need HMR at once.

---

## HMR vs manifest (`serve` vs `fe dev`)

PHP chooses dev HMR or production manifest using `PINOOX_VITE_HMR` and runtime checks:

| Command | `PINOOX_VITE_HMR` | PHP serves | Twig `vite_tags()` |
|---------|-------------------|------------|-------------------|
| `php pinoox fe dev` / `php pinoox dev` | `1` | HMR via `dist/hot` + Vite | Vite dev server |
| `php pinoox serve` | `0` | Built assets only | `dist/.vite/manifest.json` |
| `pinx dev` (single-app) | `1` when Vite stack is set | Same as `fe dev` | HMR |
| `pinx dev --no-frontend` | `0` | Manifest only | Built assets |

`php pinoox serve` never enables HMR — even if `dist/hot` exists from a previous dev session. Use `fe dev` when you want live reload.

When `APP_ENV=production`, Pinoox always uses the manifest regardless of `dist/hot`.

---

## Environment variables

### Runtime (default)

On `fe dev`, Pinoox resolves dev URLs from the **app router** (mount path, proxy prefixes) and passes missing `VITE_*` values to the npm process. **The theme `.env` file is not modified** unless you opt in (below).

Existing values in theme `.env` always win. Auto-resolved values fill only empty keys at runtime.

| Variable | Purpose |
|----------|---------|
| `VITE_HOT_FILE` | Relative path to hot file (default `dist/hot`) |
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
VITE_HOT_FILE=dist/hot
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
    'stack' => 'vue',
    'manifest' => 'dist/.vite/manifest.json',
    'dev' => [
        'port' => 5174,
        'hot' => 'dist/hot',
        'server_url' => 'http://127.0.0.1:8000/my-shop',
        'proxy' => ['/my-shop', '/api'],       // replaces auto list
        'proxy_extra' => ['/uploads'],          // merged with auto list
    ],
];
```

`dev.server_url` and `dev.proxy` take precedence over router detection for Vite proxy targets.

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

**Development:** when `dist/hot` exists and runtime is not production, helpers point script tags at the Vite dev server.

---

## Dev vs production

| Mode | `APP_ENV` | Hot file | Manifest | Browser URL |
|------|-----------|----------|----------|-------------|
| Dev | `development` (etc.) | `dist/hot` present | ignored when hot exists | PHP app URL |
| Prod | `production` | ignored | `dist/.vite/manifest.json` | PHP app URL |

When `APP_ENV=production`, Pinoox **never** enables Vite HMR — even if `dist/hot` exists from a previous `fe dev` session. Built assets from the manifest are always used.

`php pinoox serve` sets `PINOOX_VITE_HMR=0` and serves built assets from the manifest — not the Vite dev server. Use `fe dev` for HMR.

---

## Mount paths and multi-app

For platform installs, `fe dev` reads each app’s router mount (e.g. `com_pinoox_manager` → `/manager`). `VITE_SERVER_URL` becomes `http://host:port/manager` and proxy prefixes include that path.

For **two or more apps at once**, use `php pinoox fe dev:apps` (see above). Each package gets its own `FrontendDevSession`, Vite port, and `dist/hot` file while PHP is served once through the full router.

Override with `frontend.config.php` or manual `.env` values when router detection is wrong.

---

## Related docs

- [@pinooxhq/vite-plugin](./vite-plugin.md)
- [Twig templates](./templates.md)
- [Views](./views.md)
- [CLI reference — `theme:frontend`](../start/cli-reference.md)
- [Vite hybrid walkthrough](../examples/vite-hybrid-app.md)
- [Vue SPA walkthrough](../examples/vue-spa-app.md)

---

[← Back to index](../README.md)

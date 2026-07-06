# Frontend & Vite

[← Back to index](../README.md)

Pinoox themes can ship a **Vite** frontend (Vue, React, or vanilla JS). PHP renders Twig; Vite builds and serves client assets. The `php pinoox fe` command (alias `theme:frontend`) wires dev URLs, hot reload, and production manifests.

---

## Theme layout

```
apps/com_my_shop/theme/default/
├── frontend.config.php    # stack, manifest, dev overrides
├── package.json
├── vite.config.js
├── vite.pinoox.mjs        # synced from pincore on fe dev/build
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

| Action | Command | Purpose |
|--------|---------|---------|
| `info` | `php pinoox fe info {package}` | Stack, manifest, hot file, Vite wiring |
| `install` | `php pinoox fe install {package}` | `npm install` / `npm ci` in the theme |
| `dev` | `php pinoox fe dev {package}` | PHP `serve` + Vite HMR (zero-config URLs) |
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
| `--serve-app` | Locked app for `php pinoox serve` (default: current package) |
| `--fix-vite` | Auto-wire `pinooxHot` / `pinooxServer` in `vite.config.js` |
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

**Workflow:** open the **PHP app URL** in the browser (e.g. `http://127.0.0.1:8000/manager`), not the Vite port. The CLI prints one application URL; Vite URL lines are hidden. PHP injects HMR tags when the hot file exists.

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

## `vite.pinoox.mjs`

Synced into the theme on `fe dev` / `fe build`. Import from `vite.config.js`:

```js
import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import {
    pinooxHot,
    pinooxServer,
    pinooxRefresh,
    pinooxDevAssets,
    pinooxVueTemplateOptions,
    createPinooxViteConfig,
} from './vite.pinoox.mjs';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            vue(pinooxVueTemplateOptions()),
            pinooxHot({ env }),
            pinooxDevAssets(env),
            pinooxRefresh(true, env),
        ],
        server: pinooxServer(env),
    };
});
```

Or use the factory:

```js
import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import { createPinooxViteConfig } from './vite.pinoox.mjs';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return createPinooxViteConfig({
        env,
        stack: 'vue',
        plugins: [vue()],
    });
});
```

| Export | Role |
|--------|------|
| `pinooxHot` | Writes hot file so PHP enables HMR |
| `pinooxServer` | Port, proxy, `origin`, `printUrls: false` |
| `pinooxRefresh` | Full reload when Twig or app PHP changes |
| `pinooxDevAssets` | Rewrites `/src/...` to Vite origin in dev |
| `pinooxVueTemplateOptions` | Vue SFC asset URLs on Vite origin |
| `createPinooxViteConfig` | Zero-config factory for the above |

`pinooxServer` sets `server.origin` to the Vite URL so assets load correctly when the HTML page is served from PHP under a mount path (e.g. `/manager`).

### Full-page reload in dev

`pinooxRefresh` watches by default:

- Theme `**/*.twig` files
- App `Flow/` (middleware)
- App `routes/`
- App `Controller/`

CLI injects absolute PHP globs via `VITE_DEV_REFRESH`. Pass `env` to `pinooxRefresh(true, env)` so extra paths are picked up.

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

`php pinoox serve` respects the same rule: production mode serves the build, not the dev server.

---

## Mount paths and multi-app

For platform installs, `fe dev` reads each app’s router mount (e.g. `com_pinoox_manager` → `/manager`). `VITE_SERVER_URL` becomes `http://host:port/manager` and proxy prefixes include that path.

For **two or more apps at once**, use `php pinoox fe dev:apps` (see above). Each package gets its own `FrontendDevSession`, Vite port, and `dist/hot` file while PHP is served once through the full router.

Override with `frontend.config.php` or manual `.env` values when router detection is wrong.

---

## Related docs

- [Twig templates](./templates.md)
- [Views](./views.md)
- [CLI reference — `theme:frontend`](../start/cli-reference.md)
- [Vite hybrid walkthrough](../examples/vite-hybrid-app.md)
- [Vue SPA walkthrough](../examples/vue-spa-app.md)

---

[← Back to index](../README.md)

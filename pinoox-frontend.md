# Pinoox frontend dev (Vite + Twig hybrid themes)

> **Moved:** This quick reference is superseded by the official docs:
> - English: [Frontend & Vite](./en/basic/frontend-vite.md) · [@pinooxhq/vite-plugin](./en/basic/vite-plugin.md)
> - فارسی: [فرانت‌اند و Vite](./fa/basic/frontend-vite.md) · [@pinooxhq/vite-plugin](./fa/basic/vite-plugin.md)

Unified local development: **one command** starts PHP + Vite HMR.

```bash
php pinoox dev com_pinoox_manager    # shortcut for fe dev
php pinoox fe dev com_pinoox_manager   # PHP serve + Vite HMR
php pinoox fe dev:apps                 # platform — multiple apps
composer run dev                       # same as php pinoox dev (when configured)
```

Browse the **PHP app URL** printed in the terminal. Twig injects HMR via `vite_tags()`; `dist/hot` is written by `@pinooxhq/vite-plugin`.

## Commands

| Command | Action |
|---------|--------|
| `php pinoox dev {package}` | PHP serve + Vite HMR (shortcut) |
| `php pinoox fe {package} dev` | Same (`fe`, `frontend` aliases) |
| `php pinoox fe {package} build` | Production build |
| `php pinoox fe {package} watch` | Rebuild on save (no HMR) |
| `php pinoox fe dev:apps` | One PHP serve + Vite per app (platform) |
| `php pinoox serve --app=...` | Manifest only — no HMR |
| `php pinoox fe dev --no-serve` | Vite only (MAMP, Docker PHP) |

## Theme layout

```text
apps/{package}/theme/{name}/
├── frontend.config.php   # profile + stack (entry/manifest auto)
├── vite.config.js        # pinoox() from @pinooxhq/vite-plugin
├── package.json          # @pinooxhq/vite-plugin in devDependencies
├── partials/scripts.twig # pinoox_bootstrap() + vite_tags('src/main.js')
└── dist/hot              # written by Vite when dev runs (gitignore)
```

## Twig

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/main.js')|raw }}
```

## vite.config.js (Vue)

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

Full guide: [en/basic/vite-plugin.md](./en/basic/vite-plugin.md)

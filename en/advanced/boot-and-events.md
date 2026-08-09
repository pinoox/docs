# boot.php and events

[← Back to index](../README.md)

Besides `routes/`, you can register routes, API endpoints, flows, schedules, GraphQL, listeners, and DI bindings in **`boot.php`** — useful for **plugins**, micro-modules, or hooks into a host app.

Each app may ship `apps/{package}/boot.php`. The file returns a closure that receives `AppRegister` and runs **before** the request is handled.

---

## Lifecycle

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Pipeline stages

| Stage | Purpose |
|-------|---------|
| `boot.global` | Boot apps with `boot-global => true` on every request |
| `app.boot` | Boot the active route app (+ extenders via `extends`) |

### Boot events

| Name | When |
|------|------|
| `app.booting` / `app.booting.{package}` | Before boot commit |
| `app.booted` / `app.booted.{package}` | After integrate |
| `app.routes` / `app.routes.{package}` | When web routes are applied |
| `app.api` / `app.api.{package}` | When API registry is built |

Listen from `boot.php`:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Core request events

Dispatched automatically on every HTTP request by the framework (via `AppCoreEventSubscriber`):

| Name | When | Package variant | Named channel |
|------|------|-----------------|---------------|
| `app.route.matched` | After router matches a route | `app.route.matched.{package}` | `app.route.{routeName}` or `app.api.{routeName}` |
| `app.controller` | Before controller runs | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | Before response is sent | `app.response.{package}` | — |
| `app.exception` | On uncaught exception | `app.exception.{package}` | — |
| `app.terminate` | After response sent | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

Use **watches** (`onRoute`, `onApi`, …) for simple hooks; use **listen** on core events for full control or cross-app plugins.

---

## Three app modes

| Mode | Config | Behavior |
|------|--------|----------|
| **Route only** | `router.routes` only | Runs when the app URL is active |
| **Boot global** | `boot-global => true` | Boots on every HTTP request |
| **Boot + Route** | `boot.php` + routes | Default scaffold |

Plugin on a host app:

```php
'extends' => ['com_host_app'],
```

Your plugin boots only when the host boots (lighter than global).

---

## `app.php` keys for boot

These keys in `apps/{package}/app.php` control **whether** `boot.php` runs, **when** it runs, and whether boot output is cached. They configure the boot pipeline — they do not replace `boot.php` itself.

### Boot file (`boot`)

Controls lookup and execution of the app's boot script (default: `apps/{package}/boot.php`).

| Value | Default | What happens |
|-------|---------|--------------|
| `true` | yes | Run `boot.php` during the boot pipeline when this app boots |
| `false` | | Skip boot file entirely — route-only app |
| `'path/custom.php'` | | Run another file relative to the app root |

```php
'boot' => true,              // standard — apps/{package}/boot.php
'boot' => false,             // no programmatic registration
'boot' => 'setup/boot.php',  // custom boot script
```

The file must **return a callable** `fn (AppRegister $register) => …`. If `boot` is `true` but the file is missing, boot continues silently (no error).

### Global plugin (`boot-global`)

| Value | Default | What happens |
|-------|---------|--------------|
| `false` | yes | Boot only when this app is active (matched URL) or extends a host |
| `true` | | Boot on **every HTTP request**, before the active app |

Use for site-wide plugins (logging, feature flags). Every request runs this app's boot logic — keep it light.

### Host plugin (`extends`)

| Value | Default | What happens |
|-------|---------|--------------|
| `[]` | yes | Normal app — boots only for itself |
| `['com_host_app']` | | Boot **before** the listed host when that host becomes active |

Lighter than `boot-global`: your plugin runs only when the host app runs (e.g. add routes or watches to a panel).

```php
'extends' => ['com_pinoox_manager'],
```

### Extra registration (`startup`)

| Value | Default | What happens |
|-------|---------|--------------|
| `null` | yes | No second boot step |
| `fn (AppRegister $r) => …` | | Extra registration in `app.php`, executed **after** `boot.php` with the same API |

Prefer `boot.php` for most apps. Use `startup` for small inline registration generated in the manifest.

### Boot cache (`cache`)

Runtime cache is **opt-in** (`cache.enabled` must be `true`). Store flags define what is baked for production.

| Key | Default | What happens |
|-----|---------|--------------|
| `cache.enabled` | `false` | Master switch — no runtime hydration until `true` |
| `cache.stores.boot` | `true` | Cache boot registrations (API/GraphQL manifests, container bindings) |
| `cache.stores.routes` | `true` | Cache web route / action manifests |
| `cache.stores.api` | `true` | Cache API entry lists |

```php
'cache' => [
    'enabled' => true,
    'stores' => [
        'boot' => true,
        'routes' => true,
        'api' => true,
    ],
],
```

After deploy: `php pinoox cache:build {package}`. Details: [Boot cache](#boot-cache) and [Pinker](./pinker.md).

### Quick chooser

| You want… | Set in `app.php` |
|-----------|------------------|
| Normal app (routes + boot) | `'boot' => true` (default) |
| Routes only, no boot file | `'boot' => false` |
| Site-wide plugin | `'boot-global' => true` |
| Plugin on one host app | `'extends' => ['com_host_app']` |
| Faster production boot | `'cache.enabled' => true` + run `cache:build` |

---

## Basic boot.php

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => static fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => static fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## Combine with route files

`boot.php` and `routes/web.php` / `routes/api.php` work together. Use route files for stable CRUD; use `boot.php` for conditional registration, plugins, and cross-app hooks.

---

## AppRegister — common methods

| Method | Purpose |
|--------|---------|
| `web(callable)` | Register routes via builder |
| `route([...])` | Single web route (supports `flow`, `permission`) |
| `api([manifest])` | Full API manifest |
| `apiRoute([...])` | Single API endpoint |
| `graphql([manifest])` | GraphQL types / queries / mutations |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flat flow alias |
| `alias(['myapp' => ['auth' => AuthFlow::class]])` | Nested flow alias |
| `schedule(callable)` | Scheduled task |
| `listen('event', listener)` | Event listener |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | Hook when another app boots |
| `onRoute` / `onApi` / `onPath` | Watch matching requests (see below) |
| `onController` / `onAction` | Watch controller or named action |
| `onModel` | Watch Eloquent model events |
| `onTheme` | Watch when a theme context or folder becomes active |

---

## Watches — react to routes, API, controllers, models

Register **declarative watches** in `boot.php` instead of writing a Symfony subscriber by hand.

```php
use Pinoox\Component\AppEvent\AppWatchContext;

return function (AppRegister $register): void {
    $register->onRoute('app.run', function (AppWatchContext $ctx): void {
        // $ctx->request, $ctx->routeName(), $ctx->route, $ctx->package()
    });

    $register->onApi('auth.login', function (AppWatchContext $ctx): void {});

    $register->onPath('/manager/app/*', function (AppWatchContext $ctx): void {});

    $register->onController([AppViewController::class, 'run'], function (AppWatchContext $ctx): void {});

    $register->onModel(OrderModel::class, 'creating', function (AppWatchContext $ctx): void {
        // $ctx->model
    });

    // Plugin: only when host app is active
    $register->onRoute('app.run', $handler, 'com_host_app');
};
```

| Method | When it runs |
|--------|----------------|
| `onRoute` | Web route name matched (before controller) |
| `onApi` | API route name matched |
| `onPath` | Request path matches (`*` suffix = prefix) |
| `onController` | Controller about to run |
| `onAction` | Named action matched |
| `onModel` | Eloquent lifecycle event |
| `onTheme` | Theme context or folder name becomes active |

Use **Flow** when you need middleware (block/redirect). Use **watch** for side effects (log, sync, metrics).

```php
$register->onTheme('panel', function (AppWatchContext $ctx): void {
    // $ctx->themeContext(), $ctx->themeName(), $ctx->themeStack()
});
```

Web route with permission:

```php
$register->route([
    'path' => '/panel',
    'action' => [PanelController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'app.panel.view',
]);
```

---

## Theme — contexts, inheritance, and boot hooks

Theme folders live under `apps/{package}/theme/{name}/`. Configure the active theme in **`app.php`**; use **`boot.php`** for runtime hooks (global view data, switching context per route).

### Keys in `app.php`

| Key | Purpose |
|-----|---------|
| `theme` | Active theme folder (e.g. `'default'`) |
| `theme-context` / `theme-contexts` | Multiple themes per app (site / panel / …) |
| `theme-extends` | Inherit from another theme folder |
| `path-theme` | Custom folder instead of `theme/` |
| `frontend` | Vite profile, entry, manifest |

Multi-context example:

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Attach a context on routes via Flow:

```php
// routes/web.php
get('/panel', [PanelController::class, 'index'], flows: ['auth', 'theme.panel']);
```

Inside `theme/{name}/`: `theme.php` (manifest + `extends`), Twig templates, optional `functions.php` (Twig helpers), `frontend.config.php`, `src/` / `dist/` for Vite. Child themes override parent templates; cross-app parent: `@com_base/default`.

**Full guides:** [Theme contexts](../basic/theme-contexts.md) · [Theme manifest (`theme.php`)](../basic/theme-manifest.md) · [Views](../basic/views.md) · [Twig templates](../basic/templates.md) · [app.php manifest](../start/app-manifest.md).

### From `boot.php`

Use **`onTheme`** for declarative theme hooks, or **listen** / **watch** for finer control:

```php
use Pinoox\Component\AppEvent\AppControllerEvent;
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Component\Template\Theme\ThemeContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});

// Global view data after boot
$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    function (): void {
        View::set('brand', config('brand.name'));
    },
);

// Manual context switch by path (alternative to theme Flow)
$register->onPath('/panel/*', function (AppWatchContext $ctx): void {
    ThemeContext::activate('panel');
});
```

In controllers you can also call `View::changeTheme('panel')`, `ThemeContext::activate('panel')`, or `within_theme('panel', fn () => View::render('pages/dashboard'))`.

| Need | Approach |
|------|----------|
| One fixed theme | `'theme' => 'default'` in `app.php` |
| Site + admin UI | `theme-contexts` + `theme_flow_aliases` on routes |
| Extend a base theme | `theme.php` → `'extends' => ['parent']` |
| Global Twig variables | `View::set()` in boot listener or controller |
| Theme only on some routes | Flow `theme.panel` or watch/listen on path |

Build / cache:

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

---

## Service container (`bindings.php`)

When `container.enabled => true` in `app.php`, bindings merge from `container.bindings` and `apps/{package}/bindings.php`:

```php
// bindings.php
return [
    OrderRepositoryInterface::class => OrderRepository::class,
];
```

New apps from `php pinoox app:create` receive both `boot.php` and `bindings.php` stubs.

For the full boot pipeline stages, dual-container model, and controller constructor injection, see [Kernel and boot pipeline](./kernel.md).

---

## Event portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

See [Email](./mail.md) for decoupling mail from controllers.

**Flow** = before the controller (middleware). **Event** = after an action (side effects).

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot(?string $package = null): AppRegister
```

---

## Boot cache

Enable runtime boot cache in `app.php`:

```php
'cache' => [
    'enabled' => true,
    'stores' => ['boot' => true, 'api' => true],
],
```

Build: `php pinoox cache:build {package}` (or via `.pinx install`). See [Pinker](./pinker.md).

---

## Package lifecycle (`lifecycle.php`)

`boot.php` runs on **every request**. Install / update / uninstall / reset are **one-shot package operations** (often CLI). Put those side-effects in optional **`apps/{package}/lifecycle.php`** — same drop-in style as `boot.php`, not four separate files.

```bash
php pinoox lifecycle:create com_acme_shop
```

```php
<?php

use Pinoox\Component\Package\Lifecycle\AppLifecycle;
use Pinoox\Component\Package\Lifecycle\AppLifecycleContext;

return function (AppLifecycle $life): void {
    $life->onInstall(function (AppLifecycleContext $ctx): void {
        // First install: seed defaults, create folders. Keep idempotent.
    });

    $life->onUpdate(function (AppLifecycleContext $ctx): void {
        // Every update (non-DB). Schema/data version bumps → migration / patch.
        // $ctx->fromVersionCode → $ctx->toVersionCode
    });

    $life->onUninstall(function (AppLifecycleContext $ctx): void {
        // Before tables and folder are removed.
    });

    $life->onReset(function (AppLifecycleContext $ctx): void {
        // Optional non-DB cleanup; install runs again after reset.
    });
};
```

Array form also works: `return ['install' => function (AppLifecycleContext $ctx) {}, …];`. Split files yourself with `$life->onInstall(require __DIR__.'/setup/install.php');`.

`app.php`:

| Value | Meaning |
|-------|---------|
| `true` (default) | Run `lifecycle.php` if present |
| `false` | Skip |
| `'setup/lifecycle.php'` | Custom path relative to the app root |

Missing file → silent skip.

| Layer | Use for |
|-------|---------|
| **migration** | Schema |
| **patch** | One-time versioned data (`history`) |
| **lifecycle.php** | Seed, folders, config, file cleanup |

### When it runs

| Operation | Order |
|-----------|--------|
| **Install** | extract → migrate → patches → `app.installing` + `onInstall` → `app.installed` → cache |
| **Update** | same, with `updating` / `onUpdate` / `updated` |
| **Uninstall** | `uninstalling` + `onUninstall` → rollback patches + clear patch/lifecycle history → rollback migrate → delete folder → `uninstalled` |
| **Reset** (`app:reset`) | `resetting` + `onReset` → rollback patches/migrate → migrate + patch → `onInstall` → `app.reset` |
| **Provision** (installer / on-disk apps) | `onInstall` only if not already recorded in `history` (`type=lifecycle`) |

```bash
php pinoox app:reset com_acme_shop
php pinoox pinx:install shop.pinx --skip-lifecycle
Pinx::resetApp('com_acme_shop');
```

### Package events (observe from `boot.php`)

| Name | When |
|------|------|
| `app.installing` / `app.installed` | Before / after install hook |
| `app.updating` / `app.updated` | Before / after update hook |
| `app.uninstalling` / `app.uninstalled` | Before hook / after folder delete |
| `app.resetting` / `app.reset` | Before reset hook / after re-install |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\Package\Lifecycle\AppLifecycleEvent;

$register->listen(AppEventNames::INSTALLED, function (AppLifecycleEvent $event): void {
    // $event->package, $event->context
});
```

Other apps / plugins listen from their `boot.php` (especially `boot-global`). The **target app’s own** seed/cleanup stays in `lifecycle.php` so CLI install does not have to boot the full request pipeline.

---

## Related files

| File | Role |
|------|------|
| `boot.php` | Programmatic registration (every request) |
| `lifecycle.php` | Install / update / uninstall / reset hooks |
| `bindings.php` | DI bindings |
| `schedule.php` | Cron tasks (file-based) |
| `routes/web.php` | Web routes |
| `routes/api.php` | API manifest (file-based) |

---

## Related docs

- [Kernel and boot pipeline](./kernel.md)
- [Schedule](./schedule.md)
- [Patches](./patches.md)
- [Flows](../basic/flows.md)
- [Views](../basic/views.md)
- [Twig templates](../basic/templates.md)
- [Routers](../basic/routers.md)
- [Project structure](../start/structure.md)
- [app.php manifest](../start/app-manifest.md)

---

[← Back to index](../README.md)

# Kernel and boot pipeline

[← Back to index](../README.md)

How Pinoox boots an app, resolves controllers, and caches production metadata — while keeping **Portal / HMVC / Flow** as the core architecture.

For registering routes, listeners, and plugins in `boot.php`, see [boot.php and events](./boot-and-events.md).

---

## Request lifecycle

```text
index.php
  → bootstrap.php
  → AppProvider::boot()
      → prerequisite()  ← BootPipeline (once per package)
      → HttpKernel::handle()
          → Flow chain
          → Controller
          → View / JSON
```

The Symfony `HttpKernel` remains the HTTP engine. Pinoox adds HMVC apps, Portal DI, and Flow middleware on top.

---

## Boot pipeline

`AppProvider::prerequisite()` runs a single ordered pipeline:

| Stage | Purpose |
|-------|---------|
| `composer` | Load app `vendor/autoload.php` if present |
| `loader` | Run `app.php` → `loader` entries |
| `app.boot` | `AppBootstrap::ensure()` — `boot.php`, routes/API registry |
| `container` | App DI + controller registration (opt-in) |
| `events` | Legacy `app.php` → `event` listeners |
| `database` | Per-app DB connections |
| `api` | OpenAPI / GraphQL providers |
| `session` | Session driver + optional auto-start |

Inspect stages:

```php
use Pinoox\Portal\Kernel\Boot;

Boot::bootStages();
// or AppProvider::___()->bootStages();
```

Extend a stage from `boot.php`:

```php
use Pinoox\Portal\App\AppProvider;

AppProvider::___()->pipeline()->add('metrics', function () {
    // custom boot logic
}, after: 'app.boot');
```

Use sparingly — prefer `AppRegister` events for app extensions. See [boot.php and events](./boot-and-events.md).

---

## Dual container model

| Container | Role |
|-----------|------|
| **Symfony `ContainerBuilder`** (`container()` / `pincore()`) | Portal services, kernel aliases |
| **Illuminate Container** (`Container::Illuminate()`) | Constructor injection / bindings |

`ServiceContainerBootstrap` registers `service_container` and bridges bindings when enabled.

---

## App container & controller DI (opt-in)

Enable in `apps/{package}/app.php`:

```php
'container' => [
    'enabled' => true,
    'autowire_controllers' => true,
    'bindings' => [
        \App\com_my_shop\Component\CartContract::class => \App\com_my_shop\Component\CartService::class,
    ],
    'singletons' => [
        \App\com_my_shop\Component\CartService::class => true,
    ],
],
```

Optional file: `apps/{package}/bindings.php` (see `pincore/stubs/bindings.php.stub`).

### Controller constructor injection

```php
namespace App\com_my_shop\Controller;

use App\com_my_shop\Component\CartContract;
use Pinoox\Component\Kernel\Controller\Controller;

class CheckoutController extends Controller
{
    public function __construct(private CartContract $cart)
    {
    }

    public function index()
    {
        return $this->json($this->cart->summary());
    }
}
```

When `container.enabled` is `false` (default), controllers behave as before (`new Controller()` + `setContainer()`).

---

## Production cache

Boot cache (`php pinoox cache:build --only=boot`) also stores:

- API / GraphQL manifests
- **Container bindings + controller list** when `container.enabled` is true

At runtime, `BootCacheStore::tryHydrate()` restores bindings before the `container` pipeline stage — no filesystem scan in production.

Rebuild after changing:

- `boot.php`
- `bindings.php`
- `app.php` container section
- files under `Controller/`

```bash
php pinoox cache:build com_my_shop --only=boot
php pinoox cache:clear com_my_shop --only=boot
```

See [Pinker and cache](./pinker.md).

---

## What stays the same

- **Portal** — static facades to components (`View`, `Router`, `Date`, …)
- **HMVC** — `apps/{package}` modules, `App::meeting()`
- **Flow** — middleware aliases in `app.php`, route `flows: ['auth']`
- **Symfony HttpKernel** — request/events/exception pipeline

No breaking changes: container DI is **opt-in**.

---

## Related docs

- [boot.php and events](./boot-and-events.md)
- [Pinker and cache](./pinker.md)
- [Flows](../basic/flows.md)
- [app.php manifest](../start/app-manifest.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)

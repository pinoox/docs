# Sub-Apps and App Mounting

[← Back to Index](../README.md)

The **Sub-App** feature in Pinoox allows you to mount and execute any independent application under a sub-path of another host application without modifying the guest app's internal routes, controllers, or structure.

It also supports **Dynamic In-Memory Configuration Overlays**, contextual data passing with **Lazy Evaluation**, selective route mounting, granular access restrictions (such as `subapp_only` and `allowed_hosts`), and complete SPA base path resolution.

---

## Common Use Cases

- **Modular Composition:** Mount self-contained micro-applications (e.g., payment gateways, blogs, chat engines, or ticketing portals) inside a primary storefront or SaaS app.
- **Secure App Previews (Secret View):** Open and browse apps within an admin console without separate domain mapping (as used by Pinoox Manager).
- **Demo & A/B Testing:** Dynamically test applications with alternative themes or configurations on the fly without altering on-disk files.

---

## Router Registration

You can mount a sub-app directly in your route files using the `subApp` helper or the `Route` portal:

```php
// routes/web.php
use function Pinoox\Router\subApp;
use Pinoox\Portal\Route;

// Simple registration
subApp('/pay', 'com_pinoox_payment');

// Or with the fluent Route portal API
Route::subApp('/shop', 'com_pinoox_shop')
    ->config([
        'theme' => 'minimal',
        'debug' => true,
    ])
    ->context([
        'embed_mode' => true,
        'portal_user_id' => 120,
    ])
    ->name('shop.embedded');
```

> **Note:** Nested subpaths are routed automatically. In the example above, requests to `/shop/cart` or `/shop/products/42` are forwarded seamlessly to `com_pinoox_shop`'s internal routes.

### `SubAppRouteBuilder` Methods

| Method | Description |
|--------|-------------|
| `path(string $path)` | Specify a custom filesystem directory path for the sub-app (auto-registered with `AppEngine`). |
| `appPath(string $path)` | Alias of `path()` to set a custom app directory. |
| `routes(string\|array $routeFiles)` | Selectively mount specific route file(s) instead of loading all guest routes (e.g., separating web/site from panel routes). |
| `only(array\|string $tagsOrContexts)` | Filter sub-app routes to only include those matching the specified tag(s). |
| `config(array $overrides)` | Temporarily override `app.php` configuration keys for the guest app during this request. |
| `context(array $data)` | Pass arbitrary contextual data, metadata, or lazy closures from the host application to the guest app. |
| `name(string $name)` | Assign a named route prefix to the sub-app base path. |
| `flows(array $flows)` | Apply middleware/flows prior to entering the guest sub-app. |
| `methods(array\|string $methods)` | Restrict allowed HTTP verbs (defaults to all methods). |

---

## Selective Route Mounting and Tag Filtering

In Pinoox, rather than loading every route defined in the guest application's `app.php`, you can selectively mount specific route files or filter routes by tags:

### 1. Specific Route Files with `routes()`
```php
// Mount only frontend / public payment routes
Route::subApp('/pay', 'com_pinoox_pay')
    ->routes('routes/site/web.php');

// Mount payment administrative routes under admin panel
Route::subApp('/panel/payment', 'com_pinoox_pay')
    ->routes('routes/panel/web.php');
```

### 2. Tag Filtering with `only()`
```php
// Only routes tagged with 'site' are mounted
Route::subApp('/pay', 'com_pinoox_pay')
    ->only('site');

// Mount routes matching multiple tags
Route::subApp('/checkout', 'com_pinoox_pay')
    ->only(['checkout', 'public']);
```

---

## Lazy Context Evaluation (Closures & Callables)

When passing dynamic values (such as the authenticated user or roles) from host to guest, evaluating them eagerly during route boot time can be problematic because the user may not yet be authenticated. Pinoox allows passing lazy closures:

```php
Route::subApp('/pay', 'com_pinoox_pay')
    ->context([
        'is_manager' => fn () => Auth::user()?->is_admin ?? false,
        'portal_mode' => 'embedded',
    ]);
```

Inside the guest sub-app:
- `App::context('is_manager')` or helper `sub_app_context('is_manager')` automatically evaluates the closure and returns the resolved value (`true` or `false`).
- `App::rawContext('is_manager')` returns the raw stored closure/callable without invoking it.
- `App::resolveContext('is_manager')` explicitly resolves and returns the evaluated value.

---

## Base Path and SPA / Theme Integration

When an application is mounted as a sub-app at `/pay` or `/panel/payment`, its mount path and base URL must be accessible to front-end routers (like Vue Router with `createWebHistory`) and templates without hardcoded paths:

### PHP and Twig Base Path Access
- In PHP: `App::mountPath(): string` returns the relative mount path (e.g. `/pay`), and `App::subAppBaseUrl(): string` returns the complete URL.
- In Twig: `{{ mount_path() }}` and `{{ sub_app_base_url() }}` are available globally.

### Response Headers
Pinoox automatically attaches tracking headers to all sub-app responses:
- `X-SubApp-Mount-Path`: The relative mount path (e.g., `/pay`)
- `X-SubApp-Base-Url`: The full base URL of the sub-app
- `X-SubApp-Parent`: The package name of the host application

### JavaScript and SPA Initialization (`window.__PINOOX__`)
The global `window.__PINOOX__.url` object automatically provides mount path information:
```javascript
// In Vue Router or React Router configuration:
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(window.__PINOOX__?.url?.MOUNT_PATH || '/'),
    routes: [ ... ]
});
```

---

## Authentication & Access Control Flows (`AuthFlow` and `AccessFlow`)

Pinoox provides `AccessFlow` (and an upgraded `AuthFlow`) to cleanly distinguish unauthenticated requests (**401 Unauthorized**) from permission/authorization denials (**403 Forbidden**):

```php
namespace App\com_pinoox_pay\Flow;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Router\Route;
use Pinoox\Flow\AccessFlow;
use Pinoox\Portal\Auth;

class PaymentAccessFlow extends AccessFlow
{
    /**
     * Determine authorization for the given request and route.
     */
    protected function authorize(Request $request, ?Route $route): bool
    {
        // At this point, the user is authenticated (guests are already redirected or returned 401)
        return Auth::user()?->can_pay ?? false;
    }
}
```

- If `Auth::guest()` is true, `unauthenticated()` is called, issuing an HTTP `401 Unauthorized` response or redirecting to the login page.
- If the user is authenticated but `authorize()` returns `false`, `forbidden()` is called, issuing an HTTP `403 Access Denied` response.
- JSON / API requests automatically receive structured JSON error payloads.

---

## Automatic App Path Detection & Custom Locations

Pinoox uses **`AppEngine`** to resolve and locate all application resources (routes, controllers, configs, translations, themes). In Sub-Apps, path detection is completely automatic:

1. **Standard `apps/` Directory:** If the app resides in `apps/{package}`, `AppEngine` detects and mounts it automatically without any manual registration.
2. **Host App Sub-Folders (`sub_apps/`):** If a sub-app is located inside the host app (e.g., `apps/{host}/sub_apps/{guest}` or `apps/{host}/apps/{guest}`), `SubApp` automatically scans conventional locations and registers the guest package in `AppEngine`.
3. **Development Server Registry (`AppDevRegistry`):** If the guest app is running on a live dev server (`pinx dev`), `AppEngine` automatically discovers and mounts it in memory without needing an entry in `apps.config.php`.
4. **Custom / Arbitrary Paths:** You can mount an app located anywhere on the filesystem:

```php
// Option A: Fluent builder path()
Route::subApp('/payment', 'com_payment')
    ->path(path('sub_apps/payment'));

// Option B: Passing directory path directly as package
Route::subApp('/chat', path('modules/live_chat'));

// Option C: Options array
Route::subApp('/blog', 'com_blog', [
    'path' => '/var/custom_apps/blog',
]);
```

Inside views or controllers, you can retrieve the resolved sub-app filesystem path using:
- PHP: `sub_app_path()` or `SubApp::path('package_name', 'sub/path')`
- Twig: `{{ sub_app_path() }}` or `{{ sub_app_path('package_name') }}`

---

## Dynamic Configuration Overlays

When a sub-app is mounted, you can dynamically override its `app.php` settings without modifying files on disk.

These modifications are managed by `LayeredConfig`:
- `get()` and `all()` return values merged from the active overlay over the base configuration.
- Mutations (`set()`, `add()`, `remove()`) are stored in-memory only.
- `save()` is safely prevented, protecting persistent configuration files.
- The overlay is cleanly popped off the stack upon request completion.

```php
Route::subApp('/demo-blog', 'com_pinoox_blog')
    ->config([
        'theme' => 'dark-demo',
        'comments_enabled' => false,
    ]);
```

---

## Guest Restrictions in `app.php`

Guest applications can declare mounting and isolation constraints in their `app.php`:

```php
// apps/com_pinoox_payment/app.php
return [
    'package' => 'com_pinoox_payment',

    // 1. Prevent standalone routing:
    // This app cannot be routed directly at top-level domain routes.
    'subapp_only' => true,

    // 2. Host whitelist:
    // Only authorized host applications can mount this sub-app.
    'allowed_hosts' => [
        'com_pinoox_manager',
        'com_pinoox_portal',
    ],
];
```

> If an unauthorized application attempts to mount a guest restricted by `allowed_hosts`, an HTTP 403 `AccessDeniedHttpException` is thrown.

---

## Detection Helpers and View Integration

Applications can inspect their sub-app state and retrieve context:

### In PHP:

```php
use Pinoox\Portal\SubApp;
use Pinoox\Portal\App\App;

// Check if currently executing as a sub-app
if (is_sub_app()) {
    // Get host package name
    $host = sub_app_parent(); // or sub_app_host()

    // Access custom context
    $isEmbed = sub_app_context('embed_mode', false);
}

// Check if hosted by a specific package
if (is_sub_app_of('com_pinoox_manager')) {
    // Custom logic when viewed inside manager preview
}
```

Equivalent methods on the `SubApp` portal and `App` facade:
- `SubApp::isSubApp()` or `App::isSubApp()`
- `SubApp::parent()` or `App::parent()`
- `SubApp::isSubAppOf('package_name')` or `App::isSubAppOf(...)`
- `SubApp::context(?string $key = null, mixed $default = null)`

### In Twig Templates:

All helper functions are available globally in Twig templates:

```twig
{% if is_sub_app() %}
    <div class="subapp-banner">
        Hosted by: {{ sub_app_parent() }}
    </div>
{% endif %}

{% if is_sub_app_of('com_pinoox_manager') %}
    {# Hide public layout chrome when running inside manager preview #}
{% else %}
    {% include 'header.html' %}
{% endif %}

{% if sub_app_context('embed_mode') %}
    <style>body { background: transparent; }</style>
{% endif %}
```

---

## Manual Execution via Controller (`SubApp::run`)

To execute a sub-app outside of standard route registration (e.g., from an administrative controller action):

```php
namespace App\com_pinoox_manager\Controller;

use Pinoox\Component\Http\Request;
use Pinoox\Portal\SubApp;

class AppViewController
{
    public function open(string $packageName, Request $request)
    {
        $mountPath = 'app/' . $packageName;

        return SubApp::run(
            guestPackage: $packageName,
            mountPath: $mountPath,
            request: $request,
            config: ['debug' => true],
            context: ['admin_token' => 'xyz']
        );
    }
}
```

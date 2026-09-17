# Sub-Apps and App Mounting

[← Back to Index](../README.md)

The **Sub-App** feature in Pinoox allows you to mount and execute any independent application under a sub-path of another host application without modifying the guest app's internal routes, controllers, or structure.

It also supports **Dynamic In-Memory Configuration Overlays**, contextual data passing, and granular access restrictions (such as `subapp_only` and `allowed_hosts`).

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
| `config(array $overrides)` | Temporarily override `app.php` configuration keys for the guest app during this request. |
| `context(array $data)` | Pass arbitrary contextual data from the host application to the guest app. |
| `name(string $name)` | Assign a named route prefix to the sub-app base path. |
| `flows(array $flows)` | Apply middleware/flows prior to entering the guest sub-app. |
| `methods(array\|string $methods)` | Restrict allowed HTTP verbs (defaults to all methods). |

---

## Automatic App Path Detection & Custom Locations

Pinoox uses **`AppEngine`** to resolve and locate all application resources (routes, controllers, configs, translations, themes). In Sub-Apps, path detection is completely automatic:

1. **Standard `apps/` Directory:** If the app resides in `apps/{package}`, `AppEngine` detects and mounts it automatically without any manual registration.
2. **Host App Sub-Folders (`sub_apps/`):** If a sub-app is located inside the host app (e.g., `apps/{host}/sub_apps/{guest}` or `apps/{host}/apps/{guest}`), `SubApp` automatically scans conventional locations and registers the guest package in `AppEngine`.
3. **Custom / Arbitrary Paths:** You can mount an app located anywhere on the filesystem:

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

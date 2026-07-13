# App dependencies

[← Back to index](../README.md)

Apps can declare **dependencies** on other installed apps. Pinoox validates required dependencies during `.pinx` install and provides a standard runtime API to reuse another app's config, lang, paths, actions, and PHP classes.

This is different from:

| Mechanism | Purpose |
|-----------|---------|
| `extends` | Plugin boot order — extender hooks into a host app |
| `depends` | Hard/soft requirement that another app exists (and optional min version) |
| `use_app()` | Runtime access to another app's resources |

---

## Declaring dependencies

In `app.php`:

```php
return [
    'package' => 'com_my_addon',

    // Simple list — app must exist
    'depends' => [
        'com_pinoox_manager',
    ],

    // Map with version constraints (version-code)
    'depends' => [
        'com_base_shop' => '>=2',
        'com_pinoox_manager' => '*',
    ],

    // Optional dependency — install succeeds without it; use when() at runtime
    'depends' => [
        'com_base_shop' => '>=2',
        'com_reviews' => ['optional' => true, 'min_code' => 1],
    ],
];
```

### Constraint formats

| Form | Meaning |
|------|---------|
| `'com_app'` (list item) | App must be installed |
| `'com_app' => '*'` | Same as above |
| `'com_app' => '>=2'` | Installed app `version-code` must be ≥ 2 |
| `'com_app' => 2` | Integer shorthand for `>=2` |
| `'com_app' => ['optional' => true]` | Soft dependency |

---

## Pinx build & install

When you run `pinx:build`, dependencies are copied into `manifest.json`:

```json
{
  "package": "com_my_addon",
  "depends": {
    "com_base_shop": ">=2",
    "com_reviews": { "optional": true }
  }
}
```

During `pinx:install`, after `minpin` check:

1. **`depends` step** — each required app must exist with matching version
2. Install aborts with a clear error if a dependency is missing or too old

CLI output:

```bash
php pinoox pinx:info export/com_my_addon.pinx   # shows Depends rows
php pinoox pinx:build com_my_addon              # lists Depends in summary
php pinoox pinx:install com_my_addon.pinx       # validates before extract
```

Optional dependencies are listed in the manifest but **do not block** installation.

---

## Runtime API — `use_app()`

Access another app's resources when it is installed:

```php
use function Pinoox\use_app;

$shop = use_app('com_base_shop');

if ($shop->exists()) {
    $code = $shop->versionCode();
    $path = $shop->path('theme/default');
}
```

Portal equivalent: `UseApp::use('com_base_shop')`.

### Methods

| Method | Description |
|--------|-------------|
| `exists()` | Whether the app is installed |
| `stable()` | Installed and enabled |
| `versionCode()` / `versionName()` | From installed `app.php` |
| `config('database.host')` | Named config file key (`config/{name}.config.php`) |
| `lang('welcome.title')` | Translation from dependency app |
| `path('Controller/HomeController.php')` | Absolute filesystem path |
| `class('Model.OrderModel')` | FQCN: `App\com_base_shop\Model\OrderModel` |
| `hasAction('home')` | Whether named action is registered |
| `actionUrl('home')` | Public URL for dependency app's action |
| `when($callback, $default)` | Run callback only if app exists |
| `meeting($callback)` | Temporarily switch active app context |

### Examples

**Config from another app**

```php
$prefix = use_app('com_base_shop')->config('database.default', 'shop_');
```

**Lang from another app**

```php
$label = use_app('com_pinoox_manager')->lang('user.profile');
```

**Controller from another app in routes**

```php
use App\com_base_shop\Controller\CatalogController;

get(
    path: '/catalog',
    action: [CatalogController::class, 'index'],
);
```

Or inside a closure with meeting:

```php
get(path: '/catalog', action: function () {
    return use_app('com_base_shop')->meeting(
        fn () => (new CatalogController())->index()
    );
});
```

**Cross-app action URL in Twig / PHP**

```php
// Reference syntax
url()->action('@com_base_shop/home');

// Helper
use_app('com_base_shop')->actionUrl('home');
```

**Optional feature when dependency exists**

```php
use_app('com_reviews')->when(function ($reviews) {
    return $reviews->config('reviews.enabled', false);
}, default: false);
```

**Global helpers**

```php
app_resource('@com_base_shop:lang.menu.shop');
app_dep_satisfied(['com_base_shop' => '>=2']); // bool
```

---

## Reference syntax

Cross-app references follow the same `@package/...` style as themes:

| Type | Syntax | Example |
|------|--------|---------|
| Action (URL) | `@pkg/action` | `@com_shop/home` |
| Config | `@pkg:config.{file}.{key}` | `@com_shop:config.database.host` |
| Lang | `@pkg:lang.{key}` | `@com_shop:lang.welcome.title` |
| Path | `@pkg:path.{relative}` | `@com_shop:path.theme/default` |
| Class | `@pkg:class.{Short}` | `@com_shop:class.Model.Order` |
| Theme | `@pkg/theme` | `@com_shop/spark` (ThemeReference) |
| Filesystem | `pkg:relative/path` | `com_shop:theme/default` (Path::get) |

Parse explicitly:

```php
UseApp::parse('@com_shop:lang.welcome');
```

---

## Recommended patterns

### 1. Addon on top of a base app

```php
// com_my_reports/app.php
'depends' => ['com_base_shop' => '>=3'],
```

Install order: base shop first, then reports addon.

### 2. Optional integration

```php
'depends' => [
    'com_base_shop' => '>=1',
    'com_sms_gateway' => ['optional' => true],
],
```

```php
if (use_app('com_sms_gateway')->exists()) {
    // send SMS
}
```

### 3. Shared auth / manager

```php
'depends' => ['com_pinoox_manager'],
```

Use manager models via full namespace or `use_app()->class()`.

### 4. Do not duplicate host logic

Prefer `extends` + `boot.php` `when()` for registering routes on a host app.  
Use `depends` + `use_app()` for reading host resources from an independent app.

---

## Related docs

- [app.php manifest](./app-manifest.md)
- [Pinx CLI](./pinx-cli.md)
- [CLI reference](./cli-reference.md)
- [boot.php and events](../advanced/boot-and-events.md) — `extends` and `when()`
- [Dependencies CLI (`deps`)](./deps-cli.md)

---

[← Back to index](../README.md)

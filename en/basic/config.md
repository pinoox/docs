# Config

Pinoox 3.x settings are stored in PHP files under `config/` (core and app). The standard approach: **`config('key')`** to read and **`config('name')->set(...)->save()`** to write.

---

## Reading

```php
// Simple key
$siteName = config('app.name');

// Nested key (dot notation)
$merchant = config('payment.merchant_id');

// Default value
$timeout = config('api.timeout', 30);

// Config object for chaining
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## Writing and saving

**Always call `save()` after changes:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## Nested data — `setLinear` / `getLinear`

```php
// Read
$themeName = config('theme.panel.name');

// Write
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Config file locations

| Location | Contents |
|----------|----------|
| `pincore/config/*.config.php` | Core settings (DB, domain, …) |
| `apps/{package}/config/*.config.php` | App settings |
| `pinker/config/` | Baked version (production) |
| `pinker/state/config/` | Post-install overrides (e.g. DB) |

In development, sensitive values are read from `.env` via `env()` / `_env()`.

---

## Example: payment gateway settings

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'stripe',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller or Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## Example: dynamic menu

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'Reports', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Portal — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

In practice `config()` wraps the same Portal — one style is enough.

---

## Tips

- Do not commit secrets (API keys, DB passwords) to git; use `.env` or `pinker/state`.
- File name: `{name}.config.php` → `config('{name}.key')`.
- After production deploy, run `php pinoox pinker:rebuild` to bake config.

---

## Related docs

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [File Path](./path.md)
- [Core Config Reference](../../basic/config.md)

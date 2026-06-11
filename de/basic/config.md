# Config

[← Zurück zum Index](../README.md)

Einstellungen in Pinoox 3.x werden in PHP-Dateien unter `config/` (Core und App) gespeichert. Der Standardansatz: **`config('key')`** zum Lesen und **`config('name')->set(...)->save()`** zum Schreiben.

---

## Lesen

```php
// Einfacher Schlüssel
$siteName = config('app.name');

// Verschachtelter Schlüssel (Punktnotation)
$merchant = config('payment.merchant_id');

// Standardwert
$timeout = config('api.timeout', 30);

// Config-Objekt für Verkettung
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## Schreiben und Speichern

**Nach Änderungen immer `save()` aufrufen:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## Verschachtelte Daten — `setLinear` / `getLinear`

```php
// Lesen
$themeName = config('theme.panel.name');

// Schreiben
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Speicherorte der Config-Dateien

| Speicherort | Inhalt |
|----------|----------|
| `pincore/config/*.config.php` | Core-Einstellungen (DB, Domain, …) |
| `apps/{package}/config/*.config.php` | App-Einstellungen |
| `pinker/config/` | Gebackene Version (Produktion) |
| `pinker/state/config/` | Overrides nach der Installation (z. B. DB) |

In der Entwicklung werden sensible Werte über `.env` via `env()` / `_env()` gelesen.

---

## Beispiel: Zahlungsgateway-Einstellungen

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
// Controller oder Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## Beispiel: dynamisches Menü

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

In der Praxis kapselt `config()` dasselbe Portal — ein Stil reicht aus.

---

## Tipps

- Keine Geheimnisse (API-Schlüssel, DB-Passwörter) in Git committen; `.env` oder `pinker/state` verwenden.
- Dateiname: `{name}.config.php` → `config('{name}.key')`.
- Nach dem Produktions-Deploy `php pinoox pinker:rebuild` ausführen, um die Config zu backen.

---

## Verwandte Dokumentation

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [Dateipfade](./path.md)
- [app.php-Manifest](../start/app-manifest.md)

---

[← Zurück zum Index](../README.md)

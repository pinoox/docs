# Config

[← Dizine dön](../README.md)

Pinoox 3.x ayarları `config/` altındaki PHP dosyalarında saklanır (çekirdek ve uygulama). Standart yaklaşım: okumak için **`config('key')`**, yazmak için **`config('name')->set(...)->save()`**.

---

## Okuma

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

## Yazma ve kaydetme

**Değişikliklerden sonra her zaman `save()` çağırın:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## İç içe veri — `setLinear` / `getLinear`

```php
// Read
$themeName = config('theme.panel.name');

// Write
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Config dosya konumları

| Konum | İçerik |
|----------|----------|
| `pincore/config/*.config.php` | Çekirdek ayarlar (DB, domain, …) |
| `apps/{package}/config/*.config.php` | Uygulama ayarları |
| `pinker/config/` | Bake edilmiş sürüm (üretim) |
| `pinker/state/config/` | Kurulum sonrası geçersiz kılmalar (ör. DB) |

Geliştirmede hassas değerler `env()` / `_env()` üzerinden `.env`'den okunur.

---

## Örnek: ödeme ağ geçidi ayarları

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

## Örnek: dinamik menü

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

Pratikte `config()` aynı Portal'ı sarar — tek bir stil yeterlidir.

---

## İpuçları

- Gizli bilgileri (API anahtarları, DB şifreleri) git'e commit etmeyin; `.env` veya `pinker/state` kullanın.
- Dosya adı: `{name}.config.php` → `config('{name}.key')`.
- Üretim dağıtımından sonra config'i bake etmek için `php pinoox pinker:rebuild` çalıştırın.

---

## İlgili dokümantasyon

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [Dosya yolu](./path.md)
- [app.php manifest](../start/app-manifest.md)

---

[← Dizine dön](../README.md)

# Config

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x की सेटिंग्स `config/` के अंतर्गत PHP फ़ाइलों में संग्रहीत होती हैं (कोर और ऐप)। मानक तरीका: पढ़ने के लिए **`config('key')`** और लिखने के लिए **`config('name')->set(...)->save()`**।

---

## पढ़ना

```php
// सरल key
$siteName = config('app.name');

// नेस्टेड key (dot notation)
$merchant = config('payment.merchant_id');

// डिफ़ॉल्ट मान
$timeout = config('api.timeout', 30);

// चेनिंग के लिए Config ऑब्जेक्ट
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## लिखना और सहेजना

**बदलावों के बाद हमेशा `save()` कॉल करें:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## नेस्टेड डेटा — `setLinear` / `getLinear`

```php
// पढ़ना
$themeName = config('theme.panel.name');

// लिखना
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Config फ़ाइलों के स्थान

| स्थान | सामग्री |
|----------|----------|
| `pincore/config/*.config.php` | कोर सेटिंग्स (DB, डोमेन, …) |
| `apps/{package}/config/*.config.php` | ऐप सेटिंग्स |
| `pinker/config/` | Baked संस्करण (प्रोडक्शन) |
| `pinker/state/config/` | इंस्टॉल के बाद के ओवरराइड (जैसे DB) |

डेवलपमेंट में, संवेदनशील मान `.env` से `env()` / `_env()` के माध्यम से पढ़े जाते हैं।

---

## उदाहरण: पेमेंट गेटवे सेटिंग्स

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
// Controller या Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## उदाहरण: डायनेमिक मेनू

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

व्यवहार में `config()` उसी Portal को रैप करता है — एक ही शैली पर्याप्त है।

---

## सुझाव

- रहस्य (API keys, DB पासवर्ड) git में कमिट न करें; `.env` या `pinker/state` का उपयोग करें।
- फ़ाइल का नाम: `{name}.config.php` → `config('{name}.key')`।
- प्रोडक्शन डिप्लॉय के बाद, config को bake करने के लिए `php pinoox pinker:rebuild` चलाएँ।

---

## संबंधित दस्तावेज़

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [File Path](./path.md)
- [app.php मैनिफ़ेस्ट](../start/app-manifest.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

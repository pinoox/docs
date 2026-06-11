# Config

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x 설정은 `config/`(core 및 app) 아래 PHP file에 저장됩니다. 표준 방법: 읽기는 **`config('key')`**, 쓰기는 **`config('name')->set(...)->save()`**.

---

## 읽기

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

## 쓰기 및 저장

**변경 후 항상 `save()` 호출:**

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

## Config file 위치

| Location | Contents |
|----------|----------|
| `pincore/config/*.config.php` | Core settings (DB, domain, …) |
| `apps/{package}/config/*.config.php` | App settings |
| `pinker/config/` | Baked version (production) |
| `pinker/state/config/` | Post-install overrides (e.g. DB) |

development에서는 민감한 값을 `env()` / `_env()`를 통해 `.env`에서 읽습니다.

---

## 예제: payment gateway 설정

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

## 예제: dynamic menu

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

실무에서는 `config()`가 같은 Portal을 감싸므로 한 가지 스타일이면 충분합니다.

---

## Tips

- secret(API key, DB password)을 git에 commit하지 마세요; `.env` 또는 `pinker/state` 사용
- file 이름: `{name}.config.php` → `config('{name}.key')`
- production 배포 후 `php pinoox pinker:rebuild`로 config bake

---

## 관련 문서

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [File Path](./path.md)
- [app.php manifest](../start/app-manifest.md)

---

[← 색인으로 돌아가기](../README.md)

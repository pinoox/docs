# پیکربندی (Config)

تنظیمات پینوکس ۳.x در فایل‌های PHP داخل `config/` (هسته و اپ) ذخیره می‌شوند. روش استاندارد: helper **`config('key')`** برای خواندن و **`config('name')->set(...)->save()`** برای نوشتن.

---

## خواندن

```php
// کلید ساده
$siteName = config('app.name');

// کلید تو در تو (dot notation)
$merchant = config('payment.merchant_id');

// مقدار پیش‌فرض
$timeout = config('api.timeout', 30);

// شیء config برای زنجیره‌ای
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## نوشتن و ذخیره

**بعد از هر تغییر حتماً `save()` بزنید:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'IDPay',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## داده تو در تو — `setLinear` / `getLinear`

```php
// خواندن
$themeName = config('theme.panel.name');

// نوشتن
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## محل فایل‌های config

| محل | محتوا |
|-----|--------|
| `pincore/config/*.config.php` | تنظیمات هسته (DB، domain، …) |
| `apps/{package}/config/*.config.php` | تنظیمات اپ |
| `pinker/config/` | نسخه bake‌شده (production) |
| `pinker/state/config/` | override بعد از نصب (مثلاً DB) |

در development مقادیر حساس از `.env` با `env()` / `_env()` خوانده می‌شوند.

---

## مثال: تنظیمات درگاه پرداخت

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'idpay',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller یا Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'درگاه غیرفعال است'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## مثال: منوی داینامیک

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'گزارش', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Portal — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

در عمل `config()` همان Portal را wrap می‌کند — یک سبک کافی است.

---

## نکات

- secretها (API key، رمز DB) را در git commit نکنید؛ از `.env` یا `pinker/state` استفاده کنید.
- نام فایل: `{name}.config.php` → `config('{name}.key')`.
- پس از deploy production، `php pinoox pinker:rebuild` برای bake config.

---

## مستندات مرتبط

- [Portal](portal.md)
- [Pinker](../advanced/pinker.md)
- [مسیر فایل](path.md)
- [پیکربندی پایه](../../basic/config.md)

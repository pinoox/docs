# الإعدادات (Config)

[← العودة إلى الفهرس](../README.md)

تُخزَّن إعدادات Pinoox 3.x في ملفات PHP ضمن `config/` (النواة والتطبيق). الأسلوب المعياري: **`config('key')`** للقراءة و**`config('name')->set(...)->save()`** للكتابة.

---

## القراءة

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

## الكتابة والحفظ

**استدعِ `save()` دائمًا بعد التغييرات:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## البيانات المتداخلة — `setLinear` / `getLinear`

```php
// Read
$themeName = config('theme.panel.name');

// Write
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## مواقع ملفات الإعداد

| الموقع | المحتوى |
|----------|----------|
| `pincore/config/*.config.php` | إعدادات النواة (قاعدة البيانات، النطاق، …) |
| `apps/{package}/config/*.config.php` | إعدادات التطبيق |
| `pinker/config/` | النسخة المُجمّعة (الإنتاج) |
| `pinker/state/config/` | تجاوزات ما بعد التثبيت (مثل قاعدة البيانات) |

في بيئة التطوير، تُقرأ القيم الحساسة من `.env` عبر `env()` / `_env()`.

---

## مثال: إعدادات بوابة الدفع

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

## مثال: قائمة ديناميكية

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

عمليًا، `config()` يغلّف نفس Portal — أسلوب واحد يكفي.

---

## نصائح

- لا تُلحق الأسرار (مفاتيح API، كلمات مرور قاعدة البيانات) في git؛ استخدم `.env` أو `pinker/state`.
- اسم الملف: `{name}.config.php` → `config('{name}.key')`.
- بعد النشر في الإنتاج، شغّل `php pinoox pinker:rebuild` لـ bake الإعدادات.

---

## وثائق ذات صلة

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [مسار الملفات (File Path)](./path.md)
- [ملف app.php](../start/app-manifest.md)

---

[← العودة إلى الفهرس](../README.md)

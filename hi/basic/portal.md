# Portal (Facade)

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में Portal core services का static gateway है — View, DB, Lang आदि तक simple access के लिए **Facade** pattern। रोज़मर्रा के काम के लिए **`Pinoox\Portal\*`** उपयोग करें; अपनी app services के लिए app के अंतर्गत Portals बनाएँ।

---

## Core Portals (common)

```php
use Pinoox\Portal\View;
use Pinoox\Portal\Url;
use Pinoox\Portal\Path;
use Pinoox\Portal\Lang;
use Pinoox\Portal\Validation;
use Pinoox\Portal\Database\DB;

View::render('home', $data);
Url::link('products');
Url::forApp('com_acme_shop');
Path::get('storage/logs');
Lang::get('welcome.title');
config('payment.merchant_id');   // helper → Config Portal
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## Portals क्यों?

- Container manually resolve किए बिना short, readable code
- Controller, Flow, और Component में एक stable entry point
- Portal classes पर `@method` के ज़रिए IDE auto-complete

---

## App service के लिए Portal

### 1. Component बनाएँ

```php
// apps/com_acme_shop/Component/PriceCalculator.php
namespace App\com_acme_shop\Component;

class PriceCalculator
{
    public function withTax(float $price, float $rate = 0.09): float
    {
        return round($price * (1 + $rate), 2);
    }
}
```

### 2. CLI से Portal generate करें

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Portal class में bind — `__register()` और `__bind()`

```php
// apps/com_acme_shop/Portal/PriceCalculator.php
namespace App\com_acme_shop\Portal;

use Pinoox\Component\Source\Portal;

class PriceCalculator extends Portal
{
    public static function __register(): void
    {
        self::__bind(\App\com_acme_shop\Component\PriceCalculator::class);
    }
}
```

### 4. Usage

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. IDE metadata refresh करें

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs helper

| Task | Recommended |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` or `Lang::get()` |
| URL | `url('path')` or `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` or `$request->validate()` |
| App service | custom Portal in `Portal/` |

---

## Tips

- Business logic `Component/` में रखें; Portal केवल facade है
- Component में methods add करने के बाद `portal:update` चलाएँ
- Core Portals directly edit न करें; `apps/{package}/Portal/` के अंतर्गत extend करें

---

## संबंधित docs

- [App Services](../advanced/services.md)
- [Config](./config.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

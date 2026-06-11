# Portal (Facade)

[← Back to index](../README.md)

In Pinoox 3.x a Portal is a static gateway to core services — the **Facade** pattern for simple access to View, DB, Lang, and more. For day-to-day work use **`Pinoox\Portal\*`**; for your own app services, create Portals under your app.

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

## Why Portals?

- Short, readable code without manually resolving the container
- One stable entry point in Controller, Flow, and Component
- IDE auto-complete via `@method` on Portal classes

---

## Portal for an app service

### 1. Create a Component

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

### 2. Generate a Portal with CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Bind in the Portal class — `__register()` and `__bind()`

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

### 5. Refresh IDE metadata

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

- Keep business logic in `Component/`; the Portal is only a facade
- After adding methods to a Component, run `portal:update`
- Do not edit core Portals directly; extend under `apps/{package}/Portal/`

---

## Related docs

- [App Services](../advanced/services.md)
- [Config](./config.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)

# Portal (فاساد)

[← بازگشت به فهرست](../README.md)

Portal در پینوکس ۳.x دروازه استاتیک به سرویس‌های هسته است — الگوی **Facade** برای دسترسی ساده به View، DB، Lang و … . برای کار روزمره از **`Pinoox\Portal\*`** استفاده کنید؛ برای سرویس‌های اپ خود Portal بسازید.

---

## Portalهای هسته (پرکاربرد)

```php
use Pinoox\Portal\View;
use Pinoox\Portal\Url;
use Pinoox\Portal\Path;
use Pinoox\Portal\Lang;
use Pinoox\Portal\Route;
use Pinoox\Portal\Validation;
use Pinoox\Portal\Database\DB;
use Pinoox\Portal\Date;

View::render('home', $data);
Route::get('/', '@welcome')->name('home');
Route::any('/webhook', 'handle')->name('webhook');
Date::display($item->created_at, 'datetime');
Url::link('products');
Url::forApp('com_acme_shop');
Path::get('storage/logs');
Lang::get('welcome.title');
config('payment.merchant_id');   // helper → Config Portal
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## چرا Portal؟

- کد کوتاه و خوانا بدون ساخت دستی Container
- یک نقطه دسترسی ثابت در Controller، Flow و Component
- IDE auto-complete با `@method` روی کلاس Portal

---

## Portal برای سرویس اپ

### ۱. ساخت Component

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

### ۲. ساخت Portal با CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### ۳. اتصال در Portal — __register() و __bind()

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

### ۴. استفاده

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### ۵. بروزرسانی متادیتای IDE

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal در مقابل helper

| کار | روش توصیه‌شده |
|-----|----------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` یا `Lang::get()` |
| URL | `url('path')` یا `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` یا `$request->validate()` |
| سرویس اپ | Portal اختصاصی در `Portal/` |

---

## نکات

- منطق business در `Component/` بماند؛ Portal فقط واسط است
- بعد از افزودن متد جدید به Component، `portal:update` بزنید
- Portal هسته را مستقیم edit نکنید؛ در `apps/{package}/Portal/` توسعه دهید

---

## مستندات مرتبط

- [سرویس‌های اپ](../advanced/services.md)
- [پیکربندی](config.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

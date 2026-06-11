# Portal (الواجهة)

[← العودة إلى الفهرس](../README.md)

في Pinoox 3.x، Portal هو بوابة ثابتة لخدمات النواة — نمط **Facade** للوصول البسيط إلى View وDB وLang وغيرها. للعمل اليومي استخدم **`Pinoox\Portal\*`**؛ لخدمات تطبيقك، أنشئ Portals ضمن تطبيقك.

---

## Portals النواة (شائعة)

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

## لماذا Portals؟

- كود قصير وقابل للقراءة دون حل الحاوية يدويًا
- نقطة دخول واحدة مستقرة في Controller وFlow وComponent
- إكمال تلقائي في IDE عبر `@method` على فئات Portal

---

## Portal لخدمة تطبيق

### 1. أنشئ Component

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

### 2. أنشئ Portal عبر CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. اربط في فئة Portal — `__register()` و `__bind()`

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

### 4. الاستخدام

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. حدّث بيانات IDE

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal مقابل المساعد

| المهمة | الموصى به |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` أو `Lang::get()` |
| URL | `url('path')` أو `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` أو `$request->validate()` |
| خدمة التطبيق | Portal مخصص في `Portal/` |

---

## نصائح

- احتفظ بمنطق الأعمال في `Component/`؛ Portal مجرد واجهة فقط
- بعد إضافة دوال إلى Component، شغّل `portal:update`
- لا تعدّل Portals النواة مباشرة؛ وسّع تحت `apps/{package}/Portal/`

---

## وثائق ذات صلة

- [خدمات التطبيق](../advanced/services.md)
- [الإعدادات (Config)](./config.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)

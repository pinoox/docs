# Portal (Facade)

[← Dizine dön](../README.md)

Pinoox 3.x'te Portal, çekirdek servislere statik bir geçittir — View, DB, Lang ve daha fazlasına basit erişim için **Facade** deseni. Günlük iş için **`Pinoox\Portal\*`** kullanın; kendi uygulama servisleriniz için uygulamanız altında Portal oluşturun.

---

## Çekirdek Portal'lar (yaygın)

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

## Neden Portal?

- Container'ı manuel çözümlemeden kısa, okunabilir kod
- Controller, Flow ve Component'te tek kararlı giriş noktası
- Portal sınıflarındaki `@method` ile IDE otomatik tamamlama

---

## Uygulama servisi için Portal

### 1. Component oluşturun

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

### 2. CLI ile Portal oluşturun

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Portal sınıfında bağlayın — `__register()` ve `__bind()`

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

### 4. Kullanım

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. IDE meta verisini yenileyin

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal ve helper

| Görev | Önerilen |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` veya `Lang::get()` |
| URL | `url('path')` veya `Url::link()` |
| Path | `path('reference')` |
| Validasyon | `Validation::validate()` veya `$request->validate()` |
| Uygulama servisi | `Portal/` altında özel Portal |

---

## İpuçları

- İş mantığını `Component/` içinde tutun; Portal yalnızca bir facade'dir
- Component'e metot ekledikten sonra `portal:update` çalıştırın
- Çekirdek Portal'ları doğrudan düzenlemeyin; `apps/{package}/Portal/` altında genişletin

---

## İlgili dokümantasyon

- [Uygulama servisleri](../advanced/services.md)
- [Config](./config.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

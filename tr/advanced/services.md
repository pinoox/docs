# Uygulama servisleri (Component + Portal)

[← Dizine dön](../README.md)

Pinoox 3.x'te iş mantığı **`apps/{package}/Component/`** içinde yer alır ve **`Portal/`** üzerinden açığa çıkarılır. Bu standart HMVC desenidir — şişkin controller'lar değil, `pincore/` içinde mantık değil.

---

## Önerilen akış

```
HTTP → Controller (ince)
         ↓
      Component (iş mantığı)
         ↓
      Model / DB / Cache
         ↓
      Portal (statik erişim)
```

---

## Örnek: sipariş servisi

```php
// apps/com_acme_shop/Component/OrderService.php
namespace App\com_acme_shop\Component;

use App\com_acme_shop\Model\OrderModel;
use App\com_acme_shop\Model\ProductModel;
use Pinoox\Portal\Validation;

class OrderService
{
    public function create(array $input): OrderModel
    {
        $data = Validation::validate($input, [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = ProductModel::findOrFail($data['product_id']);

        return OrderModel::create([
            'product_id' => $product->id,
            'qty' => $data['qty'],
            'total' => $product->price * $data['qty'],
        ]);
    }
}
```

---

## Portal üzerinden açma

```bash
php pinoox portal:create OrderService -p com_acme_shop
```

```php
// Portal/OrderService.php
public static function __register(): void
{
    self::__bind(\App\com_acme_shop\Component\OrderService::class);
}
```

---

## İnce controller

```php
namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Portal\OrderService;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class OrderController extends ApiController
{
    public function store(Request $request)
    {
        $order = OrderService::create($request->all());

        return $this->ok($order, 'order.created', status: 201);
    }
}
```

---

## Bağımlılık enjeksiyonu (isteğe bağlı)

Bir Component constructor'ında diğer Component'leri alabilir:

```php
class CheckoutService
{
    public function __construct(
        private OrderService $orders,
        private PriceCalculator $pricing,
    ) {}

    public function checkout(array $cart): array
    {
        // ...
    }
}
```

Portal bağlandığında Pinoox container bunları çözümler.

---

## Neyi nereye koymalı

| Katman | Sorumluluk |
|-------|----------------|
| `Controller/` | HTTP, durum kodları, view/json |
| `Component/` | İş kuralları, orkestrasyon |
| `Model/` | Eloquent, sorgular, ilişkiler |
| `Flow/` | Auth, middleware |
| `Portal/` | Component'e facade |

---

## Servis testi

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## İpuçları

- Servisleri durumsuz tutun; session/request'i parametre olarak geçirin
- Portal'ları yalnızca genel uygulama API'leri için kullanın — her şeyi Portal yapmayın
- Uygulamalar arası doğrudan import yapmayın; API/event/çekirdek portal'larını kullanın

---

## İlgili dokümantasyon

- [Portal](../basic/portal.md)
- [Validasyon](../basic/validation.md)
- [Veritabanına başlarken](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Dizine dön](../README.md)

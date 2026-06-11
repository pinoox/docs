# ऐप Services (Component + Portal)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x में business logic **`apps/{package}/Component/`** में रहता है और **`Portal/`** के माध्यम से उपलब्ध कराया जाता है। यह मानक HMVC पैटर्न है — न fat controllers, न ही `pincore/` में logic।

---

## अनुशंसित प्रवाह

```
HTTP → Controller (thin)
         ↓
      Component (business)
         ↓
      Model / DB / Cache
         ↓
      Portal (static access)
```

---

## उदाहरण: order service

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

## Portal के माध्यम से expose करना

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

## Thin controller

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

## Dependency injection (वैकल्पिक)

एक Component अपने constructor में अन्य Components प्राप्त कर सकता है:

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

जब Portal bind होता है, तो Pinoox container इन्हें resolve कर देता है।

---

## क्या कहाँ रखें

| परत | जिम्मेदारी |
|-------|----------------|
| `Controller/` | HTTP, status codes, view/json |
| `Component/` | Business नियम, orchestration |
| `Model/` | Eloquent, queries, relations |
| `Flow/` | Auth, middleware |
| `Portal/` | Component के लिए Facade |

---

## किसी service का परीक्षण

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## सुझाव

- Services को stateless रखें; session/request को parameters के रूप में पास करें
- Portals का उपयोग केवल सार्वजनिक ऐप APIs के लिए करें — हर चीज़ को Portal न बनाएँ
- ऐप्स के बीच सीधे import न करें; इसके बजाय API/events/core portals का उपयोग करें

---

## संबंधित दस्तावेज़

- [Portal](../basic/portal.md)
- [Validation](../basic/validation.md)
- [Database getting started](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

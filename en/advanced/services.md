# App Services (Component + Portal)

[← Back to index](../README.md)

In Pinoox 3.x business logic lives in **`apps/{package}/Component/`** and is exposed through **`Portal/`**. This is the standard HMVC pattern — not fat controllers, not logic in `pincore/`.

---

## Recommended flow

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

## Example: order service

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

## Expose via Portal

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

## Dependency injection (optional)

A Component can receive other Components in its constructor:

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

The Pinoox container resolves them when the Portal is bound.

---

## Where to put what

| Layer | Responsibility |
|-------|----------------|
| `Controller/` | HTTP, status codes, view/json |
| `Component/` | Business rules, orchestration |
| `Model/` | Eloquent, queries, relations |
| `Flow/` | Auth, middleware |
| `Portal/` | Facade to Component |

---

## Testing a service

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## Tips

- Keep services stateless; pass session/request as parameters
- Use Portals only for public app APIs — do not Portal everything
- Do not import directly across apps; use API/events/core portals instead

---

## Related docs

- [Portal](../basic/portal.md)
- [Validation](../basic/validation.md)
- [Database getting started](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Back to index](../README.md)

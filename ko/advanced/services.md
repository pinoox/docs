# App Services (Component + Portal)

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 business logic은 **`apps/{package}/Component/`**에 있고 **`Portal/`**로 노출됩니다. 표준 HMVC 패턴 — fat Controller 아님, `pincore/` logic 아님.

---

## 권장 flow

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

## 예제: order service

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

## Portal로 노출

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

## Thin Controller

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

## Dependency injection (선택)

Component constructor에서 다른 Component 받기:

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

Portal이 bind되면 Pinoox container가 resolve합니다.

---

## 무엇을 어디에

| Layer | Responsibility |
|-------|----------------|
| `Controller/` | HTTP, status code, view/json |
| `Component/` | Business rule, orchestration |
| `Model/` | Eloquent, query, relation |
| `Flow/` | Auth, middleware |
| `Portal/` | Component facade |

---

## Service 테스트

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

- service는 stateless 유지; session/request는 parameter로 전달
- public 앱 API에만 Portal — 모든 것을 Portal하지 마세요
- 앱 간 direct import 금지; API/event/core portal 사용

---

## 관련 문서

- [Portal](../basic/portal.md)
- [Validation](../basic/validation.md)
- [Database 시작하기](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← 색인으로 돌아가기](../README.md)

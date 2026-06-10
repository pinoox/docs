# سرویس‌های اپ (Component + Portal)

در پینوکس ۳.x منطق business داخل **`apps/{package}/Component/`** می‌ماند و از طریق **`Portal/`** در کل اپ در دسترس است. این الگوی استاندارد HMVC است — نه Controller ضخیم، نه logic در `pincore/`.

---

## جریان پیشنهادی

```
HTTP → Controller (نازک)
         ↓
      Component (business)
         ↓
      Model / DB / Cache
         ↓
      Portal (دسترسی استاتیک)
```

---

## مثال: سرویس سفارش

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

## در معرض Portal

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

## کنترلر نازک

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

## تزریق وابستگی (اختیاری)

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

Container پینوکس هنگام bind شدن Portal آن‌ها را resolve می‌کند.

---

## هر لایه چه کاری انجام می‌دهد

| لایه | مسئولیت |
|------|---------|
| `Controller/` | HTTP، status code، view/json |
| `Component/` | قوانین business، orchestration |
| `Model/` | Eloquent، query، relation |
| `Flow/` | Auth، middleware |
| `Portal/` | Facade به Component |

---

## تست سرویس

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## نکات

- سرویس‌ها stateless باشند؛ session/request را به‌صورت پارامتر بدهید
- Portal فقط برای API عمومی اپ — همه چیز را Portal نکنید
- بین اپ‌ها import مستقیم نزنید؛ از API/event/portal هسته استفاده کنید

---

## مستندات مرتبط

- [Portal](../basic/portal.md)
- [Validation](../basic/validation.md)
- [شروع دیتابیس](../database/getting-started.md)
- [Flow](../basic/flows.md)

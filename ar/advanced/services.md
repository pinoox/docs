# خدمات التطبيق (Component + Portal)

[← العودة إلى الفهرس](../README.md)

في Pinoox 3.x يوضع منطق الأعمال (Business Logic) في **`apps/{package}/Component/`** ويُعرَض من خلال **`Portal/`**. هذا هو نمط HMVC القياسي — لا متحكمات (Controllers) ضخمة، ولا منطق داخل `pincore/`.

---

## التدفق الموصى به

```
HTTP → Controller (رفيع)
         ↓
      Component (منطق الأعمال)
         ↓
      Model / DB / Cache
         ↓
      Portal (وصول ساكن Static)
```

---

## مثال: خدمة الطلبات

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

## العرض عبر البوابة (Portal)

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

## متحكم رفيع (Thin controller)

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

## حقن التبعيات (Dependency Injection) — اختياري

يمكن لأي مكوّن (Component) أن يستقبل مكوّنات أخرى في الباني (Constructor):

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

تتولى حاوية Pinoox حلّها عند ربط البوابة (Portal).

---

## أين تضع ماذا

| الطبقة | المسؤولية |
|-------|----------------|
| `Controller/` | HTTP، رموز الحالة، view/json |
| `Component/` | قواعد الأعمال والتنسيق بينها |
| `Model/` | Eloquent والاستعلامات والعلاقات |
| `Flow/` | المصادقة (Auth) والوسطاء (Middleware) |
| `Portal/` | واجهة (Facade) للمكوّن |

---

## اختبار خدمة

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## نصائح

- اجعل الخدمات عديمة الحالة (Stateless)؛ ومرّر الجلسة/الطلب كمعاملات
- استخدم البوابات (Portals) فقط لواجهات API العامة للتطبيق — لا تنشئ بوابة لكل شيء
- لا تستورد مباشرةً بين التطبيقات؛ استخدم API/الأحداث/بوابات النواة بدلاً من ذلك

---

## وثائق ذات صلة

- [البوابة (Portal)](../basic/portal.md)
- [التحقق من الصحة (Validation)](../basic/validation.md)
- [البدء مع قاعدة البيانات](../database/getting-started.md)
- [التدفق / الوسيط (Flow / Middleware)](../basic/flows.md)

---

[← العودة إلى الفهرس](../README.md)

# 应用服务（Component + Portal）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，业务逻辑位于 **`apps/{package}/Component/`**，并通过 **`Portal/`** 对外暴露。这是标准的 HMVC 模式 —— 既不是臃肿的控制器，也不是把逻辑写进 `pincore/`。

---

## 推荐流程

```
HTTP → Controller（薄层）
         ↓
      Component（业务）
         ↓
      Model / DB / Cache
         ↓
      Portal（静态访问）
```

---

## 示例：订单服务

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

## 通过 Portal 暴露

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

## 薄控制器

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

## 依赖注入（可选）

一个 Component 可以在构造函数中接收其他 Component：

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

绑定 Portal 时，Pinoox 容器会自动解析它们。

---

## 各层职责

| 层 | 职责 |
|-------|----------------|
| `Controller/` | HTTP、状态码、view/json |
| `Component/` | 业务规则、编排 |
| `Model/` | Eloquent、查询、关联 |
| `Flow/` | 认证、中间件 |
| `Portal/` | Component 的门面（Facade） |

---

## 测试服务

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## 提示

- 保持服务无状态；将 session/request 作为参数传入
- 仅对应用的公共 API 使用 Portal —— 不要给所有东西都建 Portal
- 不要在应用之间直接 import；改用 API、事件或核心 Portal

---

## 相关文档

- [Portal](../basic/portal.md)
- [验证（Validation）](../basic/validation.md)
- [数据库入门](../database/getting-started.md)
- [Flow / 中间件](../basic/flows.md)

---

[← 返回索引](../README.md)

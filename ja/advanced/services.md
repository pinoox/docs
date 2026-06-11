# App Services（Component + Portal）

[← 索引に戻る](../README.md)

Pinoox 3.x ではビジネスロジックは **`apps/{package}/Component/`** に置き、**`Portal/`** 経由で公開します。これが標準的な HMVC パターンです — Fat Controller ではなく、`pincore/` にロジックを置きません。

---

## 推奨フロー

```
HTTP → Controller（薄い）
         ↓
      Component（ビジネス）
         ↓
      Model / DB / Cache
         ↓
      Portal（静的アクセス）
```

---

## 例: 注文サービス

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

## Portal 経由で公開

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

## 薄い Controller

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

## 依存性注入（任意）

Component はコンストラクタで他の Component を受け取れます。

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

Portal がバインドされると Pinoox コンテナが解決します。

---

## 何をどこに置くか

| レイヤー | 責務 |
|-------|----------------|
| `Controller/` | HTTP、ステータスコード、view/json |
| `Component/` | ビジネスルール、オーケストレーション |
| `Model/` | Eloquent、クエリ、リレーション |
| `Flow/` | 認証、ミドルウェア |
| `Portal/` | Component への Facade |

---

## サービスのテスト

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## ヒント

- サービスはステートレスに。セッション/リクエストはパラメータで渡す
- Portal は公開アプリ API 用のみ — すべてを Portal 化しない
- アプリ間で直接 import しない。API/イベント/コア Portal を使用

---

## 関連ドキュメント

- [Portal](../basic/portal.md)
- [Validation](../basic/validation.md)
- [Database はじめに](../database/getting-started.md)
- [Flow / ミドルウェア](../basic/flows.md)

---

[← 索引に戻る](../README.md)

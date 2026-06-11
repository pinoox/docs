# App-Services (Component + Portal)

[← Zurück zur Übersicht](../README.md)

In Pinoox 3.x lebt die Geschäftslogik in **`apps/{package}/Component/`** und wird über **`Portal/`** verfügbar gemacht. Das ist das Standard-HMVC-Muster — keine fetten Controller, keine Logik in `pincore/`.

---

## Empfohlener Ablauf

```
HTTP → Controller (schlank)
         ↓
      Component (Geschäftslogik)
         ↓
      Model / DB / Cache
         ↓
      Portal (statischer Zugriff)
```

---

## Beispiel: Order-Service

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

## Über ein Portal verfügbar machen

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

## Schlanker Controller

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

## Dependency Injection (optional)

Ein Component kann andere Components im Konstruktor erhalten:

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

Der Pinoox-Container löst sie auf, wenn das Portal gebunden wird.

---

## Was gehört wohin?

| Schicht | Verantwortung |
|-------|----------------|
| `Controller/` | HTTP, Statuscodes, View/JSON |
| `Component/` | Geschäftsregeln, Orchestrierung |
| `Model/` | Eloquent, Queries, Relationen |
| `Flow/` | Auth, Middleware |
| `Portal/` | Fassade zum Component |

---

## Einen Service testen

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## Tipps

- Halten Sie Services zustandslos; übergeben Sie Session/Request als Parameter
- Verwenden Sie Portals nur für öffentliche App-APIs — machen Sie nicht alles zum Portal
- Importieren Sie nicht direkt zwischen Apps; verwenden Sie stattdessen API/Events/Core-Portals

---

## Verwandte Dokumente

- [Portal](../basic/portal.md)
- [Validierung (Validation)](../basic/validation.md)
- [Erste Schritte mit der Datenbank](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Zurück zur Übersicht](../README.md)

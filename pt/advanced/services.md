# Serviços do App (Component + Portal)

[← Voltar ao índice](../README.md)

No Pinoox 3.x a lógica de negócio vive em **`apps/{package}/Component/`** e é exposta através de **`Portal/`**. Esse é o padrão HMVC — nada de controllers inchados, nem lógica no `pincore/`.

---

## Fluxo recomendado

```
HTTP → Controller (enxuto)
         ↓
      Component (negócio)
         ↓
      Model / DB / Cache
         ↓
      Portal (acesso estático)
```

---

## Exemplo: serviço de pedidos

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

## Expor via Portal

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

## Controller enxuto

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

## Injeção de dependência (opcional)

Um Component pode receber outros Components no construtor:

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

O container do Pinoox os resolve quando o Portal é vinculado (bound).

---

## O que vai onde

| Camada | Responsabilidade |
|-------|----------------|
| `Controller/` | HTTP, códigos de status, view/json |
| `Component/` | Regras de negócio, orquestração |
| `Model/` | Eloquent, queries, relações |
| `Flow/` | Auth, middleware |
| `Portal/` | Facade para o Component |

---

## Testando um serviço

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## Dicas

- Mantenha os serviços sem estado (stateless); passe sessão/request como parâmetros
- Use Portals apenas para APIs públicas do app — não transforme tudo em Portal
- Não importe diretamente entre apps; use API/events/portals do core em vez disso

---

## Documentação relacionada

- [Portal](../basic/portal.md)
- [Validação](../basic/validation.md)
- [Primeiros passos com banco de dados](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Voltar ao índice](../README.md)

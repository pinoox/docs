# Servicios de app (Component + Portal)

[← Volver al índice](../README.md)

En Pinoox 3.x la lógica de negocio vive en **`apps/{package}/Component/`** y se expone mediante **`Portal/`**. Es el patrón HMVC estándar — no controllers gordos, no lógica en `pincore/`.

---

## Flujo recomendado

```
HTTP → Controller (delgado)
         ↓
      Component (negocio)
         ↓
      Model / DB / Cache
         ↓
      Portal (acceso estático)
```

---

## Ejemplo: servicio de pedidos

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

## Exponer vía Portal

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

## Controller delgado

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

## Inyección de dependencias (opcional)

Un Component puede recibir otros Components en su constructor:

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

El contenedor de Pinoox los resuelve cuando el Portal está enlazado.

---

## Dónde poner cada cosa

| Capa | Responsabilidad |
|-------|----------------|
| `Controller/` | HTTP, códigos de estado, view/json |
| `Component/` | Reglas de negocio, orquestación |
| `Model/` | Eloquent, consultas, relaciones |
| `Flow/` | Auth, middleware |
| `Portal/` | Fachada al Component |

---

## Probar un servicio

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## Consejos

- Mantén los servicios sin estado; pasa sesión/request como parámetros
- Usa Portales solo para APIs públicas de la app — no hagas Portal de todo
- No importes directamente entre apps; usa API/eventos/portales del núcleo

---

## Documentación relacionada

- [Portal](../basic/portal.md)
- [Validación](../basic/validation.md)
- [Primeros pasos con base de datos](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Volver al índice](../README.md)

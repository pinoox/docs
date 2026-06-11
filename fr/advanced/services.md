# Services d'application (Component + Portal)

[← Retour à l'index](../README.md)

Dans Pinoox 3.x, la logique métier réside dans **`apps/{package}/Component/`** et est exposée via **`Portal/`**. C'est le modèle HMVC standard — pas de contrôleurs obèses, pas de logique dans `pincore/`.

---

## Flux recommandé

```
HTTP → Controller (léger)
         ↓
      Component (métier)
         ↓
      Model / DB / Cache
         ↓
      Portal (accès statique)
```

---

## Exemple : service de commandes

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

## Exposer via un Portal

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

## Contrôleur léger

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

## Injection de dépendances (optionnel)

Un Component peut recevoir d'autres Components dans son constructeur :

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

Le conteneur Pinoox les résout lorsque le Portal est lié (bind).

---

## Où placer quoi

| Couche | Responsabilité |
|-------|----------------|
| `Controller/` | HTTP, codes de statut, view/json |
| `Component/` | Règles métier, orchestration |
| `Model/` | Eloquent, requêtes, relations |
| `Flow/` | Authentification, middleware |
| `Portal/` | Façade vers le Component |

---

## Tester un service

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## Conseils

- Gardez les services sans état (stateless) ; passez la session/request en paramètres
- Utilisez les Portals uniquement pour les API publiques de l'application — ne mettez pas tout derrière un Portal
- N'importez pas directement entre applications ; utilisez plutôt les API/événements/portails du cœur

---

## Documentation associée

- [Portal](../basic/portal.md)
- [Validation](../basic/validation.md)
- [Premiers pas avec la base de données](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Retour à l'index](../README.md)

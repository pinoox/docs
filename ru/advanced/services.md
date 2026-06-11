# Сервисы приложения (Component + Portal)

[← Назад к оглавлению](../README.md)

В Pinoox 3.x бизнес-логика располагается в **`apps/{package}/Component/`** и предоставляется наружу через **`Portal/`**. Это стандартный HMVC-паттерн — без «толстых» контроллеров и без логики в `pincore/`.

---

## Рекомендуемый поток

```
HTTP → Controller (тонкий)
         ↓
      Component (бизнес-логика)
         ↓
      Model / DB / Cache
         ↓
      Portal (статический доступ)
```

---

## Пример: сервис заказов

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

## Публикация через Portal

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

## Тонкий контроллер

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

## Внедрение зависимостей (опционально)

Component может получать другие Components через конструктор:

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

Контейнер Pinoox разрешает их при привязке портала.

---

## Что куда класть

| Слой | Ответственность |
|-------|----------------|
| `Controller/` | HTTP, коды статусов, view/json |
| `Component/` | Бизнес-правила, оркестрация |
| `Model/` | Eloquent, запросы, связи |
| `Flow/` | Аутентификация, middleware |
| `Portal/` | Фасад для Component |

---

## Тестирование сервиса

```php
// tests/Unit/OrderServiceTest.php
$order = OrderService::create([
    'product_id' => 1,
    'qty' => 2,
]);
expect($order->qty)->toBe(2);
```

---

## Советы

- Делайте сервисы без состояния (stateless); передавайте сессию/запрос как параметры
- Используйте порталы только для публичных API приложения — не превращайте всё в Portal
- Не импортируйте напрямую между приложениями; используйте API/события/базовые порталы

---

## Связанные документы

- [Portal](../basic/portal.md)
- [Валидация](../basic/validation.md)
- [База данных: начало работы](../database/getting-started.md)
- [Flow / Middleware](../basic/flows.md)

---

[← Назад к оглавлению](../README.md)

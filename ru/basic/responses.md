# HTTP-ответ (Response)

[← Вернуться к оглавлению](../README.md)

В Pinoox 3.x каждый контроллер должен возвращать HTTP-ответ. Для HTML используйте **`View::render()`**; для JSON — **`response()->json()`** или **`ApiController`**.

---

## HTML-ответ (стандартный)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Для сырого HTML без Twig:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## JSON-ответ (API)

```php
public function list()
{
    $products = ProductModel::limit(10)->get();

    return response()->json([
        'success' => true,
        'data' => $products,
    ], 200);
}
```

Параметры `json()`:

| Параметр | Описание |
|-----------|-------------|
| `$data` | Массив или объект, сериализуемый в JSON |
| `$status` | Код состояния HTTP (по умолчанию 200) |
| `$headers` | Дополнительные заголовки (опционально) |

---

## ApiController — стандартный конверт

```php
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function show(int $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return $this->fail('NOT_FOUND', 'Product not found.', status: 404);
        }

        return $this->ok($product);
    }
}
```

---

## `json()` в базовом контроллере

```php
return $this->json(['items' => $items], 200);
```

---

## Перенаправление (Redirect)

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` и `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML, обёрнутый в готовый Response
return View::response('pages/home', ['title' => 'Home']);

// Twig-файл, выводящий JavaScript (например, pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## Пример валидации в API

```php
use Pinoox\Component\Http\Request;
use Pinoox\Component\Validation\ValidationException;
use Pinoox\Portal\Validation;

public function store(Request $request)
{
    try {
        $validated = Validation::validate($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        $product = ProductModel::create($validated);

        return response()->json([
            'success' => true,
            'data' => $product,
        ], 201);
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
        ], 422);
    }
}
```

---

## Советы

- Для API `json()` автоматически устанавливает `Content-Type`
- Явно задавайте коды состояния HTTP: `201` для создания, `422` для ошибок валидации, `404` если не найдено
- HTML-страницы рендерите через **`View::render()`**

---

## Связанные документы

- [Запрос (Request)](./requests.md)
- [Контроллеры (Controllers)](./controllers.md)
- [Валидация](./validation.md)
- [Представления (Views)](./views.md)
- [Portal](./portal.md)

---

[← Вернуться к оглавлению](../README.md)

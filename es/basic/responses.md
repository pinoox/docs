# Response HTTP

[← Volver al índice](../README.md)

En Pinoox 3.x todo controller debe devolver una respuesta HTTP. Para HTML usa **`View::render()`**; para JSON usa **`response()->json()`** o **`ApiController`**.

---

## Respuesta HTML (estándar)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Para HTML directo sin Twig:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## Respuesta JSON (API)

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

Parámetros de `json()`:

| Parámetro | Descripción |
|-----------|-------------|
| `$data` | Array u objeto serializable a JSON |
| `$status` | Código de estado HTTP (200 por defecto) |
| `$headers` | Cabeceras adicionales (opcional) |

---

## ApiController — envoltorio estándar

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

## `json()` en el controller base

```php
return $this->json(['items' => $items], 200);
```

---

## Redirección

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` y `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML envuelto en un Response listo
return View::response('pages/home', ['title' => 'Home']);

// Archivo Twig que genera JavaScript (p. ej. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## Ejemplo de validación en una API

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

## Consejos

- Para APIs, `json()` establece el `Content-Type` automáticamente
- Establece los códigos de estado HTTP explícitamente: `201` para crear, `422` para errores de validación, `404` para no encontrado
- Renderiza las páginas HTML con **`View::render()`**

---

## Documentación relacionada

- [Request](./requests.md)
- [Controllers](./controllers.md)
- [Validación](./validation.md)
- [Vistas](./views.md)
- [Portal](./portal.md)

---

[← Volver al índice](../README.md)

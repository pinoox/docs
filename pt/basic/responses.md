# Resposta HTTP

[← Voltar ao índice](../README.md)

No Pinoox 3.x, todo controller deve retornar uma resposta HTTP. Para HTML use **`View::render()`**; para JSON use **`response()->json()`** ou **`ApiController`**.

---

## Resposta HTML (padrão)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Para HTML bruto sem Twig:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## Resposta JSON (API)

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

Parâmetros de `json()`:

| Parâmetro | Descrição |
|-----------|-------------|
| `$data` | Array ou objeto serializável em JSON |
| `$status` | Código de status HTTP (padrão 200) |
| `$headers` | Cabeçalhos extras (opcional) |

---

## ApiController — envelope padrão

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

## `json()` no controller base

```php
return $this->json(['items' => $items], 200);
```

---

## Redirect

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` e `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML encapsulado em Response pronto
return View::response('pages/home', ['title' => 'Home']);

// Arquivo Twig que gera JavaScript (ex.: pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## Exemplo de validação em API

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

## Dicas

- Para APIs, `json()` define `Content-Type` automaticamente
- Defina códigos HTTP explicitamente: `201` para criação, `422` para erros de validação, `404` para não encontrado
- Renderize páginas HTML com **`View::render()`**

---

## Documentação relacionada

- [Request](./requests.md)
- [Controllers](./controllers.md)
- [Validação](./validation.md)
- [Views](./views.md)
- [Portal](./portal.md)

---

[← Voltar ao índice](../README.md)

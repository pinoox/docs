# HTTP Response

In Pinoox 3.x every controller must return an HTTP response. For HTML use **`View::render()`**; for JSON use **`response()->json()`** or **`ApiController`**.

---

## HTML response (standard)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

For raw HTML without Twig:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## JSON response (API)

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

`json()` parameters:

| Parameter | Description |
|-----------|-------------|
| `$data` | Array or object serializable to JSON |
| `$status` | HTTP status code (default 200) |
| `$headers` | Extra headers (optional) |

---

## ApiController — standard envelope

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

## `json()` on base controller

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

## `View::response()` and `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML wrapped in a ready Response
return View::response('pages/home', ['title' => 'Home']);

// Twig file that outputs JavaScript (e.g. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## Validation example in API

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

## Tips

- For APIs, `json()` sets `Content-Type` automatically
- Set HTTP status codes explicitly: `201` for create, `422` for validation errors, `404` for not found
- Render HTML pages with **`View::render()`**

---

## Related docs

- [Request](./requests.md)
- [Controllers](./controllers.md)
- [Validation](./validation.md)
- [Views](./views.md)
- [Portal](./portal.md)

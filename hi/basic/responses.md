# HTTP Response

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में हर controller को HTTP response return करना चाहिए। HTML के लिए **`View::render()`**; JSON के लिए **`response()->json()`** या **`ApiController`** उपयोग करें।

---

## HTML response (standard)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Twig के बिना raw HTML:

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

## Base controller पर `json()`

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

## `View::response()` और `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML wrapped in a ready Response
return View::response('pages/home', ['title' => 'Home']);

// Twig file that outputs JavaScript (e.g. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## API में validation example

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

- APIs के लिए `json()` automatically `Content-Type` set करता है
- HTTP status codes explicitly set करें: create के लिए `201`, validation errors के लिए `422`, not found के लिए `404`
- HTML pages **`View::render()`** से render करें

---

## संबंधित docs

- [Request](./requests.md)
- [Controllers](./controllers.md)
- [Validation](./validation.md)
- [Views](./views.md)
- [Portal](./portal.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

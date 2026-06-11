# HTTP Response

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 모든 Controller는 HTTP response를 반환해야 합니다. HTML은 **`View::render()`**; JSON은 **`response()->json()`** 또는 **`ApiController`**를 사용하세요.

---

## HTML response (표준)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Twig 없이 raw HTML:

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
| `$data` | JSON으로 직렬화 가능한 array 또는 object |
| `$status` | HTTP status code (default 200) |
| `$headers` | Extra headers (optional) |

---

## ApiController — 표준 envelope

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

## base Controller의 `json()`

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

## `View::response()`와 `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML wrapped in a ready Response
return View::response('pages/home', ['title' => 'Home']);

// Twig file that outputs JavaScript (e.g. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## API에서 Validation 예제

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

- API에서는 `json()`이 `Content-Type`을 자동 설정합니다
- HTTP status code를 명시하세요: create는 `201`, validation 오류는 `422`, not found는 `404`
- HTML 페이지는 **`View::render()`**로 렌더링하세요

---

## 관련 문서

- [Request](./requests.md)
- [Controller](./controllers.md)
- [Validation](./validation.md)
- [Views](./views.md)
- [Portal](./portal.md)

---

[← 색인으로 돌아가기](../README.md)

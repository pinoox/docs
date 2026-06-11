# HTTP 响应（Response）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，每个控制器都必须返回一个 HTTP 响应。HTML 使用 **`View::render()`**；JSON 使用 **`response()->json()`** 或 **`ApiController`**。

---

## HTML 响应（标准方式）

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

不使用 Twig 输出原始 HTML：

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## JSON 响应（API）

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

`json()` 的参数：

| 参数 | 说明 |
|-----------|-------------|
| `$data` | 可序列化为 JSON 的数组或对象 |
| `$status` | HTTP 状态码（默认 200） |
| `$headers` | 额外的响应头（可选） |

---

## ApiController — 标准响应封装

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

## 基础控制器上的 `json()`

```php
return $this->json(['items' => $items], 200);
```

---

## 重定向

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` 与 `View::jsResponse()`

```php
use Pinoox\Portal\View;

// 包装为现成 Response 的 HTML
return View::response('pages/home', ['title' => 'Home']);

// 输出 JavaScript 的 Twig 文件（例如 pinoox.twig）
return View::jsResponse('pinoox.twig');
```

---

## API 中的验证示例

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

## 小贴士

- 对于 API，`json()` 会自动设置 `Content-Type`
- 显式设置 HTTP 状态码：创建用 `201`，验证错误用 `422`，未找到用 `404`
- HTML 页面使用 **`View::render()`** 渲染

---

## 相关文档

- [请求（Request）](./requests.md)
- [控制器（Controllers）](./controllers.md)
- [验证（Validation）](./validation.md)
- [视图（Views）](./views.md)
- [Portal](./portal.md)

---

[← 返回索引](../README.md)

# استجابة HTTP (Response)

[← العودة إلى الفهرس](../README.md)

في Pinoox 3.x يجب أن يُرجع كل متحكم استجابة HTTP. لـ HTML استخدم **`View::render()`**؛ لـ JSON استخدم **`response()->json()`** أو **`ApiController`**.

---

## استجابة HTML (معيارية)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

لـ HTML خام بدون Twig:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## استجابة JSON (API)

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

معاملات `json()`:

| المعامل | الوصف |
|-----------|-------------|
| `$data` | مصفوفة أو كائن قابل للتسلسل إلى JSON |
| `$status` | رمز حالة HTTP (افتراضي 200) |
| `$headers` | رؤوس إضافية (اختياري) |

---

## ApiController — غلاف معياري

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

## `json()` على المتحكم الأساسي

```php
return $this->json(['items' => $items], 200);
```

---

## إعادة التوجيه

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` و `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML wrapped in a ready Response
return View::response('pages/home', ['title' => 'Home']);

// Twig file that outputs JavaScript (e.g. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## مثال التحقق في API

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

## نصائح

- لـ APIs، `json()` يضبط `Content-Type` تلقائيًا
- حدّد رموز حالة HTTP صراحة: `201` للإنشاء، `422` لأخطاء التحقق، `404` للغير موجود
- اعرض صفحات HTML بـ **`View::render()`**

---

## وثائق ذات صلة

- [الطلب (Request)](./requests.md)
- [المتحكمات (Controllers)](./controllers.md)
- [التحقق (Validation)](./validation.md)
- [العروض (Views)](./views.md)
- [Portal](./portal.md)

---

[← العودة إلى الفهرس](../README.md)

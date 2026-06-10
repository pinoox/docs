# پاسخ HTTP (Response)

در پینوکس ۳.x هر کنترلر باید یک پاسخ HTTP برگرداند. برای HTML از **`View::render()`**، برای JSON از **`response()->json()`** یا **`ApiController`** استفاده کنید.

---

## پاسخ HTML (استاندارد)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'درباره ما',
]);
```

برای خروجی HTML خام (بدون Twig):

```php
return response('<h1>درباره ما</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## پاسخ JSON (API)

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

پارامترهای `json()`:

| پارامتر | توضیح |
|---------|--------|
| `$data` | آرایه یا شیء قابل تبدیل به JSON |
| `$status` | کد HTTP (پیش‌فرض ۲۰۰) |
| `$headers` | هدرهای اضافی (اختیاری) |

---

## ApiController — envelope استاندارد

```php
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function show(int $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return $this->fail('NOT_FOUND', 'محصول یافت نشد.', status: 404);
        }

        return $this->ok($product);
    }
}
```

---

## متد json() در کنترلر پایه

```php
return $this->json(['items' => $items], 200);
```

---

## ریدایرکت

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## View::response() و View::jsResponse()

```php
use Pinoox\Portal\View;

// HTML با Response آماده
return View::response('pages/home', ['title' => 'خانه']);

// فایل Twig که JavaScript تولید می‌کند (مثل pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## مثال اعتبارسنجی در API

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

## نکات

- برای API، `json()` هدر `Content-Type` را خودش تنظیم می‌کند
- کد HTTP را صریح بگذارید: `201` برای ایجاد، `422` برای خطای اعتبارسنجی، `404` برای یافت نشدن
- صفحات HTML را با **`View::render()`** رندر کنید

---

## مستندات مرتبط

- [Request](./requests.md)
- [کنترلر](./controllers.md)
- [اعتبارسنجی](./validation.md)
- [View](./views.md)
- [Portal](./portal.md)

# HTTP Response

[← Dizine dön](../README.md)

Pinoox 3.x'te her controller bir HTTP yanıtı döndürmelidir. HTML için **`View::render()`**; JSON için **`response()->json()`** veya **`ApiController`** kullanın.

---

## HTML yanıtı (standart)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Twig olmadan ham HTML için:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## JSON yanıtı (API)

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

`json()` parametreleri:

| Parametre | Açıklama |
|-----------|-------------|
| `$data` | JSON'a serileştirilebilir dizi veya nesne |
| `$status` | HTTP durum kodu (varsayılan 200) |
| `$headers` | Ek başlıklar (isteğe bağlı) |

---

## ApiController — standart zarf

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

## Temel controller'da `json()`

```php
return $this->json(['items' => $items], 200);
```

---

## Yönlendirme

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` ve `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML wrapped in a ready Response
return View::response('pages/home', ['title' => 'Home']);

// Twig file that outputs JavaScript (e.g. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## API'de validasyon örneği

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

## İpuçları

- API'ler için `json()` `Content-Type`'ı otomatik ayarlar
- HTTP durum kodlarını açıkça belirleyin: oluşturma için `201`, validasyon hataları için `422`, bulunamadı için `404`
- HTML sayfalarını **`View::render()`** ile render edin

---

## İlgili dokümantasyon

- [Request](./requests.md)
- [Controller](./controllers.md)
- [Validasyon](./validation.md)
- [View](./views.md)
- [Portal](./portal.md)

---

[← Dizine dön](../README.md)

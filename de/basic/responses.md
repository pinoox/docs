# HTTP-Response

[← Zurück zur Übersicht](../README.md)

In Pinoox 3.x muss jeder Controller eine HTTP-Antwort zurückgeben. Für HTML verwenden Sie **`View::render()`**; für JSON verwenden Sie **`response()->json()`** oder **`ApiController`**.

---

## HTML-Antwort (Standard)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Für rohes HTML ohne Twig:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## JSON-Antwort (API)

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

`json()`-Parameter:

| Parameter | Beschreibung |
|-----------|-------------|
| `$data` | Array oder Objekt, das nach JSON serialisierbar ist |
| `$status` | HTTP-Statuscode (Standard 200) |
| `$headers` | Zusätzliche Header (optional) |

---

## ApiController — Standard-Envelope

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

## `json()` auf dem Basis-Controller

```php
return $this->json(['items' => $items], 200);
```

---

## Redirect (Weiterleitung)

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` und `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML, verpackt in eine fertige Response
return View::response('pages/home', ['title' => 'Home']);

// Twig-Datei, die JavaScript ausgibt (z. B. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## Validierungsbeispiel in einer API

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

## Tipps

- Bei APIs setzt `json()` den `Content-Type` automatisch
- Setzen Sie HTTP-Statuscodes explizit: `201` für Erstellung, `422` für Validierungsfehler, `404` für nicht gefunden
- Rendern Sie HTML-Seiten mit **`View::render()`**

---

## Verwandte Dokumente

- [Request](./requests.md)
- [Controller](./controllers.md)
- [Validierung](./validation.md)
- [Views](./views.md)
- [Portal](./portal.md)

---

[← Zurück zur Übersicht](../README.md)

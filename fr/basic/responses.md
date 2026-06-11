# Réponse HTTP

[← Retour à l'index](../README.md)

Dans Pinoox 3.x, chaque contrôleur doit renvoyer une réponse HTTP. Pour le HTML, utilisez **`View::render()`** ; pour le JSON, **`response()->json()`** ou **`ApiController`**.

---

## Réponse HTML (standard)

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Pour du HTML brut sans Twig :

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## Réponse JSON (API)

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

Paramètres de `json()` :

| Paramètre | Description |
|-----------|-------------|
| `$data` | Tableau ou objet sérialisable en JSON |
| `$status` | Code de statut HTTP (200 par défaut) |
| `$headers` | En-têtes supplémentaires (optionnel) |

---

## ApiController — enveloppe standard

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

## `json()` sur le contrôleur de base

```php
return $this->json(['items' => $items], 200);
```

---

## Redirection

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` et `View::jsResponse()`

```php
use Pinoox\Portal\View;

// HTML encapsulé dans une Response prête
return View::response('pages/home', ['title' => 'Home']);

// Fichier Twig qui produit du JavaScript (ex. pinoox.twig)
return View::jsResponse('pinoox.twig');
```

---

## Exemple de validation dans une API

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

## Conseils

- Pour les API, `json()` définit `Content-Type` automatiquement
- Définissez explicitement les codes HTTP : `201` pour la création, `422` pour les erreurs de validation, `404` pour introuvable
- Rendez les pages HTML avec **`View::render()`**

---

## Documentation associée

- [Request](./requests.md)
- [Contrôleurs](./controllers.md)
- [Validation](./validation.md)
- [Views](./views.md)
- [Portal](./portal.md)

---

[← Retour à l'index](../README.md)

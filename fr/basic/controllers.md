# Contrôleurs

[← Retour à l'index](../README.md)

Les contrôleurs reçoivent les requêtes HTTP, travaillent avec les modèles si nécessaire, et renvoient une View ou une réponse JSON. Dans Pinoox 3.x, les contrôleurs d'app se trouvent dans `apps/{package}/Controller/` avec l'espace de noms `App\{package}\Controller`.

---

## Créer un contrôleur

```bash
php pinoox controller:create HomeController com_acme_shop
```

Fichier : `apps/com_acme_shop/Controller/HomeController.php`

---

## Structure de base (pages HTML)

```php
<?php

namespace App\com_acme_shop\Controller;

use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class HomeController extends Controller
{
    public function index()
    {
        return View::render('pages/home', [
            'title' => 'Home',
        ]);
    }
}
```

La façon standard de rendre du HTML est **`View::render()`**. Le helper `view()` existe aussi, mais préférez le Portal dans les contrôleurs.

---

## Contrôleur API

Pour les endpoints JSON, étendez **`ApiController`** et utilisez **`ok()`**, **`fail()`** et **`validated()`** :

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
        ]);

        $product = ProductModel::create($data);

        return $this->ok($product, 'Product saved.', status: 201);
    }

    public function destroy(Request $request, int $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return $this->fail('NOT_FOUND', 'Product not found.', status: 404);
        }

        $product->delete();

        return $this->ok(null, 'Deleted.');
    }
}
```

`$this->validate()` équivaut à `$this->getRequest()->validate()` et lève `ValidationException` en cas d'échec.

---

## Connecter à une route

**routes/actions.php :**

```php
use App\com_acme_shop\Controller\HomeController;
use function Pinoox\Router\action;

action('home', [HomeController::class, 'index']);
```

**routes/web.php :**

```php
use function Pinoox\Router\get;

get('/', '@home')->name('home');
```

Ou liez le contrôleur directement :

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Injection de Request

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox injecte automatiquement `Request` dans les paramètres des méthodes du contrôleur. Il n'existe pas de helper global **`request()`** — utilisez l'injection, `$this->getRequest()` ou `$this->validate()`.

---

## Réponse JSON (alternatives)

```php
// helper response
return response()->json(['items' => $items], 200);

// méthode protégée sur le contrôleur de base
return $this->json(['items' => $items], 200);
```

Pour les API structurées, **`ApiController`** avec `$this->ok()` / `$this->fail()` est recommandé.

---

## Redirection

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

Le helper **`redirect()`** transforme les chemins relatifs en URL complètes via **`Url::link()`**.

---

## Exemple complet avec un modèle

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class ProductController extends Controller
{
    public function show(Request $request, int $id)
    {
        $product = ProductModel::findOrFail($id);

        return View::render('pages/product', ['product' => $product]);
    }
}
```

---

## Recommandations

- Le dossier est **`Controller/`** (singulier) — pas `Controllers/`
- L'espace de noms inclut le nom du paquet : `App\com_acme_shop\Controller`
- Gardez les contrôleurs légers ; placez la logique lourde dans `Component/`
- N'écrivez pas de logique d'app dans `vendor/pinoox/pincore/`

---

## Documentation associée

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Views](./views.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)

# Controller

[← Zurück zur Übersicht](../README.md)

Controller empfangen HTTP-Anfragen, arbeiten bei Bedarf mit Models und geben eine View- oder JSON-Antwort zurück. In Pinoox 3.x liegen App-Controller in `apps/{package}/Controller/` mit dem Namespace `App\{package}\Controller`.

---

## Einen Controller erstellen

```bash
php pinoox controller:create HomeController com_acme_shop
```

Datei: `apps/com_acme_shop/Controller/HomeController.php`

---

## Grundstruktur (HTML-Seiten)

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

Der Standardweg, HTML zu rendern, ist **`View::render()`**. Der `view()`-Helfer existiert ebenfalls, aber bevorzugen Sie in Controllern das Portal.

---

## API-Controller

Erweitern Sie für JSON-Endpoints **`ApiController`** und verwenden Sie **`ok()`**, **`fail()`** und **`validated()`**:

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

`$this->validate()` entspricht `$this->getRequest()->validate()` und wirft bei einem Fehler eine `ValidationException`.

---

## Mit einer Route verbinden

**routes/actions.php:**

```php
use App\com_acme_shop\Controller\HomeController;
use function Pinoox\Router\action;

action('home', [HomeController::class, 'index']);
```

**routes/web.php:**

```php
use function Pinoox\Router\get;

get('/', '@home')->name('home');
```

Oder binden Sie den Controller direkt:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Request-Injection

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox injiziert `Request` automatisch in Controller-Methodenparameter. Es gibt keinen globalen **`request()`**-Helfer — verwenden Sie Injection, `$this->getRequest()` oder `$this->validate()`.

---

## JSON-Antwort (Alternativen)

```php
// response-Helfer
return response()->json(['items' => $items], 200);

// geschützte Methode auf dem Basis-Controller
return $this->json(['items' => $items], 200);
```

Für strukturierte APIs wird **`ApiController`** mit `$this->ok()` / `$this->fail()` empfohlen.

---

## Redirect (Weiterleitung)

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

Der **`redirect()`**-Helfer wandelt relative Pfade über **`Url::link()`** in vollständige URLs um.

---

## Vollständiges Beispiel mit einem Model

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

## Richtlinien

- Der Ordner heißt **`Controller/`** (Singular) — nicht `Controllers/`
- Der Namespace enthält den Paketnamen: `App\com_acme_shop\Controller`
- Halten Sie Controller schlank; lagern Sie umfangreiche Logik in `Component/` aus
- Schreiben Sie keine App-Logik in `vendor/pinoox/pincore/`

---

## Verwandte Dokumente

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Views](./views.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zur Übersicht](../README.md)

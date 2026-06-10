# Controllers

[← Back to index](../../readme.md)

Controllers receive HTTP requests, work with models when needed, and return a View or JSON response. In Pinoox 3.x, app controllers live in `apps/{package}/Controller/` with the namespace `App\{package}\Controller`.

---

## Create a controller

```bash
php pinoox controller:create HomeController com_acme_shop
```

File: `apps/com_acme_shop/Controller/HomeController.php`

---

## Basic structure (HTML pages)

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

The standard way to render HTML is **`View::render()`**. The `view()` helper also exists, but prefer the Portal in controllers.

---

## API controller

For JSON endpoints, extend **`ApiController`** and use **`ok()`**, **`fail()`**, and **`validated()`**:

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

`$this->validate()` is the same as `$this->getRequest()->validate()` and throws `ValidationException` on failure.

---

## Connect to a route

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

Or bind the controller directly:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Request injection

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox automatically injects `Request` into controller method parameters. There is no global **`request()`** helper — use injection, `$this->getRequest()`, or `$this->validate()`.

---

## JSON response (alternatives)

```php
// response helper
return response()->json(['items' => $items], 200);

// protected method on base controller
return $this->json(['items' => $items], 200);
```

For structured APIs, **`ApiController`** with `$this->ok()` / `$this->fail()` is recommended.

---

## Redirect

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

The **`redirect()`** helper turns relative paths into full URLs via **`Url::link()`**.

---

## Full example with a model

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

## Guidelines

- Folder is **`Controller/`** (singular) — not `Controllers/`
- Namespace includes the package name: `App\com_acme_shop\Controller`
- Keep controllers thin; put heavy logic in `Component/`
- Do not write app logic in `vendor/pinoox/pincore/`

---

## Related docs

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Views](./views.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../../readme.md)

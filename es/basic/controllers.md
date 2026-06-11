# Controllers

[← Volver al índice](../README.md)

Los controllers reciben las peticiones HTTP, trabajan con los modelos cuando es necesario y devuelven una Vista o una respuesta JSON. En Pinoox 3.x, los controllers de la app viven en `apps/{package}/Controller/` con el namespace `App\{package}\Controller`.

---

## Crear un controller

```bash
php pinoox controller:create HomeController com_acme_shop
```

Archivo: `apps/com_acme_shop/Controller/HomeController.php`

---

## Estructura básica (páginas HTML)

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

La forma estándar de renderizar HTML es **`View::render()`**. El helper `view()` también existe, pero prefiere el Portal en los controllers.

---

## Controller de API

Para endpoints JSON, extiende **`ApiController`** y usa **`ok()`**, **`fail()`** y **`validated()`**:

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

`$this->validate()` es lo mismo que `$this->getRequest()->validate()` y lanza `ValidationException` en caso de fallo.

---

## Conectar a una ruta

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

O vincula el controller directamente:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Inyección del Request

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox inyecta automáticamente `Request` en los parámetros de los métodos del controller. No hay un helper global **`request()`** — usa la inyección, `$this->getRequest()` o `$this->validate()`.

---

## Respuesta JSON (alternativas)

```php
// helper response
return response()->json(['items' => $items], 200);

// método protegido del controller base
return $this->json(['items' => $items], 200);
```

Para APIs estructuradas, se recomienda **`ApiController`** con `$this->ok()` / `$this->fail()`.

---

## Redirección

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

El helper **`redirect()`** convierte rutas relativas en URLs completas mediante **`Url::link()`**.

---

## Ejemplo completo con un modelo

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

## Pautas

- La carpeta es **`Controller/`** (singular) — no `Controllers/`
- El namespace incluye el nombre del paquete: `App\com_acme_shop\Controller`
- Mantén los controllers ligeros; pon la lógica pesada en `Component/`
- No escribas lógica de la app en `vendor/pinoox/pincore/`

---

## Documentación relacionada

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Vistas](./views.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)

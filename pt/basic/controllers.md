# Controllers

[← Voltar ao índice](../README.md)

Controllers recebem requisições HTTP, trabalham com models quando necessário e retornam uma View ou resposta JSON. No Pinoox 3.x, os controllers do app ficam em `apps/{package}/Controller/` com o namespace `App\{package}\Controller`.

---

## Criar um controller

```bash
php pinoox controller:create HomeController com_acme_shop
```

Arquivo: `apps/com_acme_shop/Controller/HomeController.php`

---

## Estrutura básica (páginas HTML)

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

A forma padrão de renderizar HTML é **`View::render()`**. O helper `view()` também existe, mas prefira o Portal nos controllers.

---

## Controller de API

Para endpoints JSON, estenda **`ApiController`** e use **`ok()`**, **`fail()`** e **`validated()`**:

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

`$this->validate()` é o mesmo que `$this->getRequest()->validate()` e lança `ValidationException` em caso de falha.

---

## Conectar a uma rota

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

Ou vincule o controller diretamente:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Injeção de Request

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

O Pinoox injeta `Request` automaticamente nos parâmetros dos métodos do controller. Não existe helper global **`request()`** — use injeção, `$this->getRequest()` ou `$this->validate()`.

---

## Resposta JSON (alternativas)

```php
// helper response
return response()->json(['items' => $items], 200);

// método protegido no controller base
return $this->json(['items' => $items], 200);
```

Para APIs estruturadas, **`ApiController`** com `$this->ok()` / `$this->fail()` é recomendado.

---

## Redirect

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

O helper **`redirect()`** transforma caminhos relativos em URLs completas via **`Url::link()`**.

---

## Exemplo completo com model

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

## Diretrizes

- A pasta é **`Controller/`** (singular) — não `Controllers/`
- O namespace inclui o nome do pacote: `App\com_acme_shop\Controller`
- Mantenha controllers enxutos; coloque lógica pesada em `Component/`
- Não escreva lógica de app em `vendor/pinoox/pincore/`

---

## Documentação relacionada

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Views](./views.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

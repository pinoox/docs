# Контроллеры (Controllers)

[← Вернуться к оглавлению](../README.md)

Контроллеры принимают HTTP-запросы, при необходимости работают с моделями и возвращают View или JSON-ответ. В Pinoox 3.x контроллеры приложений находятся в `apps/{package}/Controller/` с пространством имён `App\{package}\Controller`.

---

## Создание контроллера

```bash
php pinoox controller:create HomeController com_acme_shop
```

Файл: `apps/com_acme_shop/Controller/HomeController.php`

---

## Базовая структура (HTML-страницы)

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

Стандартный способ рендеринга HTML — **`View::render()`**. Хелпер `view()` тоже существует, но в контроллерах предпочитайте Portal.

---

## API-контроллер

Для JSON-эндпоинтов наследуйтесь от **`ApiController`** и используйте **`ok()`**, **`fail()`** и **`validated()`**:

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

`$this->validate()` — то же самое, что `$this->getRequest()->validate()`, и при ошибке выбрасывает `ValidationException`.

---

## Привязка к маршруту

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

Или привяжите контроллер напрямую:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Внедрение Request

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox автоматически внедряет `Request` в параметры методов контроллера. Глобального хелпера **`request()`** нет — используйте внедрение, `$this->getRequest()` или `$this->validate()`.

---

## JSON-ответ (альтернативы)

```php
// хелпер response
return response()->json(['items' => $items], 200);

// защищённый метод базового контроллера
return $this->json(['items' => $items], 200);
```

Для структурированных API рекомендуется **`ApiController`** с `$this->ok()` / `$this->fail()`.

---

## Перенаправление (Redirect)

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

Хелпер **`redirect()`** превращает относительные пути в полные URL через **`Url::link()`**.

---

## Полный пример с моделью

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

## Рекомендации

- Папка называется **`Controller/`** (в единственном числе) — не `Controllers/`
- Пространство имён включает имя пакета: `App\com_acme_shop\Controller`
- Держите контроллеры «тонкими»; тяжёлую логику размещайте в `Component/`
- Не пишите логику приложения в `vendor/pinoox/pincore/`

---

## Связанные документы

- [Роутер (Router)](./routers.md)
- [Запрос (Request)](./requests.md)
- [Flow](./flows.md)
- [Представления (Views)](./views.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)

# 控制器（Controllers）

[← 返回索引](../README.md)

控制器接收 HTTP 请求，在需要时与模型交互，并返回 View 或 JSON 响应。在 Pinoox 3.x 中，应用控制器位于 `apps/{package}/Controller/`，命名空间为 `App\{package}\Controller`。

---

## 创建控制器

```bash
php pinoox controller:create HomeController com_acme_shop
```

文件：`apps/com_acme_shop/Controller/HomeController.php`

---

## 基本结构（HTML 页面）

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

渲染 HTML 的标准方式是 **`View::render()`**。`view()` 辅助函数也存在，但在控制器中建议使用 Portal。

---

## API 控制器

对于 JSON 端点，请继承 **`ApiController`** 并使用 **`ok()`**、**`fail()`** 和 **`validated()`**：

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

`$this->validate()` 等同于 `$this->getRequest()->validate()`，验证失败时会抛出 `ValidationException`。

---

## 关联到路由

**routes/actions.php：**

```php
use App\com_acme_shop\Controller\HomeController;
use function Pinoox\Router\action;

action('home', [HomeController::class, 'index']);
```

**routes/web.php：**

```php
use function Pinoox\Router\get;

get('/', '@home')->name('home');
```

或者直接绑定控制器：

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Request 注入

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox 会自动将 `Request` 注入到控制器方法参数中。不存在全局的 **`request()`** 辅助函数 — 请使用注入、`$this->getRequest()` 或 `$this->validate()`。

---

## JSON 响应（替代方式）

```php
// response 辅助函数
return response()->json(['items' => $items], 200);

// 基础控制器上的受保护方法
return $this->json(['items' => $items], 200);
```

对于结构化 API，推荐使用 **`ApiController`** 搭配 `$this->ok()` / `$this->fail()`。

---

## 重定向

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

**`redirect()`** 辅助函数会通过 **`Url::link()`** 将相对路径转换为完整 URL。

---

## 带模型的完整示例

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

## 指导原则

- 文件夹是 **`Controller/`**（单数）— 不是 `Controllers/`
- 命名空间包含包名：`App\com_acme_shop\Controller`
- 保持控制器轻量；把复杂逻辑放在 `Component/` 中
- 不要在 `vendor/pinoox/pincore/` 中编写应用逻辑

---

## 相关文档

- [路由（Router）](./routers.md)
- [请求（Request）](./requests.md)
- [Flow](./flows.md)
- [视图（Views）](./views.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

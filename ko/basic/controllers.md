# Controller

[← 색인으로 돌아가기](../README.md)

Controller는 HTTP request를 받고, 필요 시 Model과 함께 작업한 뒤 View 또는 JSON response를 반환합니다. Pinoox 3.x에서 앱 Controller는 `apps/{package}/Controller/`에 있으며 namespace는 `App\{package}\Controller`입니다.

---

## Controller 생성

```bash
php pinoox controller:create HomeController com_acme_shop
```

파일: `apps/com_acme_shop/Controller/HomeController.php`

---

## 기본 구조 (HTML 페이지)

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

HTML 렌더링의 표준 방법은 **`View::render()`**입니다. `view()` helper도 있지만 Controller에서는 Portal 사용을 권장합니다.

---

## API Controller

JSON endpoint는 **`ApiController`**를 확장하고 **`ok()`**, **`fail()`**, **`validated()`**를 사용하세요:

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

`$this->validate()`는 `$this->getRequest()->validate()`와 같으며 실패 시 `ValidationException`을 던집니다.

---

## Route에 연결

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

또는 Controller를 직접 바인딩:

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

Pinoox는 Controller method parameter에 `Request`를 자동으로 inject합니다. 전역 **`request()`** helper는 없습니다 — injection, `$this->getRequest()`, 또는 `$this->validate()`를 사용하세요.

---

## JSON response (대안)

```php
// response helper
return response()->json(['items' => $items], 200);

// base controller의 protected method
return $this->json(['items' => $items], 200);
```

구조화된 API에는 **`ApiController`**와 `$this->ok()` / `$this->fail()`을 권장합니다.

---

## Redirect

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

**`redirect()`** helper는 상대 경로를 **`Url::link()`**를 통해 전체 URL로 변환합니다.

---

## Model을 사용한 전체 예제

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

## 가이드라인

- 폴더는 **`Controller/`**(단수) — `Controllers/` 아님
- Namespace에 package 이름 포함: `App\com_acme_shop\Controller`
- Controller는 얇게 유지; 무거운 logic은 `Component/`에
- `vendor/pinoox/pincore/`에 앱 logic을 작성하지 마세요

---

## 관련 문서

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Views](./views.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)

# المتحكمات (Controllers)

[← العودة إلى الفهرس](../README.md)

تستقبل المتحكمات طلبات HTTP، وتتعامل مع النماذج عند الحاجة، وتُرجع View أو استجابة JSON. في Pinoox 3.x، تعيش متحكمات التطبيق في `apps/{package}/Controller/` ضمن مساحة الأسماء `App\{package}\Controller`.

---

## إنشاء متحكم

```bash
php pinoox controller:create HomeController com_acme_shop
```

الملف: `apps/com_acme_shop/Controller/HomeController.php`

---

## البنية الأساسية (صفحات HTML)

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

الطريقة المعيارية لعرض HTML هي **`View::render()`**. يوجد أيضًا المساعد `view()`، لكن يُفضّل Portal في المتحكمات.

---

## متحكم API

لنقاط نهاية JSON، ورّث **`ApiController`** واستخدم **`ok()`** و**`fail()`** و**`validated()`**:

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

`$this->validate()` يعادل `$this->getRequest()->validate()` ويرمي `ValidationException` عند الفشل.

---

## ربط بمسار

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

أو اربط المتحكم مباشرة:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## حقن Request

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

يحقن Pinoox تلقائيًا `Request` في معاملات دوال المتحكم. لا يوجد مساعد عام **`request()`** — استخدم الحقن أو `$this->getRequest()` أو `$this->validate()`.

---

## استجابة JSON (بدائل)

```php
// response helper
return response()->json(['items' => $items], 200);

// protected method on base controller
return $this->json(['items' => $items], 200);
```

لـ APIs منظمة، يُوصى بـ **`ApiController`** مع `$this->ok()` / `$this->fail()`.

---

## إعادة التوجيه

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

المساعد **`redirect()`** يحوّل المسارات النسبية إلى عناوين URL كاملة عبر **`Url::link()`**.

---

## مثال كامل مع نموذج

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

## إرشادات

- المجلد هو **`Controller/`** (مفرد) — وليس `Controllers/`
- مساحة الأسماء تتضمن اسم الحزمة: `App\com_acme_shop\Controller`
- اجعل المتحكمات رقيقة؛ ضع المنطق الثقيل في `Component/`
- لا تكتب منطق التطبيق في `vendor/pinoox/pincore/`

---

## وثائق ذات صلة

- [المُوجّه (Router)](./routers.md)
- [الطلب (Request)](./requests.md)
- [Flow](./flows.md)
- [العروض (Views)](./views.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)

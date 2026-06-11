# کنترلر (Controller)

[← بازگشت به فهرست](../README.md)

کنترلر لایه‌ای است که درخواست HTTP را می‌گیرد، در صورت نیاز با Model کار می‌کند و View یا JSON برمی‌گرداند. در پینوکس ۳.x کنترلرهای اپ در `apps/{package}/Controller/` با namespace `App\{package}\Controller` قرار می‌گیرند.

---

## ساخت کنترلر

```bash
php pinoox controller:create HomeController com_acme_shop
```

فایل: `apps/com_acme_shop/Controller/HomeController.php`

---

## ساختار پایه (صفحات HTML)

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
            'title' => 'صفحه اصلی',
        ]);
    }
}
```

روش استاندارد رندر HTML: **`View::render()`**. تابع `view()` هم وجود دارد اما در کنترلر از Portal استفاده کنید.

---

## کنترلر API

برای endpointهای JSON از **`ApiController`** extend کنید و متدهای **`ok()`**، **`fail()`** و **`validated()`** را به‌کار ببرید:

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

        return $this->ok($product, 'محصول ذخیره شد.', status: 201);
    }

    public function destroy(Request $request, int $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return $this->fail('NOT_FOUND', 'محصول یافت نشد.', status: 404);
        }

        $product->delete();

        return $this->ok(null, 'حذف شد.');
    }
}
```

`$this->validate()` همان `$this->getRequest()->validate()` است و در صورت خطا `ValidationException` پرتاب می‌کند.

در برخی اپ‌ها (مثل `com_pinoox_manager`) کلاس پایه API متد **`validated($request, $rules)`** هم دارد که خطا را به JSON تبدیل می‌کند.

---

## اتصال به route

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

یا مستقیم:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## تزریق Request

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

پینوکس `Request` را به‌صورت خودکار به پارامتر متد تزریق می‌کند. helper سراسری **`request()`** وجود ندارد — از تزریق، `$this->getRequest()` یا `$this->validate()` استفاده کنید.

---

## پاسخ JSON (روش‌های جایگزین)

```php
// helper response
return response()->json(['items' => $items], 200);

// متد protected کنترل پایه
return $this->json(['items' => $items], 200);
```

برای API ساختاریافته، **`ApiController`** و `$this->ok()` / `$this->fail()` توصیه می‌شود.

---

## Redirect

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

helper **`redirect()`** مسیر نسبی را با **`Url::link()`** به URL کامل تبدیل می‌کند.

---

## مثال کامل با Model

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

## نکات

- پوشه **`Controller/`** (مفرد) — نه `Controllers/`
- namespace شامل نام پکیج: `App\com_acme_shop\Controller`
- منطق سنگین را در `Component/` بگذارید؛ کنترلر نازک بماند
- منطق اپ را در `vendor/pinoox/pincore/` ننویسید

---

## مستندات مرتبط

- [روتر](./routers.md)
- [درخواست — Request](./requests.md)
- [فلو — Flow](./flows.md)
- [View — ویو](./views.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

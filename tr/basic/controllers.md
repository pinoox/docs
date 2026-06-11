# Controller

[← Dizine dön](../README.md)

Controller'lar HTTP isteklerini alır, gerektiğinde model'lerle çalışır ve View veya JSON yanıtı döndürür. Pinoox 3.x'te uygulama controller'ları `apps/{package}/Controller/` içinde, `App\{package}\Controller` namespace'i ile yer alır.

---

## Controller oluşturma

```bash
php pinoox controller:create HomeController com_acme_shop
```

Dosya: `apps/com_acme_shop/Controller/HomeController.php`

---

## Temel yapı (HTML sayfaları)

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

HTML render etmenin standart yolu **`View::render()`**'dır. `view()` helper'ı da vardır, ancak controller'larda Portal'ı tercih edin.

---

## API controller

JSON endpoint'leri için **`ApiController`**'ı genişletin ve **`ok()`**, **`fail()`** ile **`validated()`** kullanın:

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

`$this->validate()`, `$this->getRequest()->validate()` ile aynıdır ve başarısızlıkta `ValidationException` fırlatır.

---

## Route'a bağlama

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

Veya controller'ı doğrudan bağlayın:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Request enjeksiyonu

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox, `Request`'i controller metot parametrelerine otomatik enjekte eder. Global **`request()`** helper'ı yoktur — enjeksiyon, `$this->getRequest()` veya `$this->validate()` kullanın.

---

## JSON yanıtı (alternatifler)

```php
// response helper
return response()->json(['items' => $items], 200);

// protected method on base controller
return $this->json(['items' => $items], 200);
```

Yapılandırılmış API'ler için **`ApiController`** ile `$this->ok()` / `$this->fail()` önerilir.

---

## Yönlendirme

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

**`redirect()`** helper'ı göreli yolları **`Url::link()`** üzerinden tam URL'lere dönüştürür.

---

## Model ile tam örnek

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

## Yönergeler

- Klasör **`Controller/`** (tekil) — `Controllers/` değil
- Namespace paket adını içerir: `App\com_acme_shop\Controller`
- Controller'ları ince tutun; ağır mantığı `Component/` içine koyun
- Uygulama mantığını `vendor/pinoox/pincore/` içine yazmayın

---

## İlgili dokümantasyon

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [View](./views.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

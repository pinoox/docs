# Controllers

[← इंडेक्स पर वापस जाएँ](../README.md)

Controllers HTTP requests प्राप्त करते हैं, आवश्यकता पड़ने पर models के साथ काम करते हैं, और एक View या JSON response लौटाते हैं। Pinoox 3.x में, ऐप controllers `apps/{package}/Controller/` में `App\{package}\Controller` namespace के साथ रहते हैं।

---

## Controller बनाएँ

```bash
php pinoox controller:create HomeController com_acme_shop
```

फ़ाइल: `apps/com_acme_shop/Controller/HomeController.php`

---

## मूल संरचना (HTML पेज)

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

HTML रेंडर करने का मानक तरीका **`View::render()`** है। `view()` हेल्पर भी मौजूद है, लेकिन controllers में Portal को प्राथमिकता दें।

---

## API controller

JSON endpoints के लिए, **`ApiController`** को extend करें और **`ok()`**, **`fail()`** तथा **`validated()`** का उपयोग करें:

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

`$this->validate()` वही है जो `$this->getRequest()->validate()` है और विफलता पर `ValidationException` throw करता है।

---

## Route से जोड़ें

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

या controller को सीधे bind करें:

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

Pinoox controller method पैरामीटर्स में `Request` को स्वतः inject करता है। कोई ग्लोबल **`request()`** हेल्पर नहीं है — injection, `$this->getRequest()`, या `$this->validate()` का उपयोग करें।

---

## JSON response (विकल्प)

```php
// response हेल्पर
return response()->json(['items' => $items], 200);

// base controller पर protected method
return $this->json(['items' => $items], 200);
```

संरचित APIs के लिए, `$this->ok()` / `$this->fail()` के साथ **`ApiController`** अनुशंसित है।

---

## Redirect

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

**`redirect()`** हेल्पर **`Url::link()`** के माध्यम से सापेक्ष (relative) पाथ को पूर्ण URL में बदलता है।

---

## Model के साथ पूर्ण उदाहरण

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

## दिशानिर्देश

- फ़ोल्डर **`Controller/`** (एकवचन) है — `Controllers/` नहीं
- Namespace में package का नाम शामिल है: `App\com_acme_shop\Controller`
- Controllers को हल्का रखें; भारी लॉजिक `Component/` में रखें
- ऐप लॉजिक `vendor/pinoox/pincore/` में न लिखें

---

## संबंधित दस्तावेज़

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [Views](./views.md)
- [प्रोजेक्ट संरचना](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

# ग्लोबल Helpers

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x ग्लोबल helpers को `pincore/functions/` से लोड करता है। रोज़मर्रा के ऐप विकास के लिए ये helpers (साथ में Portals) पर्याप्त हैं — core Components को सीधे instantiate न करें।

---

## मुख्य helpers

| Helper | उद्देश्य | उदाहरण |
|--------|---------|---------|
| `render()` | String के रूप में HTML | `$html = render('email', $data);` |
| `response()` | HTTP response | `return response()->json($data);` |
| `redirect()` | Redirect | `return redirect(url('login'));` |
| `url()` | ऐप/साइट URL | `url('products')` |
| `path()` | डिस्क पर फ़ाइल पथ | `path('storage/logs/app.log')` |
| `assets()` | Theme फ़ाइल URL | `assets('dist/app.css')` |
| `config()` | कॉन्फ़िग पढ़ें/लिखें | `config('app.name')` |
| `t()` | अनुवाद (return) | `t('welcome.title')` |
| `lang()` | अनुवाद (echo) | `lang('welcome.title')` |
| `app()` | सक्रिय ऐप | `app()->get('package')` |
| `auth()` | लॉग-इन उपयोगकर्ता | `auth()` → `Auth::user()` |
| `user()` | उपयोगकर्ता फ़ील्ड | `user('email')` |
| `isLogin()` | लॉगिन स्थिति | `if (isLogin()) { … }` |
| `session()` | Session | `session('token')` |
| `runtime()` | सक्रिय HTTP kernel | `runtime()->getRequest()` |
| `_env()` | Environment variable | `_env('APP_DEBUG', false)` |
| `alias()` | Flow/class alias | `alias('auth')` |

Controllers में HTML के लिए **`View::render()`** का उपयोग करें (system ऐप्स की तरह)। `view()` helper मौजूद है, लेकिन controllers में Portal को प्राथमिकता दें।

---

## Request — injection या `runtime()`

Pincore में कोई ग्लोबल `request()` helper नहीं है। Controllers और components में type-hint injection का उपयोग करें:

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

किसी Flow में या ऐसी जगह जहाँ signature injection की अनुमति नहीं देता:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// वर्तमान उपयोगकर्ता (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) वही है जो user($key)
$email = auth('email');

// Flow alias से routes को सुरक्षित करें
// app.php → 'alias' => ['auth' => AuthFlow::class]
// routes → ->flows(['auth']) या flows के साथ group
```

---

## View और Response

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## Config

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## Lang

```php
$label = t('product.title');
// Twig में: {{ t('product.title') }}
```

---

## URL और Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## कस्टम ऐप helpers

`app.php` में:

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return '$' . number_format($amount, 2);
}
```

---

## Twig helpers (templates में)

PHP helpers के अलावा, ये Twig में उपलब्ध हैं:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## सुझाव

- Controllers में HTML के लिए `View::render()` का उपयोग करें; रोज़मर्रा के कामों के लिए `url()`, `t()`, और `config()` जैसे helpers
- Helpers केवल Pinoox bootstrap के बाद काम करते हैं — इन्हें `index.php` / `pinoox` के बाहर raw PHP स्क्रिप्ट में लोड न करें
- जटिल लॉजिक के लिए, कस्टम helpers की बजाय `Component/` + Portal को प्राथमिकता दें

---

## संबंधित दस्तावेज़

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Language](../basic/language.md)
- [Services](./services.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

# Global helper'lar

[← Dizine dön](../README.md)

Pinoox 3.x global helper'ları `pincore/functions/` içinden yükler. Günlük uygulama geliştirmesi için bu helper'lar (artı Portal'lar) yeterlidir — çekirdek Component'leri doğrudan örneklemeyin.

---

## Ana helper'lar

| Helper | Amaç | Örnek |
|--------|---------|---------|
| `render()` | Dize olarak HTML | `$html = render('email', $data);` |
| `response()` | HTTP yanıtı | `return response()->json($data);` |
| `redirect()` | Yönlendirme | `return redirect(url('login'));` |
| `url()` | Uygulama/site URL'si | `url('products')` |
| `path()` | Diskte dosya yolu | `path('storage/logs/app.log')` |
| `assets()` | Tema dosyası URL'si | `assets('dist/app.css')` |
| `config()` | Config okuma/yazma | `config('app.name')` |
| `t()` | Çeviri (döndür) | `t('welcome.title')` |
| `lang()` | Çeviri (echo) | `lang('welcome.title')` |
| `app()` | Aktif uygulama | `app()->get('package')` |
| `auth()` | Giriş yapmış kullanıcı | `auth()` → `Auth::user()` |
| `user()` | Kullanıcı alanı | `user('email')` |
| `isLogin()` | Giriş durumu | `if (isLogin()) { … }` |
| `session()` | Session | `session('token')` |
| `runtime()` | Aktif HTTP kernel | `runtime()->getRequest()` |
| `_env()` | Ortam değişkeni | `_env('APP_DEBUG', false)` |
| `alias()` | Flow/sınıf takma adı | `alias('auth')` |

Controller'larda HTML için **`View::render()`** kullanın (sistem uygulamalarıyla aynı). `view()` helper'ı da vardır ancak controller'larda Portal'ı tercih edin.

---

## Request — enjeksiyon veya `runtime()`

pincore'da global `request()` helper'ı yoktur. Controller ve component'lerde tip ipucu enjeksiyonu kullanın:

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

İmza enjeksiyona izin vermeyen Flow veya başka yerlerde:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// Current user (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) is the same as user($key)
$email = auth('email');

// Protect routes with Flow alias
// app.php → 'alias' => ['auth' => AuthFlow::class]
// routes → ->flows(['auth']) or group with flows
```

---

## View ve Response

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
// In Twig: {{ t('product.title') }}
```

---

## URL ve Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Özel uygulama helper'ları

`app.php` içinde:

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

## Twig helper'ları (şablonlarda)

PHP helper'larına ek olarak Twig'de bunlar kullanılabilir:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## İpuçları

- HTML için controller'larda `View::render()`; günlük işler için `url()`, `t()` ve `config()` gibi helper'lar
- Helper'lar yalnızca Pinoox bootstrap'tan sonra çalışır — `index.php` / `pinoox` dışındaki ham PHP script'lerinde yüklemeyin
- Karmaşık mantık için özel helper yerine `Component/` + Portal tercih edin

---

## İlgili dokümantasyon

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Dil](../basic/language.md)
- [Servisler](./services.md)

---

[← Dizine dön](../README.md)

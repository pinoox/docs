# Global Helpers

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 `pincore/functions/`에서 global helper를 로드합니다. 일상 앱 개발에는 이 helper(와 Portal)면 충분 — core Component를 직접 instantiate하지 마세요.

---

## 주요 helper

| Helper | Purpose | Example |
|--------|---------|---------|
| `render()` | HTML as string | `$html = render('email', $data);` |
| `response()` | HTTP response | `return response()->json($data);` |
| `redirect()` | Redirect | `return redirect(url('login'));` |
| `url()` | App/site URL | `url('products')` |
| `path()` | File path on disk | `path('storage/logs/app.log')` |
| `assets()` | Theme file URL | `assets('dist/app.css')` |
| `config()` | Read/write config | `config('app.name')` |
| `t()` | Translation (return) | `t('welcome.title')` |
| `lang()` | Translation (echo) | `lang('welcome.title')` |
| `app()` | Active app | `app()->get('package')` |
| `auth()` | Logged-in user | `auth()` → `Auth::user()` |
| `user()` | User field | `user('email')` |
| `isLogin()` | Login status | `if (isLogin()) { … }` |
| `session()` | Session | `session('token')` |
| `runtime()` | Active HTTP kernel | `runtime()->getRequest()` |
| `_env()` | Environment variable | `_env('APP_DEBUG', false)` |
| `alias()` | Flow/class alias | `alias('auth')` |

Controller HTML은 **`View::render()`** 사용 (system app과 동일). `view()` helper도 있지만 Controller에서는 Portal 권장.

---

## Request — injection 또는 `runtime()`

pincore에 global `request()` helper 없음. Controller와 Component에서 type-hint injection:

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

signature에 injection 불가한 Flow 등:

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

## View and Response

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

## URL and Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Custom app helper

`app.php`에서:

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

## Twig helper (template)

PHP helper 외 template에서 사용 가능:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## Tips

- Controller HTML은 `View::render()`; `url()`, `t()`, `config()` 등 helper로 일상 작업
- Helper는 Pinoox bootstrap 후에만 — `index.php` / `pinoox` 밖 raw PHP script에서 로드 금지
- 복잡 logic은 custom helper보다 `Component/` + Portal

---

## 관련 문서

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Language](../basic/language.md)
- [Services](./services.md)

---

[← 색인으로 돌아가기](../README.md)

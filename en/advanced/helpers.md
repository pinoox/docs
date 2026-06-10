# Global Helpers

Pinoox 3.x loads global helpers from `pincore/functions/`. For day-to-day app development these helpers (plus Portals) are enough — do not instantiate core Components directly.

---

## Main helpers

| Helper | Purpose | Example |
|--------|---------|---------|
| `render()` | HTML as string | `$html = render('email', $data);` |
| `response()` | HTTP response | `return response()->json($data);` |
| `redirect()` | Redirect | `return redirect(url('login'));` |
| `url()` | App/site URL | `url('products')` |
| `path()` | File path on disk | `path('uploads/file.jpg')` |
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

For HTML in controllers use **`View::render()`** (same as system apps). The `view()` helper exists but prefer the Portal in controllers.

---

## Request — injection or `runtime()`

There is no global `request()` helper in pincore. In controllers and components use type-hint injection:

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

In a Flow or elsewhere where the signature does not allow injection:

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

## Custom app helpers

In `app.php`:

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

## Twig helpers (in templates)

In addition to PHP helpers, these are available in Twig:

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

- Use `View::render()` in controllers for HTML; helpers such as `url()`, `t()`, and `config()` for everyday tasks
- Helpers work only after Pinoox bootstrap — do not load them in raw PHP scripts outside `index.php` / `pinoox`
- For complex logic, prefer `Component/` + Portal over custom helpers

---

## Related docs

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Language](../basic/language.md)
- [Services](./services.md)

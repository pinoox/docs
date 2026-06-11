# Globale Helpers

[← Zurück zur Übersicht](../README.md)

Pinoox 3.x lädt globale Helpers aus `pincore/functions/`. Für die tägliche App-Entwicklung reichen diese Helpers (zusammen mit den Portals) aus — instanziieren Sie Core-Components nicht direkt.

---

## Wichtigste Helpers

| Helper | Zweck | Beispiel |
|--------|---------|---------|
| `render()` | HTML als String | `$html = render('email', $data);` |
| `response()` | HTTP-Response | `return response()->json($data);` |
| `redirect()` | Weiterleitung (Redirect) | `return redirect(url('login'));` |
| `url()` | App-/Website-URL | `url('products')` |
| `path()` | Dateipfad auf der Festplatte | `path('storage/logs/app.log')` |
| `assets()` | URL einer Theme-Datei | `assets('dist/app.css')` |
| `config()` | Konfiguration lesen/schreiben | `config('app.name')` |
| `t()` | Übersetzung (Rückgabe) | `t('welcome.title')` |
| `lang()` | Übersetzung (Ausgabe) | `lang('welcome.title')` |
| `app()` | Aktive App | `app()->get('package')` |
| `auth()` | Angemeldeter Benutzer | `auth()` → `Auth::user()` |
| `user()` | Benutzerfeld | `user('email')` |
| `isLogin()` | Login-Status | `if (isLogin()) { … }` |
| `session()` | Session | `session('token')` |
| `runtime()` | Aktiver HTTP-Kernel | `runtime()->getRequest()` |
| `_env()` | Umgebungsvariable | `_env('APP_DEBUG', false)` |
| `alias()` | Flow-/Klassen-Alias | `alias('auth')` |

Für HTML in Controllern verwenden Sie **`View::render()`** (wie in den System-Apps). Der `view()`-Helper existiert zwar, in Controllern ist jedoch das Portal vorzuziehen.

---

## Request — Injection oder `runtime()`

In pincore gibt es keinen globalen `request()`-Helper. In Controllern und Components verwenden Sie Type-Hint-Injection:

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

In einem Flow oder an anderen Stellen, an denen die Signatur keine Injection erlaubt:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// Aktueller Benutzer (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) ist dasselbe wie user($key)
$email = auth('email');

// Routen mit Flow-Alias schützen
// app.php → 'alias' => ['auth' => AuthFlow::class]
// Routen → ->flows(['auth']) oder Gruppe mit flows
```

---

## View und Response

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

## URL und Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Eigene App-Helpers

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

## Twig-Helpers (in Templates)

Zusätzlich zu den PHP-Helpers stehen in Twig folgende zur Verfügung:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## Tipps

- Verwenden Sie `View::render()` in Controllern für HTML; Helpers wie `url()`, `t()` und `config()` für alltägliche Aufgaben
- Helpers funktionieren erst nach dem Pinoox-Bootstrap — laden Sie sie nicht in rohen PHP-Skripten außerhalb von `index.php` / `pinoox`
- Für komplexe Logik bevorzugen Sie `Component/` + Portal gegenüber eigenen Helpers

---

## Verwandte Dokumente

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Sprache (Language)](../basic/language.md)
- [Services](./services.md)

---

[← Zurück zur Übersicht](../README.md)

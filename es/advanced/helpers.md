# Helpers globales

[← Volver al índice](../README.md)

Pinoox 3.x carga helpers globales desde `pincore/functions/`. Para el desarrollo diario de apps, estos helpers (más los Portales) son suficientes — no instancies Componentes del núcleo directamente.

---

## Helpers principales

| Helper | Propósito | Ejemplo |
|--------|---------|---------|
| `render()` | HTML como string | `$html = render('email', $data);` |
| `response()` | Respuesta HTTP | `return response()->json($data);` |
| `redirect()` | Redirección | `return redirect(url('login'));` |
| `url()` | URL de app/sitio | `url('products')` |
| `path()` | Ruta de archivo en disco | `path('storage/logs/app.log')` |
| `assets()` | URL de archivo del tema | `assets('dist/app.css')` |
| `config()` | Leer/escribir config | `config('app.name')` |
| `t()` | Traducción (retorno) | `t('welcome.title')` |
| `lang()` | Traducción (echo) | `lang('welcome.title')` |
| `app()` | App activa | `app()->get('package')` |
| `auth()` | Usuario conectado | `auth()` → `Auth::user()` |
| `user()` | Campo de usuario | `user('email')` |
| `isLogin()` | Estado de sesión | `if (isLogin()) { … }` |
| `session()` | Sesión | `session('token')` |
| `runtime()` | Kernel HTTP activo | `runtime()->getRequest()` |
| `_env()` | Variable de entorno | `_env('APP_DEBUG', false)` |
| `alias()` | Alias de flow/clase | `alias('auth')` |

Para HTML en controllers usa **`View::render()`** (igual que las apps del sistema). Existe el helper `view()` pero en controllers se prefiere el Portal.

---

## Request — inyección o `runtime()`

No hay helper global `request()` en pincore. En controllers y componentes usa inyección por type-hint:

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

En un Flow u otro contexto donde la firma no permita inyección:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// Usuario actual (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) es lo mismo que user($key)
$email = auth('email');

// Proteger rutas con alias Flow
// app.php → 'alias' => ['auth' => AuthFlow::class]
// routes → ->flows(['auth']) o grupo con flows
```

---

## View y Response

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
// En Twig: {{ t('product.title') }}
```

---

## URL y Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Helpers personalizados de la app

En `app.php`:

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

## Helpers Twig (en plantillas)

Además de los helpers PHP, estos están disponibles en Twig:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## Consejos

- Usa `View::render()` en controllers para HTML; helpers como `url()`, `t()` y `config()` para tareas cotidianas
- Los helpers solo funcionan tras el bootstrap de Pinoox — no los cargues en scripts PHP fuera de `index.php` / `pinoox`
- Para lógica compleja, prefiere `Component/` + Portal en lugar de helpers personalizados

---

## Documentación relacionada

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Idioma](../basic/language.md)
- [Servicios](./services.md)

---

[← Volver al índice](../README.md)

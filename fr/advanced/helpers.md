# Helpers globaux

[← Retour à l'index](../README.md)

Pinoox 3.x charge les helpers globaux depuis `pincore/functions/`. Pour le développement d'applications au quotidien, ces helpers (plus les Portals) suffisent — n'instanciez pas directement les Components du cœur.

---

## Helpers principaux

| Helper | Rôle | Exemple |
|--------|---------|---------|
| `render()` | HTML sous forme de chaîne | `$html = render('email', $data);` |
| `response()` | Réponse HTTP | `return response()->json($data);` |
| `redirect()` | Redirection | `return redirect(url('login'));` |
| `url()` | URL de l'application/du site | `url('products')` |
| `path()` | Chemin de fichier sur le disque | `path('storage/logs/app.log')` |
| `assets()` | URL d'un fichier du thème | `assets('dist/app.css')` |
| `config()` | Lire/écrire la configuration | `config('app.name')` |
| `t()` | Traduction (retour) | `t('welcome.title')` |
| `lang()` | Traduction (echo) | `lang('welcome.title')` |
| `app()` | Application active | `app()->get('package')` |
| `auth()` | Utilisateur connecté | `auth()` → `Auth::user()` |
| `user()` | Champ utilisateur | `user('email')` |
| `isLogin()` | Statut de connexion | `if (isLogin()) { … }` |
| `session()` | Session | `session('token')` |
| `runtime()` | Noyau HTTP actif | `runtime()->getRequest()` |
| `_env()` | Variable d'environnement | `_env('APP_DEBUG', false)` |
| `alias()` | Alias de flow/classe | `alias('auth')` |

Pour le HTML dans les contrôleurs, utilisez **`View::render()`** (comme les applications système). Le helper `view()` existe, mais préférez le Portal dans les contrôleurs.

---

## Request — injection ou `runtime()`

Il n'y a pas de helper global `request()` dans pincore. Dans les contrôleurs et les composants, utilisez l'injection par type-hint :

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

Dans un Flow ou ailleurs lorsque la signature ne permet pas l'injection :

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// Utilisateur courant (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) est identique à user($key)
$email = auth('email');

// Protéger les routes avec un alias de Flow
// app.php → 'alias' => ['auth' => AuthFlow::class]
// routes → ->flows(['auth']) ou un groupe avec flows
```

---

## View et Response

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
// Dans Twig : {{ t('product.title') }}
```

---

## URL et Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Helpers personnalisés d'application

Dans `app.php` :

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

## Helpers Twig (dans les templates)

En plus des helpers PHP, les éléments suivants sont disponibles dans Twig :

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## Conseils

- Utilisez `View::render()` dans les contrôleurs pour le HTML ; les helpers comme `url()`, `t()` et `config()` pour les tâches courantes
- Les helpers ne fonctionnent qu'après le bootstrap de Pinoox — ne les chargez pas dans des scripts PHP bruts en dehors de `index.php` / `pinoox`
- Pour la logique complexe, préférez `Component/` + Portal aux helpers personnalisés

---

## Documentation associée

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Langue](../basic/language.md)
- [Services](./services.md)

---

[← Retour à l'index](../README.md)

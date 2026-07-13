# Views

[← Back to index](../README.md)

In Pinoox 3.x HTML pages are rendered with **Twig** in the theme folder. The standard approach in controllers: **`View::render()`** from the Portal.

---

## Theme structure

```
apps/com_acme_shop/
├── app.php                 # 'theme' => 'default'
└── theme/default/
    ├── main.twig
    ├── layout.twig
    └── pages/
        └── home.twig
```

---

## Render in a controller (standard)

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'title' => 'Shop',
        'products' => ProductModel::latest()->take(6)->get(),
    ]);
}
```

Do not include the `.twig` extension; View resolves the file automatically.

The **`view()`** helper also exists and returns `View::ready()`, but prefer **`View::render()`** in controllers:

```php
// Helper equivalent — mainly for set/exists on the engine
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // rare
```

---

## String output (no Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

The **`render()`** helper calls `View::render()` directly.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Returns Twig content inside an HTTP `Response`.

---

## Global data for all views

```php
View::set('siteName', config('app.name'));
// or
view()->set('siteName', config('app.name'));
```

In Twig:

```twig
<title>{{ siteName }} — {{ title }}</title>
```

---

## SEO (Pinoox 3.x)

```php
View::shareSeo([
    'title' => 'Products',
    'description' => 'Shop product list',
    'canonical' => url('products'),
    'image' => assets('dist/og-cover.jpg'),
]);

return View::render('pages/products');
```

In `partials/head.twig`:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — shell with Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

See [Twig Templates](./templates.md) for details.

---

## Check if a view exists

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Switching theme context

For apps with more than one theme tree (site + panel, …):

```php
ThemeContext::activate('panel');
return View::render('pages/dashboard');

// or temporary:
return within_theme('panel', fn () => View::render('pages/dashboard'));
```

Prefer attaching `theme.panel` (via `theme_flow_aliases`) on route collections so every HTML route in that area uses the right stack. Full guide: [Theme contexts](./theme-contexts.md).

---

## Tips

- Keep business logic in Controller/Component; Twig is for presentation only
- The active theme comes from `app.php` → `'theme'` (or the active theme context)
- For pure JSON use `response()->json()` or `ApiController` — do not attach `theme.*` flows

---

## Related docs

- [Twig Templates](./templates.md)
- [Theme contexts](./theme-contexts.md)
- [Theme manifest (`theme.php`)](./theme-manifest.md)
- [URL and Assets](./url.md)
- [HTTP Response](./responses.md)
- [Portal](./portal.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)

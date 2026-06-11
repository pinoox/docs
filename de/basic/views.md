# Views

[← Zurück zum Index](../README.md)

In Pinoox 3.x werden HTML-Seiten mit **Twig** im Theme-Ordner gerendert. Der Standardansatz in Controllern: **`View::render()`** über das Portal.

---

## Theme-Struktur

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

## Rendern im Controller (Standard)

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

Die `.twig`-Erweiterung nicht angeben; View löst die Datei automatisch auf.

Der **`view()`**-Helper existiert ebenfalls und gibt `View::ready()` zurück, aber in Controllern **`View::render()`** bevorzugen:

```php
// Helper-Äquivalent — hauptsächlich für set/exists auf der Engine
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // selten
```

---

## String-Ausgabe (ohne Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// E-Mail, PDF, …
```

Der **`render()`**-Helper ruft `View::render()` direkt auf.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Gibt Twig-Inhalt in einer HTTP-`Response` zurück.

---

## Globale Daten für alle Views

```php
View::set('siteName', config('app.name'));
// oder
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

## SPA — Shell mit Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

Details siehe [Twig-Templates](./templates.md).

---

## Prüfen, ob eine View existiert

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Tipps

- Geschäftslogik in Controller/Component; Twig nur für Darstellung
- Das aktive Theme kommt aus `app.php` → `'theme'`
- Für reines JSON `response()->json()` oder `ApiController` verwenden

---

## Verwandte Dokumentation

- [Twig-Templates](./templates.md)
- [URL und Assets](./url.md)
- [HTTP-Response](./responses.md)
- [Portal](./portal.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zum Index](../README.md)

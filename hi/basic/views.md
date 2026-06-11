# Views

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में HTML pages theme folder में **Twig** से render होती हैं। Controllers में standard approach: Portal से **`View::render()`**।

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

## Controller में render (standard)

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

`.twig` extension include न करें; View file automatically resolve करता है।

**`view()`** helper भी exists करता है और `View::ready()` return करता है, लेकिन controllers में **`View::render()`** prefer करें:

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

**`render()`** helper सीधे `View::render()` call करता है।

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Twig content HTTP `Response` के अंदर return करता है।

---

## सभी views के लिए global data

```php
View::set('siteName', config('app.name'));
// or
view()->set('siteName', config('app.name'));
```

Twig में:

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

`partials/head.twig` में:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — Vite के साथ shell

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

विवरण के लिए [Twig Templates](./templates.md) देखें।

---

## View exists है या नहीं check करें

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Tips

- Business logic Controller/Component में रखें; Twig केवल presentation के लिए
- Active theme `app.php` → `'theme'` से आता है
- Pure JSON के लिए `response()->json()` या `ApiController` उपयोग करें

---

## संबंधित docs

- [Twig Templates](./templates.md)
- [URL and Assets](./url.md)
- [HTTP Response](./responses.md)
- [Portal](./portal.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

# View

[← Dizine dön](../README.md)

Pinoox 3.x'te HTML sayfaları tema klasöründe **Twig** ile render edilir. Controller'larda standart yaklaşım: Portal'dan **`View::render()`**.

---

## Tema yapısı

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

## Controller'da render (standart)

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

`.twig` uzantısını eklemeyin; View dosyayı otomatik çözümler.

**`view()`** helper'ı da vardır ve `View::ready()` döndürür, ancak controller'larda **`View::render()`** tercih edin:

```php
// Helper equivalent — mainly for set/exists on the engine
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // rare
```

---

## Dize çıktısı (Response yok)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

**`render()`** helper'ı doğrudan `View::render()` çağırır.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Twig içeriğini HTTP `Response` içinde döndürür.

---

## Tüm view'lar için global veri

```php
View::set('siteName', config('app.name'));
// or
view()->set('siteName', config('app.name'));
```

Twig'de:

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

`partials/head.twig` içinde:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — Vite ile kabuk

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

Ayrıntılar için bkz. [Twig şablonları](./templates.md).

---

## View'ın varlığını kontrol etme

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## İpuçları

- İş mantığını Controller/Component'te tutun; Twig yalnızca sunum içindir
- Aktif tema `app.php` → `'theme'`'den gelir
- Saf JSON için `response()->json()` veya `ApiController` kullanın

---

## İlgili dokümantasyon

- [Twig şablonları](./templates.md)
- [URL ve asset'ler](./url.md)
- [HTTP Response](./responses.md)
- [Portal](./portal.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

# Vistas

[← Volver al índice](../README.md)

En Pinoox 3.x las páginas HTML se renderizan con **Twig** en la carpeta del tema. El enfoque estándar en los controllers: **`View::render()`** desde el Portal.

---

## Estructura del tema

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

## Renderizar en un controller (estándar)

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

No incluyas la extensión `.twig`; View resuelve el archivo automáticamente.

El helper **`view()`** también existe y devuelve `View::ready()`, pero prefiere **`View::render()`** en los controllers:

```php
// Equivalente con el helper — principalmente para set/exists en el motor
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // poco común
```

---

## Salida como cadena (sin Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

El helper **`render()`** llama directamente a `View::render()`.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Devuelve el contenido Twig dentro de un `Response` HTTP.

---

## Datos globales para todas las vistas

```php
View::set('siteName', config('app.name'));
// o
view()->set('siteName', config('app.name'));
```

En Twig:

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

En `partials/head.twig`:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — shell con Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

Consulta [Plantillas Twig](./templates.md) para más detalles.

---

## Comprobar si existe una vista

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Consejos

- Mantén la lógica de negocio en Controller/Component; Twig es solo para la presentación
- El tema activo proviene de `app.php` → `'theme'`
- Para JSON puro usa `response()->json()` o `ApiController`

---

## Documentación relacionada

- [Plantillas Twig](./templates.md)
- [URL y assets](./url.md)
- [Response HTTP](./responses.md)
- [Portal](./portal.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)

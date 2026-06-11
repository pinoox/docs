# Views

[← Voltar ao índice](../README.md)

No Pinoox 3.x, páginas HTML são renderizadas com **Twig** na pasta do tema. A abordagem padrão nos controllers: **`View::render()`** do Portal.

---

## Estrutura do tema

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

## Renderizar em um controller (padrão)

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

Não inclua a extensão `.twig`; a View resolve o arquivo automaticamente.

O helper **`view()`** também existe e retorna `View::ready()`, mas prefira **`View::render()`** nos controllers:

```php
// Equivalente do helper — principalmente para set/exists no engine
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // raro
```

---

## Saída como string (sem Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// e-mail, PDF, …
```

O helper **`render()`** chama `View::render()` diretamente.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Retorna conteúdo Twig dentro de um `Response` HTTP.

---

## Dados globais para todas as views

```php
View::set('siteName', config('app.name'));
// ou
view()->set('siteName', config('app.name'));
```

No Twig:

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

Em `partials/head.twig`:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — shell com Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

Veja [Templates Twig](./templates.md) para detalhes.

---

## Verificar se uma view existe

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Dicas

- Mantenha lógica de negócio em Controller/Component; Twig é só apresentação
- O tema ativo vem de `app.php` → `'theme'`
- Para JSON puro use `response()->json()` ou `ApiController`

---

## Documentação relacionada

- [Templates Twig](./templates.md)
- [URL e assets](./url.md)
- [Resposta HTTP](./responses.md)
- [Portal](./portal.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

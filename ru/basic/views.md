# Views

[← Вернуться к оглавлению](../README.md)

В Pinoox 3.x HTML-страницы рендерятся с **Twig** в папке темы. Стандартный подход в контроллерах: **`View::render()`** из Portal.

---

## Структура темы

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

## Рендер в контроллере (стандарт)

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

Не указывайте расширение `.twig`; View автоматически разрешает файл.

Хелпер **`view()`** тоже существует и возвращает `View::ready()`, но в контроллерах предпочитайте **`View::render()`**:

```php
// Эквивалент хелпера — в основном для set/exists на движке
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // редко
```

---

## Вывод строки (без Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

Хелпер **`render()`** вызывает `View::render()` напрямую.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Возвращает содержимое Twig внутри HTTP `Response`.

---

## Глобальные данные для всех представлений

```php
View::set('siteName', config('app.name'));
// или
view()->set('siteName', config('app.name'));
```

В Twig:

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

В `partials/head.twig`:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — оболочка с Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

Подробности см. в [Шаблонах Twig](./templates.md).

---

## Проверка существования представления

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Советы

- Бизнес-логику держите в Controller/Component; Twig — только для представления
- Активная тема задаётся в `app.php` → `'theme'`
- Для чистого JSON используйте `response()->json()` или `ApiController`

---

## Связанные документы

- [Шаблоны Twig](./templates.md)
- [URL и Assets](./url.md)
- [HTTP Response](./responses.md)
- [Portal](./portal.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)

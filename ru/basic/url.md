# URL и построение ссылок

[← Вернуться к оглавлению](../README.md)

В Pinoox 3.x используйте **`url()`** для построения внутренних URL. Этот хелпер использует **`Url::link()`** и учитывает домен, путь установки (подпапку) и сегмент текущего приложения.

> Не используйте **`Url::get()`** или **`Url::app()`**. Вместо этого используйте **`url()`**, **`Url::link()`** и **`Url::forApp()`**.

---

## PHP — хелпер `url()`

```php
// Относительная ссылка внутри активного приложения
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// Аксессор без аргументов
$accessor = url();
echo $accessor->app;               // базовый URL приложения
echo $accessor->site;              // origin + путь проекта
echo $accessor->api;               // префикс API

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // то же, что url('products')
echo Url::forApp('com_acme_shop'); // базовый URL конкретного приложения
echo Url::current();               // URL текущей страницы
echo Url::origin();                // https://example.com/pinoox
```

Префикс `^` или `~` для ссылок вне базового пути приложения:

```php
echo url('^about');                // от корня проекта
echo Url::link('^config/app.php');
```

---

## Twig — аксессор `url()`

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| Метод аксессора | Назначение |
|-----------------|---------|
| `url().site` | origin + путь проекта |
| `url().app` | origin + сегмент приложения |
| `url().api` | префикс API (по умолчанию `api/v1/`) |
| `url().resource('resources/logo.png')` | статический файл в `apps/{package}/` |
| `url('profile')` | ссылка на маршрут внутри приложения |

---

## По имени маршрута — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Ассеты темы — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL файла в активной теме
```

---

## Пример меню в контроллере

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## Информация о запросе

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Советы

- Не «зашивайте» ссылки в код; всегда используйте `url()` или `Url::link()`
- Для файлов в `apps/{package}/resources/` используйте `url().resource()` или `asset()`; для файлов темы — **`assets()`**
- Базовый URL не задаётся вручную в конфигурации; он определяется из HTTP-запроса

---

## Связанные документы

- [Пути к файлам (Path)](./path.md)
- [Представления (Views)](./views.md)
- [Шаблоны Twig](./templates.md)
- [Роутер (Router)](./routers.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)

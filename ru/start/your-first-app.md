# Ваше первое приложение

[← Вернуться к оглавлению](../README.md)

Самый быстрый способ создать приложение в Pinoox 3.x — CLI-команда `app:create`. Она генерирует стандартную MVC-структуру в `apps/{package}/`: `routes/`, `Controller/`, `theme/`, `config/`.

---

## Создание приложения

Из корня проекта:

```bash
php pinoox app:create com_acme_blog
```

| Запрос CLI | Пример |
|------------|---------|
| Имя пакета | `com_acme_blog` (формат: `com_{vendor}_{name}`) |
| Отображаемое имя | `Blog` |
| URL-путь | `/blog` (опционально — регистрируется в `config/app-router.config.php`) |

Простой режим (только Twig, без мастера):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Сгенерированная структура

```
apps/com_acme_blog/
├── app.php
├── Controller/
│   └── MainController.php
├── routes/
│   ├── actions.php
│   └── web.php
├── Router/
│   └── Actions.php
├── theme/
│   └── default/
│       └── hello.twig
└── config/
```

---

## app.php — регистрация маршрутов

Манифест `app.php` перечисляет файлы маршрутов приложения:

```php
<?php

return [
    'package' => 'com_acme_blog',
    'name' => 'Blog',
    'enable' => true,
    'theme' => 'default',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Именованные действия (Named Actions) и маршруты

**actions.php** — определите обработчик:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — привяжите URL:

```php
<?php

use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\get;

get('/', '@' . Actions::HOME)->name('home');
```

---

## Controller

```php
<?php

namespace App\com_acme_blog\Controller;

use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class MainController extends Controller
{
    public function index()
    {
        return View::render('hello', [
            'title' => 'My first app',
        ]);
    }
}
```

Пространство имён: `App\{package}\Controller` — папка называется `Controller/` (не `Controllers/`).

---

## Регистрация URL приложения (на уровне проекта)

Если вы зарегистрировали `/blog` в мастере, запись добавляется в `config/app-router.config.php`:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Вручную или через CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## Просмотр в браузере

```
http://localhost/blog
```

---

## Полезные следующие команды

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## Связанные документы

- [Структура проекта](./structure.md)
- [Роутер (Router)](../basic/routers.md)
- [Контроллеры (Controllers)](../basic/controllers.md)
- [Руководство по Notes API](../examples/simple-api-app.md)
- [Руководство по веб-приложению «Телефонная книга»](../examples/phonebook-app.md)
- [Руководство по форме обратной связи](../examples/contact-form-app.md)
- [Руководство по простому блогу](../examples/blog-app.md)
- [Руководство по доске задач](../examples/task-board-app.md)
- [Руководство по галерее изображений](../examples/gallery-app.md)

---

[← Вернуться к оглавлению](../README.md)

# Your first app

[← Back to index](../README.md)

The fastest way to create an app in Pinoox 3.x is the CLI command `app:create`. It scaffolds the standard MVC structure under `apps/{package}/`: `routes/`, `Controller/`, `theme/`, `config/`.

---

## Create the app

From the project root:

```bash
php pinoox app:create com_acme_blog
```

| CLI prompt | Example |
|------------|---------|
| Package name | `com_acme_blog` (format: `com_{vendor}_{name}`) |
| Display name | `Blog` |
| URL path | `/blog` (optional — registered in `config/app-router.config.php`) |

Simple mode (Twig-only, no wizard):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Generated structure

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

## app.php — register routes

The `app.php` manifest lists the app's route files:

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

## Named Actions and routes

**actions.php** — define the handler:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — map the URL:

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

Namespace: `App\{package}\Controller` — folder is `Controller/` (not `Controllers/`).

---

## Register app URL (project level)

If you registered `/blog` during the wizard, an entry is added to `config/app-router.config.php`:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Manually or via CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## View in the browser

```
http://localhost/blog
```

---

## Useful next commands

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## Related docs

- [Project structure](./structure.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Notes API walkthrough](../examples/simple-api-app.md)
- [Phonebook web walkthrough](../examples/phonebook-app.md)
- [Contact form walkthrough](../examples/contact-form-app.md)
- [Simple blog walkthrough](../examples/blog-app.md)
- [Task board walkthrough](../examples/task-board-app.md)
- [Image gallery walkthrough](../examples/gallery-app.md)

---

[← Back to index](../README.md)

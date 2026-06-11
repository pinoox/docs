# Tu primera app

[← Volver al índice](../README.md)

La forma más rápida de crear una app en Pinoox 3.x es el comando de la CLI `app:create`. Genera la estructura MVC estándar bajo `apps/{package}/`: `routes/`, `Controller/`, `theme/`, `config/`.

---

## Crear la app

Desde la raíz del proyecto:

```bash
php pinoox app:create com_acme_blog
```

| Pregunta de la CLI | Ejemplo |
|------------|---------|
| Nombre del paquete | `com_acme_blog` (formato: `com_{vendor}_{name}`) |
| Nombre para mostrar | `Blog` |
| Ruta de URL | `/blog` (opcional — se registra en `config/app-router.config.php`) |

Modo simple (solo Twig, sin asistente):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Estructura generada

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

## app.php — registrar rutas

El manifiesto `app.php` lista los archivos de rutas de la app:

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

## Named Actions y rutas

**actions.php** — define el manejador:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — asigna la URL:

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

Namespace: `App\{package}\Controller` — la carpeta es `Controller/` (no `Controllers/`).

---

## Registrar la URL de la app (nivel de proyecto)

Si registraste `/blog` durante el asistente, se añade una entrada en `config/app-router.config.php`:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Manualmente o mediante la CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## Ver en el navegador

```
http://localhost/blog
```

---

## Siguientes comandos útiles

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## Documentación relacionada

- [Estructura del proyecto](./structure.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Tutorial de la API de notas](../examples/simple-api-app.md)
- [Tutorial web de la agenda telefónica](../examples/phonebook-app.md)
- [Tutorial del formulario de contacto](../examples/contact-form-app.md)
- [Tutorial del blog sencillo](../examples/blog-app.md)
- [Tutorial del tablero de tareas](../examples/task-board-app.md)
- [Tutorial de la galería de imágenes](../examples/gallery-app.md)

---

[← Volver al índice](../README.md)

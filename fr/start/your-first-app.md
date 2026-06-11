# Votre première app

[← Retour à l'index](../README.md)

La façon la plus rapide de créer une app dans Pinoox 3.x est la commande CLI `app:create`. Elle génère la structure MVC standard sous `apps/{package}/` : `routes/`, `Controller/`, `theme/`, `config/`.

---

## Créer l'app

Depuis la racine du projet :

```bash
php pinoox app:create com_acme_blog
```

| Invite CLI | Exemple |
|------------|---------|
| Nom du package | `com_acme_blog` (format : `com_{vendor}_{name}`) |
| Nom d'affichage | `Blog` |
| Chemin d'URL | `/blog` (optionnel — enregistré dans `config/app-router.config.php`) |

Mode simple (Twig uniquement, sans assistant) :

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Structure générée

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

## app.php — enregistrer les routes

Le manifeste `app.php` liste les fichiers de routes de l'app :

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

## Named Actions (actions nommées) et routes

**actions.php** — définit le gestionnaire :

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — associe l'URL :

```php
<?php

use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\get;

get('/', '@' . Actions::HOME)->name('home');
```

---

## Controller (contrôleur)

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

Namespace : `App\{package}\Controller` — le dossier est `Controller/` (et non `Controllers/`).

---

## Enregistrer l'URL de l'app (au niveau du projet)

Si vous avez enregistré `/blog` pendant l'assistant, une entrée est ajoutée dans `config/app-router.config.php` :

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Manuellement ou via la CLI :

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## Voir dans le navigateur

```
http://localhost/blog
```

---

## Commandes utiles pour la suite

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## Documentation associée

- [Structure du projet](./structure.md)
- [Router (routeur)](../basic/routers.md)
- [Controllers (contrôleurs)](../basic/controllers.md)
- [Tutoriel : API de notes](../examples/simple-api-app.md)
- [Tutoriel web : annuaire téléphonique](../examples/phonebook-app.md)
- [Tutoriel : formulaire de contact](../examples/contact-form-app.md)
- [Tutoriel : blog simple](../examples/blog-app.md)
- [Tutoriel : tableau de tâches](../examples/task-board-app.md)
- [Tutoriel : galerie d'images](../examples/gallery-app.md)

---

[← Retour à l'index](../README.md)

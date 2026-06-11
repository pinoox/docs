# Ihre erste App

[← Zurück zur Übersicht](../README.md)

Der schnellste Weg, eine App in Pinoox 3.x zu erstellen, ist der CLI-Befehl `app:create`. Er erzeugt die Standard-MVC-Struktur unter `apps/{package}/`: `routes/`, `Controller/`, `theme/`, `config/`.

---

## App erstellen

Aus dem Projektstammverzeichnis:

```bash
php pinoox app:create com_acme_blog
```

| CLI-Abfrage | Beispiel |
|------------|---------|
| Paketname | `com_acme_blog` (Format: `com_{vendor}_{name}`) |
| Anzeigename | `Blog` |
| URL-Pfad | `/blog` (optional — wird in `config/app-router.config.php` registriert) |

Einfacher Modus (nur Twig, ohne Assistent):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Generierte Struktur

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

## app.php — Routen registrieren

Das `app.php`-Manifest listet die Routendateien der App auf:

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

## Named Actions und Routen

**actions.php** — den Handler definieren:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — die URL zuordnen:

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

Namespace: `App\{package}\Controller` — der Ordner heißt `Controller/` (nicht `Controllers/`).

---

## App-URL registrieren (Projektebene)

Wenn Sie `/blog` während des Assistenten registriert haben, wird ein Eintrag in `config/app-router.config.php` hinzugefügt:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Manuell oder über die CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## Im Browser ansehen

```
http://localhost/blog
```

---

## Nützliche nächste Befehle

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## Verwandte Dokumente

- [Projektstruktur](./structure.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Notes-API-Anleitung](../examples/simple-api-app.md)
- [Telefonbuch-Web-Anleitung](../examples/phonebook-app.md)
- [Kontaktformular-Anleitung](../examples/contact-form-app.md)
- [Anleitung: Einfacher Blog](../examples/blog-app.md)
- [Anleitung: Aufgaben-Board](../examples/task-board-app.md)
- [Anleitung: Bildergalerie](../examples/gallery-app.md)

---

[← Zurück zur Übersicht](../README.md)

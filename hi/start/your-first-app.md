# आपका पहला ऐप

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में ऐप बनाने का सबसे तेज़ तरीका CLI कमांड `app:create` है। यह `apps/{package}/` के अंतर्गत मानक MVC संरचना स्कैफ़ोल्ड करता है: `routes/`, `Controller/`, `theme/`, `config/`।

---

## ऐप बनाएँ

प्रोजेक्ट रूट से:

```bash
php pinoox app:create com_acme_blog
```

| CLI प्रॉम्प्ट | उदाहरण |
|------------|---------|
| Package नाम | `com_acme_blog` (प्रारूप: `com_{vendor}_{name}`) |
| प्रदर्शित नाम | `Blog` |
| URL पाथ | `/blog` (वैकल्पिक — `config/app-router.config.php` में पंजीकृत) |

सरल मोड (केवल Twig, कोई विज़ार्ड नहीं):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## जनरेट की गई संरचना

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

## app.php — routes पंजीकृत करें

`app.php` मैनिफ़ेस्ट ऐप की route फ़ाइलों की सूची रखता है:

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

## Named Actions और routes

**actions.php** — हैंडलर परिभाषित करें:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — URL मैप करें:

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

Namespace: `App\{package}\Controller` — फ़ोल्डर `Controller/` है (`Controllers/` नहीं)।

---

## ऐप URL पंजीकृत करें (प्रोजेक्ट स्तर)

यदि आपने विज़ार्ड के दौरान `/blog` पंजीकृत किया था, तो `config/app-router.config.php` में एक एंट्री जोड़ी जाती है:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

मैन्युअल रूप से या CLI के माध्यम से:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## ब्राउज़र में देखें

```
http://localhost/blog
```

---

## उपयोगी अगले कमांड

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## संबंधित दस्तावेज़

- [प्रोजेक्ट संरचना](./structure.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Notes API वॉकथ्रू](../examples/simple-api-app.md)
- [Phonebook वेब वॉकथ्रू](../examples/phonebook-app.md)
- [Contact form वॉकथ्रू](../examples/contact-form-app.md)
- [सरल ब्लॉग वॉकथ्रू](../examples/blog-app.md)
- [टास्क बोर्ड वॉकथ्रू](../examples/task-board-app.md)
- [इमेज गैलरी वॉकथ्रू](../examples/gallery-app.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

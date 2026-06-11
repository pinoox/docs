# İlk uygulamanız

[← Dizine dön](../README.md)

Pinoox 3.x'te uygulama oluşturmanın en hızlı yolu CLI komutu `app:create`'dir. `apps/{package}/` altında standart MVC yapısını iskelet olarak oluşturur: `routes/`, `Controller/`, `theme/`, `config/`.

---

## Uygulamayı oluşturun

Proje kökünden:

```bash
php pinoox app:create com_acme_blog
```

| CLI istemi | Örnek |
|------------|---------|
| Paket adı | `com_acme_blog` (format: `com_{vendor}_{name}`) |
| Görünen ad | `Blog` |
| URL yolu | `/blog` (isteğe bağlı — `config/app-router.config.php` içinde kayıtlı) |

Basit mod (yalnızca Twig, sihirbaz yok):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Oluşturulan yapı

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

## app.php — route'ları kaydedin

`app.php` manifest'i uygulamanın route dosyalarını listeler:

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

## Named Action'lar ve route'lar

**actions.php** — handler'ı tanımlayın:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — URL'yi eşleyin:

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

Namespace: `App\{package}\Controller` — klasör `Controller/` ( `Controllers/` değil).

---

## Uygulama URL'sini kaydedin (proje düzeyinde)

Sihirbaz sırasında `/blog` kaydettiyseniz `config/app-router.config.php` dosyasına bir giriş eklenir:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Manuel olarak veya CLI üzerinden:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## Tarayıcıda görüntüleyin

```
http://localhost/blog
```

---

## Faydalı sonraki komutlar

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## İlgili dokümantasyon

- [Proje yapısı](./structure.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Notes API uygulamalı rehber](../examples/simple-api-app.md)
- [Telefon rehberi web uygulamalı rehber](../examples/phonebook-app.md)
- [İletişim formu uygulamalı rehber](../examples/contact-form-app.md)
- [Basit blog uygulamalı rehber](../examples/blog-app.md)
- [Görev panosu uygulamalı rehber](../examples/task-board-app.md)
- [Resim galerisi uygulamalı rehber](../examples/gallery-app.md)

---

[← Dizine dön](../README.md)

# ساخت اولین اپلیکیشن

[← بازگشت به فهرست](../../readme-fa.md)

سریع‌ترین راه ساخت اپ در پینوکس ۳.x، دستور CLI `app:create` است. این دستور ساختار استاندارد MVC را در `apps/{package}/` می‌سازد: `routes/`، `Controller/`، `theme/`، `config/`.

---

## ساخت اپ

از ریشه پروژه:

```bash
php pinoox app:create com_acme_blog
```

| ورودی CLI | مثال |
|-----------|------|
| نام پکیج | `com_acme_blog` (فرمت: `com_{vendor}_{name}`) |
| نام نمایشی | `Blog` |
| مسیر URL | `/blog` (اختیاری — در `config/app-router.config.php` ثبت می‌شود) |

حالت ساده (فقط Twig، بدون wizard):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## ساختار تولیدشده

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

## app.php — ثبت مسیرها

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

## Named Action و Route

**actions.php** — تعریف handler:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — اتصال URL:

```php
<?php

use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\get;

get('/', '@' . Actions::HOME)->name('home');
```

---

## کنترلر

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
            'title' => 'اولین اپ من',
        ]);
    }
}
```

Namespace: `App\{package}\Controller` — پوشه `Controller/` (نه `Controllers/`).

---

## ثبت URL اپ (سطح پروژه)

اگر در wizard مسیر `/blog` را ثبت کردید، ورودی در `config/app-router.config.php` اضافه می‌شود:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

دستی یا با CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## مشاهده در مرورگر

```
http://localhost/blog
```

---

## دستورات مفید بعدی

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## مستندات مرتبط

- [ساختار پوشه‌بندی](./structure.md)
- [روتر](../basic/routers.md)
- [کنترلر](../basic/controllers.md)
- [نمونه API یادداشت](../examples/simple-api-app.md)
- [نمونه دفترچه تلفن](../examples/phonebook-app.md)
- [نمونه فرم تماس](../examples/contact-form-app.md)
- [نمونه وبلاگ](../examples/blog-app.md)
- [نمونه تابلوی کار](../examples/task-board-app.md)
- [نمونه گالری تصاویر](../examples/gallery-app.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

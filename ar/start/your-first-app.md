# تطبيقك الأول

[← العودة إلى الفهرس](../README.md)

أسرع طريقة لإنشاء تطبيق في Pinoox 3.x هي أمر CLI‏ `app:create`. فهو يولّد بنية MVC القياسية ضمن `apps/{package}/`: المجلدات `routes/` و `Controller/` و `theme/` و `config/`.

---

## إنشاء التطبيق

من جذر المشروع:

```bash
php pinoox app:create com_acme_blog
```

| سؤال CLI | مثال |
|------------|---------|
| اسم الحزمة (Package) | `com_acme_blog` (الصيغة: `com_{vendor}_{name}`) |
| اسم العرض | `Blog` |
| مسار URL | `/blog` (اختياري — يُسجَّل في `config/app-router.config.php`) |

الوضع البسيط (Twig فقط، بدون معالج):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## البنية المولّدة

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

## app.php — تسجيل المسارات

يسرد ملف البيان `app.php` ملفات المسارات (Routes) الخاصة بالتطبيق:

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

## الإجراءات المسماة (Named Actions) والمسارات

**actions.php** — تعريف المعالج:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — ربط الـ URL:

```php
<?php

use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\get;

get('/', '@' . Actions::HOME)->name('home');
```

---

## المتحكم (Controller)

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

مساحة الأسماء: `App\{package}\Controller` — المجلد هو `Controller/` (وليس `Controllers/`).

---

## تسجيل URL التطبيق (على مستوى المشروع)

إذا سجّلت `/blog` أثناء المعالج، يُضاف إدخال في `config/app-router.config.php`:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

يدويًا أو عبر CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## العرض في المتصفح

```
http://localhost/blog
```

---

## أوامر تالية مفيدة

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## وثائق ذات صلة

- [بنية المشروع](./structure.md)
- [الموجّه (Router)](../basic/routers.md)
- [المتحكمات (Controllers)](../basic/controllers.md)
- [دليل تطبيق Notes API](../examples/simple-api-app.md)
- [دليل تطبيق ويب دفتر الهاتف](../examples/phonebook-app.md)
- [دليل تطبيق نموذج التواصل](../examples/contact-form-app.md)
- [دليل تطبيق المدونة البسيطة](../examples/blog-app.md)
- [دليل لوحة المهام](../examples/task-board-app.md)
- [دليل معرض الصور](../examples/gallery-app.md)

---

[← العودة إلى الفهرس](../README.md)

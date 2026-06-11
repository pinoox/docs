# 첫 번째 앱 만들기

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 앱을 만드는 가장 빠른 방법은 CLI command `app:create`입니다. `apps/{package}/` 아래 표준 MVC 구조를 스캐폴딩합니다: `routes/`, `Controller/`, `theme/`, `config/`.

---

## 앱 생성

프로젝트 루트에서:

```bash
php pinoox app:create com_acme_blog
```

| CLI prompt | 예시 |
|------------|---------|
| Package name | `com_acme_blog` (형식: `com_{vendor}_{name}`) |
| Display name | `Blog` |
| URL path | `/blog` (선택 — `config/app-router.config.php`에 등록) |

Simple mode (Twig만, wizard 없음):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## 생성된 구조

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

## app.php — route 등록

`app.php` 매니페스트는 앱의 route 파일을 나열합니다:

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

## Named Actions와 route

**actions.php** — handler 정의:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — URL 매핑:

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

Namespace: `App\{package}\Controller` — 폴더는 `Controller/` (`Controllers/` 아님).

---

## 앱 URL 등록 (프로젝트 수준)

wizard에서 `/blog`를 등록했다면 `config/app-router.config.php`에 항목이 추가됩니다:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

수동 또는 CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## 브라우저에서 View

```
http://localhost/blog
```

---

## 유용한 다음 command

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## 관련 문서

- [프로젝트 구조](./structure.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Notes API 워크스루](../examples/simple-api-app.md)
- [Phonebook 웹 워크스루](../examples/phonebook-app.md)
- [Contact form 워크스루](../examples/contact-form-app.md)
- [Simple blog 워크스루](../examples/blog-app.md)
- [Task board 워크스루](../examples/task-board-app.md)
- [Image gallery 워크스루](../examples/gallery-app.md)

---

[← 색인으로 돌아가기](../README.md)

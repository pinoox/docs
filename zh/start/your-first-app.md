# 你的第一个应用

[← 返回索引](../README.md)

在 Pinoox 3.x 中创建应用最快的方式是 CLI 命令 `app:create`。它会在 `apps/{package}/` 下生成标准的 MVC 结构：`routes/`、`Controller/`、`theme/`、`config/`。

---

## 创建应用

在项目根目录下执行：

```bash
php pinoox app:create com_acme_blog
```

| CLI 提示项 | 示例 |
|------------|---------|
| 包名 | `com_acme_blog`（格式：`com_{vendor}_{name}`） |
| 显示名称 | `Blog` |
| URL 路径 | `/blog`（可选 — 注册在 `config/app-router.config.php` 中） |

简单模式（仅 Twig，不使用向导）：

```bash
php pinoox app:create com_acme_blog --simple
```

---

## 生成的结构

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

## app.php — 注册路由

`app.php` 清单列出了应用的路由文件：

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

## 命名 Action 与路由

**actions.php** — 定义处理器：

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — 映射 URL：

```php
<?php

use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\get;

get('/', '@' . Actions::HOME)->name('home');
```

---

## 控制器（Controller）

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

命名空间：`App\{package}\Controller` — 文件夹是 `Controller/`（不是 `Controllers/`）。

---

## 注册应用 URL（项目级）

如果你在向导中注册了 `/blog`，则会在 `config/app-router.config.php` 中添加一条记录：

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

也可以手动添加，或通过 CLI：

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## 在浏览器中查看

```
http://localhost/blog
```

---

## 后续常用命令

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## 相关文档

- [项目结构](./structure.md)
- [路由（Router）](../basic/routers.md)
- [控制器（Controllers）](../basic/controllers.md)
- [笔记 API 实战演练](../examples/simple-api-app.md)
- [电话簿 Web 实战演练](../examples/phonebook-app.md)
- [联系表单实战演练](../examples/contact-form-app.md)
- [简易博客实战演练](../examples/blog-app.md)
- [任务看板实战演练](../examples/task-board-app.md)
- [图片画廊实战演练](../examples/gallery-app.md)

---

[← 返回索引](../README.md)

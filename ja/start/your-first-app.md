# 最初のアプリ

[← 索引に戻る](../README.md)

Pinoox 3.x でアプリを作成する最速の方法は、CLI コマンド `app:create` です。`apps/{package}/` 配下に標準的な MVC 構造（`routes/`、`Controller/`、`theme/`、`config/`）をスキャフォールディングします。

---

## アプリを作成

プロジェクトルートから:

```bash
php pinoox app:create com_acme_blog
```

| CLI プロンプト | 例 |
|------------|---------|
| パッケージ名 | `com_acme_blog`（形式: `com_{vendor}_{name}`） |
| 表示名 | `Blog` |
| URL パス | `/blog`（任意 — `config/app-router.config.php` に登録） |

シンプルモード（Twig のみ、ウィザードなし）:

```bash
php pinoox app:create com_acme_blog --simple
```

---

## 生成される構造

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

## app.php — ルートの登録

`app.php` マニフェストには、アプリのルートファイルが一覧表示されます。

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

## Named Actions とルート

**actions.php** — ハンドラーを定義:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — URL をマッピング:

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

名前空間: `App\{package}\Controller` — フォルダは `Controller/`（`Controllers/` ではない）。

---

## アプリ URL の登録（プロジェクトレベル）

ウィザードで `/blog` を登録した場合、`config/app-router.config.php` にエントリが追加されます。

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

手動または CLI 経由:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## ブラウザで表示

```
http://localhost/blog
```

---

## 便利な次のコマンド

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## 関連ドキュメント

- [プロジェクト構造](./structure.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Notes API ウォークスルー](../examples/simple-api-app.md)
- [電話帳 Web ウォークスルー](../examples/phonebook-app.md)
- [お問い合わせフォーム ウォークスルー](../examples/contact-form-app.md)
- [シンプルなブログ ウォークスルー](../examples/blog-app.md)
- [タスクボード ウォークスルー](../examples/task-board-app.md)
- [画像ギャラリー ウォークスルー](../examples/gallery-app.md)

---

[← 索引に戻る](../README.md)

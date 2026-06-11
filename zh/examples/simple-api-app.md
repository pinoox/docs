# 实战演练：笔记 API 应用

[← 返回索引](../README.md)

本指南构建一个小型应用，为笔记（标题 + 正文）提供 **JSON API**。假设你已安装 Pinoox，并且可以在项目根目录运行 `php pinoox`。

**包名（Package）：** `com_acme_notes`  
**应用 URL：** `/notes`  
**示例端点：** `GET /notes/api/v1/notes`  
**完整源码：** [docs/source/simple-api-app/](../../source/simple-api-app/) — 复制到 `apps/`

---

## 前置条件

- [安装 Pinoox](../start/installing-pinoox.md)
- [你的第一个应用](../start/your-first-app.md) — 目录结构基础

---

## 第 1 步 — 创建应用

```bash
php pinoox app:create com_acme_notes --simple
```

在向导中，将显示名称设为 `Notes`、路径设为 `/notes`（也可以稍后通过 CLI 注册路径）。

---

## 第 2 步 — 注册应用 URL

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## 第 3 步 — 迁移（Migration）

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

编辑生成的文件：

```php
<?php
namespace App\com_acme_notes\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('notes', 'com_acme_notes'), function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('notes', 'com_acme_notes'));
    }
};
```

运行迁移：

```bash
php pinoox migrate com_acme_notes
```

---

## 第 4 步 — 模型（Model）

```bash
php pinoox model:create Note com_acme_notes
```

```php
<?php
namespace App\com_acme_notes\Model;

use Pinoox\Component\Database\Model;

class NoteModel extends Model
{
    protected $table = 'notes';

    protected $fillable = ['title', 'body'];
}
```

---

## 第 5 步 — API 控制器（Controller）

```bash
php pinoox controller:create NoteApiController com_acme_notes
```

```php
<?php
namespace App\com_acme_notes\Controller;

use App\com_acme_notes\Model\NoteModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class NoteApiController extends ApiController
{
    public function index()
    {
        $notes = NoteModel::orderByDesc('id')->get();

        return $this->ok($notes);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:200',
            'body' => 'nullable|string',
        ]);

        $note = NoteModel::create($data);

        return $this->ok($note, 'Note saved.', status: 201);
    }

    public function show(Request $request, int $id)
    {
        $note = NoteModel::find($id);

        if (!$note) {
            return $this->fail('NOT_FOUND', 'Note not found.', status: 404);
        }

        return $this->ok($note);
    }

    public function destroy(Request $request, int $id)
    {
        $note = NoteModel::find($id);

        if (!$note) {
            return $this->fail('NOT_FOUND', 'Note not found.', status: 404);
        }

        $note->delete();

        return $this->ok(null, 'Deleted.');
    }
}
```

JSON 响应使用 Pinoox 的标准信封格式：`{ success, data, message, meta }`。

---

## 第 6 步 — `routes/api.php`

```php
<?php

use App\com_acme_notes\Controller\NoteApiController;
use function Pinoox\Router\{collect, get, post, delete, routes};

return routes([
    'version' => 'v1',
    'prefix' => '',
    'routes' => collect(function () {
        get('/notes', [NoteApiController::class, 'index'])->name('notes.index');
        post('/notes', [NoteApiController::class, 'store'])->name('notes.store');
        get('/notes/{id}', [NoteApiController::class, 'show'])->name('notes.show');
        delete('/notes/{id}', [NoteApiController::class, 'destroy'])->name('notes.destroy');
    }),
]);
```

---

## 第 7 步 — 在 `app.php` 中注册 `api.php`

```php
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## 第 8 步 — 使用 curl 测试

如果项目位于 `http://localhost/pinoox`：

```bash
# 列表
curl -s http://localhost/pinoox/notes/api/v1/notes

# 创建
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# 单条记录
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# 删除
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **什么是命名 Action（Named Action）？** 在 API 路由中，你通常直接指向 `[Controller::class, 'method']`。命名 Action（`@home`）更适合用于 `web.php` 中的 HTML 页面 — 参见 [路由（Routers）](../basic/routers.md)。

---

## 第 9 步 —（可选）带 CSS 的 HTML 页面

要在浏览器中查看笔记，添加一个使用内联 CSS 的简单 Twig 页面：

在 `routes/web.php` 中：

```php
get('/browse', [MainController::class, 'browse'])->name('browse');
```

`Controller/MainController.php`：

```php
public function browse()
{
    $notes = NoteModel::orderByDesc('id')->get();

    return View::render('pages/browse.twig', [
        'title' => 'Notes',
        'notes' => $notes,
    ]);
}
```

`theme/default/pages/browse.twig`：

```twig
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; color: #222; }
        h1 { font-size: 1.35rem; margin: 0 0 1rem; }
        ul { padding-left: 1.2rem; }
        li { margin-bottom: .75rem; padding-bottom: .75rem; border-bottom: 1px solid #ddd; }
        li strong { display: block; margin-bottom: .25rem; }
        .meta { color: #666; font-size: .85rem; }
        .empty { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <h1>{{ title }}</h1>
    {% if notes is empty %}
        <p class="empty">No notes yet — create one with curl or Postman.</p>
    {% else %}
        <ul>
            {% for note in notes %}
            <li>
                <strong>{{ note.title }}</strong>
                {% if note.body %}<span>{{ note.body }}</span>{% endif %}
                <div class="meta">#{{ note.id }}</div>
            </li>
            {% endfor %}
        </ul>
    {% endif %}
</body>
</html>
```

URL：`http://localhost/pinoox/notes/browse` — API 仍位于 `/api/v1/notes`。

---

## 后续步骤

- [验证（Validation）](../basic/validation.md)
- [API 资源（API resources）](../eloquent-orm/api-resources.md)
- [HTTP 测试](../test/http-tests.md)

---

[← 返回索引](../README.md)

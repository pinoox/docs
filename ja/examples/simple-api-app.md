# ウォークスルー: Notes API アプリ

[← 索引に戻る](../README.md)

このガイドでは、メモ（title + body）向け **JSON API** を公開する小さなアプリを構築します。Pinoox がインストール済みで、プロジェクトルートから `php pinoox` が動作することを前提とします。

**Package:** `com_acme_notes`  
**App URL:** `/notes`  
**サンプルエンドポイント:** `GET /notes/api/v1/notes`  
**完全なソース:** [docs/source/simple-api-app/](../../source/simple-api-app/) — `apps/` にコピー

---

## 前提条件

- [Pinoox のインストール](../start/installing-pinoox.md)
- [最初のアプリ](../start/your-first-app.md) — フォルダ構造の基本

---

## ステップ 1 — アプリを作成

```bash
php pinoox app:create com_acme_notes --simple
```

ウィザードで表示名 `Notes` とパス `/notes` を設定（または後で CLI でパスを登録）。

---

## ステップ 2 — アプリ URL を登録

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## ステップ 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

生成されたファイルを編集:

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

Migration を実行:

```bash
php pinoox migrate com_acme_notes
```

---

## ステップ 4 — Model

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

## ステップ 5 — API Controller

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

JSON レスポンスは Pinoox の標準エンベロープ `{ success, data, message, meta }` を使用します。

---

## ステップ 6 — `routes/api.php`

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

## ステップ 7 — `app.php` に `api.php` を登録

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

## ステップ 8 — curl でテスト

プロジェクトが `http://localhost/pinoox` にある場合:

```bash
# 一覧
curl -s http://localhost/pinoox/notes/api/v1/notes

# 作成
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# 1 件
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# 削除
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **Named Action とは？** API ルートでは通常 `[Controller::class, 'method']` を直接指定します。Named Actions（`@home`）は `web.php` の HTML ページでより有用 — [Router](../basic/routers.md) を参照。

---

## ステップ 9 — （任意）CSS 付き HTML ページ

ブラウザでメモを表示するには、インライン CSS 付きのシンプルな Twig ページを追加:

`routes/web.php` 内:

```php
get('/browse', [MainController::class, 'browse'])->name('browse');
```

`Controller/MainController.php`:

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

`theme/default/pages/browse.twig`:

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

URL: `http://localhost/pinoox/notes/browse` — API は `/api/v1/notes` のまま。

---

## 次のステップ

- [Validation](../basic/validation.md)
- [API Resources](../eloquent-orm/api-resources.md)
- [HTTP テスト](../test/http-tests.md)

---

[← 索引に戻る](../README.md)

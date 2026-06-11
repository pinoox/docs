# 워크스루: Notes API 앱

[← 색인으로 돌아가기](../README.md)

이 가이드는 note(title + body)용 **JSON API**를 제공하는 작은 앱을 만듭니다. Pinoox가 설치되어 있고 프로젝트 루트에서 `php pinoox`가 동작한다고 가정합니다.

**Package:** `com_acme_notes`  
**App URL:** `/notes`  
**Sample endpoint:** `GET /notes/api/v1/notes`  
**Full source:** [docs/source/simple-api-app/](../../source/simple-api-app/) — copy to `apps/`

---

## 사전 요구 사항

- [Pinoox 설치](../start/installing-pinoox.md)
- [첫 번째 앱 만들기](../start/your-first-app.md) — 폴더 구조 기본

---

## 단계 1 — 앱 생성

```bash
php pinoox app:create com_acme_notes --simple
```

wizard에서 display name `Notes`, path `/notes` 설정 (또는 나중에 CLI로 path 등록).

---

## 단계 2 — 앱 URL 등록

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## 단계 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

생성된 file 편집:

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

Migration 실행:

```bash
php pinoox migrate com_acme_notes
```

---

## 단계 4 — Model

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

## 단계 5 — API Controller

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

JSON response는 Pinoox 표준 envelope 사용: `{ success, data, message, meta }`.

---

## 단계 6 — `routes/api.php`

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

## 단계 7 — `app.php`에 `api.php` 등록

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

## 단계 8 — curl로 테스트

프로젝트가 `http://localhost/pinoox`에 있으면:

```bash
# List
curl -s http://localhost/pinoox/notes/api/v1/notes

# Create
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# One record
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# Delete
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **Named Action이란?** API route에서는 보통 `[Controller::class, 'method']`를 직접 지정합니다. Named Action(`@home`)은 `web.php`의 HTML page에 더 유용 — [Router](../basic/routers.md) 참조.

---

## 단계 9 — (선택) CSS가 있는 HTML page

브라우저에서 note를 보려면 inline CSS가 있는 simple Twig page 추가:

`routes/web.php`에서:

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

URL: `http://localhost/pinoox/notes/browse` — API는 `/api/v1/notes`에 그대로.

---

## 다음 단계

- [Validation](../basic/validation.md)
- [API resources](../eloquent-orm/api-resources.md)
- [HTTP tests](../test/http-tests.md)

---

[← 색인으로 돌아가기](../README.md)

# Walkthrough: Notes API app

[← Back to index](../../readme.md)

This guide builds a small app that exposes a **JSON API** for notes (title + body). Assumes Pinoox is installed and `php pinoox` works from the project root.

**Package:** `com_acme_notes`  
**App URL:** `/notes`  
**Sample endpoint:** `GET /notes/api/v1/notes`  
**Full source:** [docs/source/simple-api-app/](../../source/simple-api-app/) — copy to `apps/`

---

## Prerequisites

- [Installing Pinoox](../start/installing-pinoox.md)
- [Your first app](../start/your-first-app.md) — folder structure basics

---

## Step 1 — Create the app

```bash
php pinoox app:create com_acme_notes --simple
```

In the wizard, set display name `Notes` and path `/notes` (or register the path later with CLI).

---

## Step 2 — Register the app URL

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Step 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Edit the generated file:

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

Run migrations:

```bash
php pinoox migrate com_acme_notes
```

---

## Step 4 — Model

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

## Step 5 — API controller

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

JSON responses use Pinoox’s standard envelope: `{ success, data, message, meta }`.

---

## Step 6 — `routes/api.php`

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

## Step 7 — Register `api.php` in `app.php`

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

## Step 8 — Test with curl

If the project lives at `http://localhost/pinoox`:

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

> **What is a Named Action?** In API routes you usually point directly to `[Controller::class, 'method']`. Named Actions (`@home`) are more useful for HTML pages in `web.php` — see [Routers](../basic/routers.md).

---

## Step 9 — (Optional) HTML page with CSS

To view notes in the browser, add a simple Twig page with inline CSS:

In `routes/web.php`:

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

URL: `http://localhost/pinoox/notes/browse` — the API remains at `/api/v1/notes`.

---

## Next steps

- [Validation](../basic/validation.md)
- [API resources](../eloquent-orm/api-resources.md)
- [HTTP tests](../test/http-tests.md)

---

[← Back to index](../../readme.md)

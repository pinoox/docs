# Walkthrough: Notes-API-App

[← Zurück zum Index](../README.md)

Diese Anleitung baut eine kleine App, die eine **JSON-API** für Notizen (Titel + Inhalt) bereitstellt. Voraussetzung: Pinoox ist installiert und `php pinoox` funktioniert vom Projektroot.

**Package:** `com_acme_notes`  
**App-URL:** `/notes`  
**Beispiel-Endpunkt:** `GET /notes/api/v1/notes`  
**Vollständiger Quellcode:** [docs/source/simple-api-app/](../../source/simple-api-app/) — nach `apps/` kopieren

---

## Voraussetzungen

- [Pinoox installieren](../start/installing-pinoox.md)
- [Ihre erste App](../start/your-first-app.md) — Grundlagen der Ordnerstruktur

---

## Schritt 1 — App erstellen

```bash
php pinoox app:create com_acme_notes --simple
```

Im Wizard Anzeigename `Notes` und Pfad `/notes` setzen (oder den Pfad später per CLI registrieren).

---

## Schritt 2 — App-URL registrieren

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Schritt 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Generierte Datei bearbeiten:

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

Migrationen ausführen:

```bash
php pinoox migrate com_acme_notes
```

---

## Schritt 4 — Model

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

## Schritt 5 — API-Controller

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

JSON-Responses verwenden Pinoox’ Standard-Envelope: `{ success, data, message, meta }`.

---

## Schritt 6 — `routes/api.php`

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

## Schritt 7 — `api.php` in `app.php` registrieren

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

## Schritt 8 — Mit curl testen

Wenn das Projekt unter `http://localhost/pinoox` liegt:

```bash
# Liste
curl -s http://localhost/pinoox/notes/api/v1/notes

# Erstellen
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# Ein Datensatz
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# Löschen
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **Was ist eine Named Action?** In API-Routen zeigen Sie meist direkt auf `[Controller::class, 'method']`. Named Actions (`@home`) sind eher für HTML-Seiten in `web.php` nützlich — siehe [Router](../basic/routers.md).

---

## Schritt 9 — (Optional) HTML-Seite mit CSS

Um Notizen im Browser anzuzeigen, eine einfache Twig-Seite mit Inline-CSS hinzufügen:

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

URL: `http://localhost/pinoox/notes/browse` — die API bleibt unter `/api/v1/notes`.

---

## Nächste Schritte

- [Validierung](../basic/validation.md)
- [API-Ressourcen](../eloquent-orm/api-resources.md)
- [HTTP-Tests](../test/http-tests.md)

---

[← Zurück zum Index](../README.md)

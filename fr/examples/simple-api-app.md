# Guide pas à pas : app API Notes

[← Retour à l'index](../README.md)

Ce guide construit une petite app qui expose une **API JSON** pour des notes (titre + corps). Suppose que Pinoox est installé et que `php pinoox` fonctionne depuis la racine du projet.

**Paquet :** `com_acme_notes`  
**URL de l'app :** `/notes`  
**Endpoint d'exemple :** `GET /notes/api/v1/notes`  
**Source complète :** [docs/source/simple-api-app/](../../source/simple-api-app/) — copier vers `apps/`

---

## Prérequis

- [Installer Pinoox](../start/installing-pinoox.md)
- [Votre première app](../start/your-first-app.md) — bases de la structure des dossiers

---

## Étape 1 — Créer l'app

```bash
php pinoox app:create com_acme_notes --simple
```

Dans l'assistant, définissez le nom d'affichage `Notes` et le chemin `/notes` (ou enregistrez le chemin plus tard avec la CLI).

---

## Étape 2 — Enregistrer l'URL de l'app

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Étape 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Modifiez le fichier généré :

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

Exécutez les migrations :

```bash
php pinoox migrate com_acme_notes
```

---

## Étape 4 — Modèle

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

## Étape 5 — Contrôleur API

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

Les réponses JSON utilisent l'enveloppe standard Pinoox : `{ success, data, message, meta }`.

---

## Étape 6 — `routes/api.php`

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

## Étape 7 — Enregistrer `api.php` dans `app.php`

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

## Étape 8 — Tester avec curl

Si le projet est à `http://localhost/pinoox` :

```bash
# Liste
curl -s http://localhost/pinoox/notes/api/v1/notes

# Créer
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# Un enregistrement
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# Supprimer
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **Qu'est-ce qu'une Named Action ?** Dans les routes API, vous pointez généralement directement vers `[Controller::class, 'method']`. Les Named Actions (`@home`) sont plus utiles pour les pages HTML dans `web.php` — voir [Router](../basic/routers.md).

---

## Étape 9 — (Optionnel) Page HTML avec CSS

Pour afficher les notes dans le navigateur, ajoutez une page Twig simple avec CSS inline :

Dans `routes/web.php` :

```php
get('/browse', [MainController::class, 'browse'])->name('browse');
```

`Controller/MainController.php` :

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

`theme/default/pages/browse.twig` :

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

URL : `http://localhost/pinoox/notes/browse` — l'API reste à `/api/v1/notes`.

---

## Prochaines étapes

- [Validation](../basic/validation.md)
- [Ressources API](../eloquent-orm/api-resources.md)
- [Tests HTTP](../test/http-tests.md)

---

[← Retour à l'index](../README.md)

# Tutorial: aplicación de API de notas

[← Volver al índice](../README.md)

Esta guía construye una app pequeña que expone una **API JSON** para notas (título + cuerpo). Supone que Pinoox está instalado y que `php pinoox` funciona desde la raíz del proyecto.

**Package:** `com_acme_notes`  
**URL de la app:** `/notes`  
**Endpoint de ejemplo:** `GET /notes/api/v1/notes`  
**Código fuente completo:** [docs/source/simple-api-app/](../../source/simple-api-app/) — copiar a `apps/`

---

## Requisitos previos

- [Installing Pinoox](../start/installing-pinoox.md)
- [Your first app](../start/your-first-app.md) — conceptos básicos de estructura de carpetas

---

## Paso 1 — Crear la app

```bash
php pinoox app:create com_acme_notes --simple
```

En el asistente, establece el nombre visible `Notes` y la ruta `/notes` (o registra la ruta más tarde con la CLI).

---

## Paso 2 — Registrar la URL de la app

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Paso 3 — Migración

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Edita el archivo generado:

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

Ejecuta las migraciones:

```bash
php pinoox migrate com_acme_notes
```

---

## Paso 4 — Modelo

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

## Paso 5 — Controller de API

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

Las respuestas JSON usan el sobre estándar de Pinoox: `{ success, data, message, meta }`.

---

## Paso 6 — `routes/api.php`

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

## Paso 7 — Registrar `api.php` en `app.php`

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

## Paso 8 — Probar con curl

Si el proyecto está en `http://localhost/pinoox`:

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

> **¿Qué es una Named Action?** En las rutas de API sueles apuntar directamente a `[Controller::class, 'method']`. Las Named Actions (`@home`) son más útiles para páginas HTML en `web.php` — consulta [Routers](../basic/routers.md).

---

## Paso 9 — (Opcional) Página HTML con CSS

Para ver las notas en el navegador, añade una página Twig simple con CSS inline:

En `routes/web.php`:

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

URL: `http://localhost/pinoox/notes/browse` — la API sigue en `/api/v1/notes`.

---

## Próximos pasos

- [Validation](../basic/validation.md)
- [API resources](../eloquent-orm/api-resources.md)
- [HTTP tests](../test/http-tests.md)

---

[← Volver al índice](../README.md)

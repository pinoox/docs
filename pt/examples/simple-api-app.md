# Passo a passo: App de API de notas

[← Voltar ao índice](../README.md)

Este guia constrói um pequeno app que expõe uma **API JSON** para notas (título + corpo). Pressupõe que o Pinoox está instalado e que `php pinoox` funciona a partir da raiz do projeto.

**Pacote:** `com_acme_notes`  
**URL do app:** `/notes`  
**Endpoint de exemplo:** `GET /notes/api/v1/notes`  
**Código-fonte completo:** [docs/source/simple-api-app/](../../source/simple-api-app/) — copie para `apps/`

---

## Pré-requisitos

- [Instalando o Pinoox](../start/installing-pinoox.md)
- [Seu primeiro app](../start/your-first-app.md) — noções básicas da estrutura de pastas

---

## Passo 1 — Criar o app

```bash
php pinoox app:create com_acme_notes --simple
```

No assistente, defina o nome de exibição `Notes` e o caminho `/notes` (ou registre o caminho depois com a CLI).

---

## Passo 2 — Registrar a URL do app

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Passo 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Edite o arquivo gerado:

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

Execute as migrations:

```bash
php pinoox migrate com_acme_notes
```

---

## Passo 4 — Model

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

## Passo 5 — Controller de API

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

As respostas JSON usam o envelope padrão do Pinoox: `{ success, data, message, meta }`.

---

## Passo 6 — `routes/api.php`

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

## Passo 7 — Registrar o `api.php` no `app.php`

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

## Passo 8 — Testar com curl

Se o projeto estiver em `http://localhost/pinoox`:

```bash
# Listar
curl -s http://localhost/pinoox/notes/api/v1/notes

# Criar
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# Um registro
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# Excluir
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **O que é uma Named Action?** Em rotas de API você normalmente aponta diretamente para `[Controller::class, 'method']`. Named Actions (`@home`) são mais úteis para páginas HTML no `web.php` — consulte [Routers](../basic/routers.md).

---

## Passo 9 — (Opcional) Página HTML com CSS

Para visualizar as notas no navegador, adicione uma página Twig simples com CSS inline:

No `routes/web.php`:

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

URL: `http://localhost/pinoox/notes/browse` — a API continua em `/api/v1/notes`.

---

## Próximos passos

- [Validação (Validation)](../basic/validation.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Testes HTTP](../test/http-tests.md)

---

[← Voltar ao índice](../README.md)

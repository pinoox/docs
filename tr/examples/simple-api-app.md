# Uygulamalı rehber: Notes API uygulaması

[← Dizine dön](../README.md)

Bu rehber notlar (başlık + gövde) için **JSON API** sunan küçük bir uygulama oluşturur. Pinoox'un kurulu olduğu ve proje kökünden `php pinoox` çalıştığı varsayılır.

**Paket:** `com_acme_notes`  
**Uygulama URL'si:** `/notes`  
**Örnek endpoint:** `GET /notes/api/v1/notes`  
**Tam kaynak:** [docs/source/simple-api-app/](../../source/simple-api-app/) — `apps/` içine kopyalayın

---

## Ön koşullar

- [Pinoox kurulumu](../start/installing-pinoox.md)
- [İlk uygulamanız](../start/your-first-app.md) — klasör yapısı temelleri

---

## Adım 1 — Uygulamayı oluşturun

```bash
php pinoox app:create com_acme_notes --simple
```

Sihirbazda görünen adı `Notes` ve yolu `/notes` olarak ayarlayın (veya yolu sonra CLI ile kaydedin).

---

## Adım 2 — Uygulama URL'sini kaydedin

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Adım 3 — Migration

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Oluşturulan dosyayı düzenleyin:

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

Migration'ları çalıştırın:

```bash
php pinoox migrate com_acme_notes
```

---

## Adım 4 — Model

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

## Adım 5 — API controller

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

JSON yanıtları Pinoox'un standart zarfını kullanır: `{ success, data, message, meta }`.

---

## Adım 6 — `routes/api.php`

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

## Adım 7 — `api.php`'yi `app.php` içinde kaydedin

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

## Adım 8 — curl ile test

Proje `http://localhost/pinoox` adresindeyse:

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

> **Named Action nedir?** API route'larında genellikle doğrudan `[Controller::class, 'method']` işaret edersiniz. Named Action'lar (`@home`) `web.php` içindeki HTML sayfaları için daha kullanışlıdır — bkz. [Router](../basic/routers.md).

---

## Adım 9 — (İsteğe bağlı) CSS ile HTML sayfası

Notları tarayıcıda görüntülemek için inline CSS'li basit bir Twig sayfası ekleyin:

`routes/web.php` içinde:

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

URL: `http://localhost/pinoox/notes/browse` — API `/api/v1/notes` adresinde kalır.

---

## Sonraki adımlar

- [Validasyon](../basic/validation.md)
- [API Resource'lar](../eloquent-orm/api-resources.md)
- [HTTP testleri](../test/http-tests.md)

---

[← Dizine dön](../README.md)

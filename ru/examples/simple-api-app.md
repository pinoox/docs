# Пошаговое руководство: приложение Notes API

[← Назад к оглавлению](../README.md)

В этом руководстве создаётся небольшое приложение, предоставляющее **JSON API** для заметок (заголовок + текст). Предполагается, что Pinoox установлен и `php pinoox` работает из корня проекта.

**Пакет:** `com_acme_notes`  
**URL приложения:** `/notes`  
**Пример endpoint:** `GET /notes/api/v1/notes`  
**Полный исходный код:** [docs/source/simple-api-app/](../../source/simple-api-app/) — скопируйте в `apps/`

---

## Предварительные требования

- [Установка Pinoox](../start/installing-pinoox.md)
- [Ваше первое приложение](../start/your-first-app.md) — основы структуры папок

---

## Шаг 1 — Создание приложения

```bash
php pinoox app:create com_acme_notes --simple
```

В мастере укажите отображаемое имя `Notes` и путь `/notes` (или зарегистрируйте путь позже через CLI).

---

## Шаг 2 — Регистрация URL приложения

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## Шаг 3 — Миграция (Migration)

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

Отредактируйте сгенерированный файл:

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

Запустите миграции:

```bash
php pinoox migrate com_acme_notes
```

---

## Шаг 4 — Модель (Model)

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

## Шаг 5 — API-контроллер

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

JSON-ответы используют стандартную обёртку Pinoox: `{ success, data, message, meta }`.

---

## Шаг 6 — `routes/api.php`

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

## Шаг 7 — Регистрация `api.php` в `app.php`

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

## Шаг 8 — Тестирование с помощью curl

Если проект находится по адресу `http://localhost/pinoox`:

```bash
# Список
curl -s http://localhost/pinoox/notes/api/v1/notes

# Создание
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# Одна запись
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# Удаление
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **Что такое именованное действие (Named Action)?** В API-маршрутах вы обычно указываете напрямую `[Controller::class, 'method']`. Именованные действия (`@home`) более полезны для HTML-страниц в `web.php` — см. [Маршрутизаторы (Routers)](../basic/routers.md).

---

## Шаг 9 — (Опционально) HTML-страница с CSS

Чтобы просматривать заметки в браузере, добавьте простую Twig-страницу со встроенным CSS:

В `routes/web.php`:

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

URL: `http://localhost/pinoox/notes/browse` — API остаётся доступным по `/api/v1/notes`.

---

## Следующие шаги

- [Валидация (Validation)](../basic/validation.md)
- [API-ресурсы](../eloquent-orm/api-resources.md)
- [HTTP-тесты](../test/http-tests.md)

---

[← Назад к оглавлению](../README.md)

# شرح تطبيقي: تطبيق Notes API

[← العودة إلى الفهرس](../README.md)

يبني هذا الدليل تطبيقًا صغيرًا يوفّر **واجهة JSON API** للملاحظات (عنوان + نص). يفترض أن Pinoox مثبَّت وأن الأمر `php pinoox` يعمل من جذر المشروع.

**الحزمة (Package):** `com_acme_notes`  
**رابط التطبيق:** `/notes`  
**نقطة نهاية (endpoint) نموذجية:** `GET /notes/api/v1/notes`  
**الكود المصدري الكامل:** [docs/source/simple-api-app/](../../source/simple-api-app/) — انسخه إلى `apps/`

---

## المتطلبات المسبقة

- [تثبيت Pinoox](../start/installing-pinoox.md)
- [تطبيقك الأول](../start/your-first-app.md) — أساسيات بنية المجلدات

---

## الخطوة 1 — إنشاء التطبيق

```bash
php pinoox app:create com_acme_notes --simple
```

في المعالج (wizard)، عيّن اسم العرض `Notes` والمسار `/notes` (أو سجّل المسار لاحقًا عبر سطر الأوامر CLI).

---

## الخطوة 2 — تسجيل رابط التطبيق

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## الخطوة 3 — الترحيل (Migration)

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

عدّل الملف المُولَّد:

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

شغّل الترحيلات:

```bash
php pinoox migrate com_acme_notes
```

---

## الخطوة 4 — الـ Model

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

## الخطوة 5 — متحكم API

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

تستخدم استجابات JSON الغلاف (envelope) القياسي في Pinoox: `{ success, data, message, meta }`.

---

## الخطوة 6 — `routes/api.php`

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

## الخطوة 7 — تسجيل `api.php` في `app.php`

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

## الخطوة 8 — الاختبار باستخدام curl

إذا كان المشروع موجودًا على `http://localhost/pinoox`:

```bash
# عرض القائمة
curl -s http://localhost/pinoox/notes/api/v1/notes

# إنشاء
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"body\":\"Hello Pinoox\"}"

# سجل واحد
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# حذف
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **ما هو الـ Named Action؟** في مسارات API عادةً ما تشير مباشرة إلى `[Controller::class, 'method']`. أما الإجراءات المسماة (Named Actions) مثل (`@home`) فهي أكثر فائدة لصفحات HTML في `web.php` — راجع [الموجّهات (Routers)](../basic/routers.md).

---

## الخطوة 9 — (اختياري) صفحة HTML مع CSS

لعرض الملاحظات في المتصفح، أضف صفحة Twig بسيطة مع CSS مضمّن:

في `routes/web.php`:

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

الرابط: `http://localhost/pinoox/notes/browse` — تبقى واجهة API على `/api/v1/notes`.

---

## الخطوات التالية

- [التحقق من الصحة (Validation)](../basic/validation.md)
- [موارد API (API resources)](../eloquent-orm/api-resources.md)
- [اختبارات HTTP](../test/http-tests.md)

---

[← العودة إلى الفهرس](../README.md)

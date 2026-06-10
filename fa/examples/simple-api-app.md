# نمونه عملی: اپ API یادداشت

[← بازگشت به فهرست](../../readme-fa.md)

در این راهنما یک اپ کوچک می‌سازیم که **JSON API** برای مدیریت یادداشت (title + body) ارائه می‌دهد. فرض: پینوکس نصب شده و از ریشه پروژه دستور `php pinoox` کار می‌کند.

**پکیج:** `com_acme_notes`  
**آدرس اپ:** `/notes`  
**endpoint نمونه:** `GET /notes/api/v1/notes`  
**سورس کامل:** [docs/source/simple-api-app/](../../source/simple-api-app/) — کپی در `apps/`

---

## پیش‌نیاز

- [نصب پینوکس](../start/installing-pinoox.md)
- [ساخت اولین اپ](../start/your-first-app.md) — برای آشنایی با ساختار

---

## گام ۱ — ساخت اپ

```bash
php pinoox app:create com_acme_notes --simple
```

در wizard نام نمایشی `Notes` و مسیر `/notes` را وارد کنید (یا بعداً با CLI ثبت کنید).

---

## گام ۲ — ثبت مسیر اپ

```bash
php pinoox app:router set /notes com_acme_notes
```

---

## گام ۳ — migration جدول

```bash
php pinoox migrate:create CreateNotes com_acme_notes
```

فایل ساخته‌شده را ویرایش کنید:

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

اجرای migration:

```bash
php pinoox migrate com_acme_notes
```

---

## گام ۴ — Model

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

## گام ۵ — کنترلر API

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

        return $this->ok($note, 'یادداشت ذخیره شد.', status: 201);
    }

    public function show(Request $request, int $id)
    {
        $note = NoteModel::find($id);

        if (!$note) {
            return $this->fail('NOT_FOUND', 'یادداشت یافت نشد.', status: 404);
        }

        return $this->ok($note);
    }

    public function destroy(Request $request, int $id)
    {
        $note = NoteModel::find($id);

        if (!$note) {
            return $this->fail('NOT_FOUND', 'یادداشت یافت نشد.', status: 404);
        }

        $note->delete();

        return $this->ok(null, 'حذف شد.');
    }
}
```

خروجی JSON همیشه در envelope استاندارد پینوکس است: `{ success, data, message, meta }`.

---

## گام ۶ — فایل `routes/api.php`

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

## گام ۷ — ثبت `api.php` در `app.php`

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

## گام ۸ — تست با curl

اگر پروژه در `http://localhost/pinoox` است:

```bash
# لیست
curl -s http://localhost/pinoox/notes/api/v1/notes

# ایجاد
curl -s -X POST http://localhost/pinoox/notes/api/v1/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"اولین یادداشت\",\"body\":\"سلام پینوکس\"}"

# یک رکورد
curl -s http://localhost/pinoox/notes/api/v1/notes/1

# حذف
curl -s -X DELETE http://localhost/pinoox/notes/api/v1/notes/1
```

> **Named Action چیست؟** در API معمولاً مستقیم `[Controller::class, 'method']` می‌نویسید. Named Action (`@home`) بیشتر برای صفحات HTML در `web.php` مفید است — [روتر](../basic/routers.md).

---

## گام ۹ — (اختیاری) صفحه HTML با CSS

برای دیدن یادداشت‌ها در مرورگر، یک صفحه Twig ساده با CSS inline:

`routes/web.php` — یک مسیر اضافه کنید:

```php
get('/browse', [MainController::class, 'browse'])->name('browse');
```

`Controller/MainController.php`:

```php
public function browse()
{
    $notes = NoteModel::orderByDesc('id')->get();

    return View::render('pages/browse.twig', [
        'title' => 'یادداشت‌ها',
        'notes' => $notes,
    ]);
}
```

`theme/default/pages/browse.twig`:

```twig
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        body { font-family: Tahoma, system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; color: #222; }
        h1 { font-size: 1.35rem; margin: 0 0 1rem; }
        ul { padding-right: 1.2rem; }
        li { margin-bottom: .75rem; padding-bottom: .75rem; border-bottom: 1px solid #ddd; }
        li strong { display: block; margin-bottom: .25rem; }
        .meta { color: #666; font-size: .85rem; }
        .empty { color: #888; font-style: italic; }
    </style>
</head>
<body>
    <h1>{{ title }}</h1>
    {% if notes is empty %}
        <p class="empty">هنوز یادداشتی نیست — با curl یا Postman یکی بسازید.</p>
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

آدرس: `http://localhost/pinoox/notes/browse` — API همچنان از `/api/v1/notes` در دسترس است.

---

## بعد از این نمونه

- [اعتبارسنجی](../basic/validation.md) — قوانین بیشتر
- [منابع API](../eloquent-orm/api-resources.md) — شکل‌دهی خروجی JSON
- [تست HTTP](../test/http-tests.md) — خودکارسازی تست endpointها

---

[← بازگشت به فهرست](../../readme-fa.md)

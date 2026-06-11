# شرح تطبيقي: تطبيق معرض الصور

[← العودة إلى الفهرس](../README.md)

ابنِ **معرض صور**: نموذج رفع، تخزين الملفات في Pinoox، شبكة صور مصغّرة، وحذف. مثال جيد لتعلّم **`File::upload()`** وربط `file_id` بالـ Model.

**الحزمة (Package):** `com_acme_gallery`  
**الرابط (URL):** `http://localhost/pinoox/gallery`  
**الكود المصدري الكامل:** [docs/source/gallery-app/](../../source/gallery-app/) — انسخه إلى `apps/`
---

## المتطلبات المسبقة

- [إدارة الملفات](../advanced/file-management.md)
- [الترحيلات (Migrations)](../database/migrations.md)

---

## الخطوة 1 — إنشاء التطبيق

```bash
php pinoox app:create com_acme_gallery --simple
php pinoox app:router set /gallery com_acme_gallery
```

---

## الخطوة 2 — إعدادات app.php

فعّل transport ونظام الملفات (filesystem) لعمليات الرفع:

```php
<?php

return [
    'package' => 'com_acme_gallery',
    'name' => 'Gallery',
    'enable' => true,
    'theme' => 'default',
    'transport' => [
        'file_storage' => 'platform',
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 320,
        'thumb_height' => 320,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## الخطوة 3 — جدول `gallery_items`

```bash
php pinoox migrate:create CreateGalleryItems com_acme_gallery
```

```php
<?php
namespace App\com_acme_gallery\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('gallery_items', 'com_acme_gallery'), function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->unsignedBigInteger('file_id');
            $table->timestamps();

            $table->index('file_id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('gallery_items', 'com_acme_gallery'));
    }
};
```

```bash
php pinoox migrate com_acme_gallery
```

---

## الخطوة 4 — الـ Model

```bash
php pinoox model:create GalleryItem com_acme_gallery
```

```php
<?php
namespace App\com_acme_gallery\Model;

use Pinoox\Component\Database\Model;

class GalleryItemModel extends Model
{
    protected $table = 'gallery_items';

    protected $fillable = ['title', 'file_id'];
}
```

---

## الخطوة 5 — المسارات (Routes)

`routes/actions.php`:

```php
<?php

use App\com_acme_gallery\Controller\GalleryController;
use function Pinoox\Router\action;

action('gallery.list', [GalleryController::class, 'index']);
action('gallery.store', [GalleryController::class, 'store']);
action('gallery.delete', [GalleryController::class, 'destroy']);
```

`routes/web.php`:

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@gallery.list')->name('home');
post('/upload', '@gallery.store')->name('gallery.store');
post('/delete/{id}', '@gallery.delete')->name('gallery.delete');
```

---

## الخطوة 6 — الـ Controller

```bash
php pinoox controller:create GalleryController com_acme_gallery
```

```php
<?php
namespace App\com_acme_gallery\Controller;

use App\com_acme_gallery\Model\GalleryItemModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\File;
use Pinoox\Portal\View;

class GalleryController extends Controller
{
    private function galleryItems()
    {
        return GalleryItemModel::orderByDesc('id')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'url' => File::url($item->file_id),
                'thumb' => File::thumb($item->file_id) ?: File::url($item->file_id),
            ];
        });
    }

    public function index()
    {
        return View::render('pages/gallery', [
            'title' => 'Image gallery',
            'items' => $this->galleryItems(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:200',
            'photo' => 'required|file|image|max:4096',
        ]);

        $result = File::upload('photo')
            ->to('gallery')   // → storage/apps/com_acme_gallery/gallery
            ->group('gallery')
            ->thumb()
            ->maxSize('4MB')
            ->extensions('jpg,jpeg,png,webp,gif')
            ->save();

        if (!$result->success) {
            return View::render('pages/gallery', [
                'title' => 'Image gallery',
                'items' => $this->galleryItems(),
                'error' => $result->error ?: 'Upload failed',
            ]);
        }

        GalleryItemModel::create([
            'title' => $data['title'],
            'file_id' => $result->id,
        ]);

        return redirect(url('/'));
    }

    public function destroy(Request $request, int $id)
    {
        $item = GalleryItemModel::find($id);

        if ($item) {
            File::remove($item->file_id);
            $item->delete();
        }

        return redirect(url('/'));
    }
}
```

> **`File::upload('photo')`** يقرأ الحقل `<input type="file" name="photo">`. البيانات الوصفية (metadata) للملف تُخزَّن في جدول ملفات المنصة؛ أما الـ Model الخاص بك فيخزّن `file_id` فقط.

---

## الخطوة 7 — قالب Twig (مع CSS مضمّن)

`theme/default/pages/gallery.twig`:

```twig
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; color: #0f172a; margin: 0; line-height: 1.5; }
        .page-wide { max-width: 960px; margin: 0 auto; padding: 2rem 1rem; }
        .page-title { margin: 0 0 1.25rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; font-size: 1.5rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; }
        .subtitle { margin: 0 0 1rem; font-size: 1.1rem; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; font-weight: 600; margin-bottom: .35rem; font-size: .9rem; }
        .field input { width: 100%; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .btn { padding: .5rem 1.25rem; font: inherit; font-weight: 600; border-radius: 6px; cursor: pointer; background: transparent; border: 2px solid #2563eb; color: #2563eb; }
        .btn:hover { background: #2563eb; color: #fff; }
        .btn-danger { border-color: #dc2626; color: #dc2626; font-size: .85rem; padding: .3rem .75rem; }
        .btn-danger:hover { background: #dc2626; color: #fff; }
        .alert { padding: .75rem 1rem; border: 2px solid #dc2626; border-radius: 6px; color: #991b1b; background: #fef2f2; margin-bottom: 1rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; }
        .card { border: 2px solid #cbd5e1; border-radius: 8px; padding: .75rem; text-align: center; background: #fff; }
        .card img { width: 100%; height: 140px; object-fit: cover; border-radius: 4px; border: 2px solid #e2e8f0; }
        .card-title { margin: .5rem 0; font-weight: 600; font-size: .9rem; }
        form.inline { display: inline; margin: 0; }
        .empty { color: #64748b; font-style: italic; }
    </style>
</head>
<body>
<div class="page-wide">
    <h1 class="page-title">{{ title }}</h1>
    {% if error %}<div class="alert">{{ error }}</div>{% endif %}
    <div class="panel">
        <h2 class="subtitle">Upload image</h2>
        <form method="post" action="{{ url('upload') }}" enctype="multipart/form-data">
            <div class="field"><label>Title</label><input name="title" required maxlength="200"></div>
            <div class="field"><label>File</label><input type="file" name="photo" accept="image/*" required></div>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
    <div class="grid">
        {% for item in items %}
        <div class="card">
            <a href="{{ item.url }}" target="_blank"><img src="{{ item.thumb }}" alt="{{ item.title }}"></a>
            <div class="card-title">{{ item.title }}</div>
            <form method="post" action="{{ url('delete/' ~ item.id) }}" class="inline"><button type="submit" class="btn btn-danger">Delete</button></form>
        </div>
        {% else %}<p class="empty">No images yet.</p>{% endfor %}
    </div>
</div>
</body>
</html>
```

لا تنسَ إضافة **`enctype="multipart/form-data"`** إلى نموذج الرفع.

---

## الخطوة 8 — الاختبار

1. افتح `http://localhost/pinoox/gallery`.
2. ارفع صورة JPG/PNG — ستظهر في الشبكة مع صورة مصغّرة (thumbnail).
3. انقر على الصورة — تُفتح بالحجم الكامل في تبويب جديد.
4. احذف — يُحذف سجل قاعدة البيانات والملف الفعلي عبر `File::remove()`.

إذا واجهت أخطاء صلاحيات، تحقّق من صلاحية الكتابة على مجلد `storage/` في المشروع.

---

## الخطوات التالية

| الترقية | التوثيق |
|---------|-----|
| رفع عدة ملفات | كرّر `File::upload()` في حلقة أو استخدم عدة حقول |
| تصنيفات الألبومات | [علاقات Eloquent](../eloquent-orm/relationships.md) |
| واجهة API للجوال | [شرح Notes API](./simple-api-app.md) |
| S3 / CDN | [إدارة الملفات](../advanced/file-management.md) |

---

## توثيقات ذات صلة

- [التحقق من الصحة (Validation)](../basic/validation.md) — قواعد `image|max`
- [المتحكمات (Controllers)](../basic/controllers.md)

---

[← العودة إلى الفهرس](../README.md)

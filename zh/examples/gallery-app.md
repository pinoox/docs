# 实战演练：图片相册应用

[← 返回索引](../README.md)

构建一个**图片相册**：上传表单、Pinoox 文件存储、缩略图网格和删除功能。非常适合学习 **`File::upload()`** 以及将 `file_id` 关联到模型。

**包名（Package）：** `com_acme_gallery`  
**URL：** `http://localhost/pinoox/gallery`  
**完整源码：** [docs/source/gallery-app/](../../source/gallery-app/) — 复制到 `apps/`
---

## 前置条件

- [文件管理](../advanced/file-management.md)
- [迁移（Migrations）](../database/migrations.md)

---

## 第 1 步 — 创建应用

```bash
php pinoox app:create com_acme_gallery --simple
php pinoox app:router set /gallery com_acme_gallery
```

---

## 第 2 步 — app.php 设置

为上传启用 transport 和 filesystem：

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

## 第 3 步 — `gallery_items` 表

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

## 第 4 步 — 模型（Model）

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

## 第 5 步 — 路由（Routes）

`routes/actions.php`：

```php
<?php

use App\com_acme_gallery\Controller\GalleryController;
use function Pinoox\Router\action;

action('gallery.list', [GalleryController::class, 'index']);
action('gallery.store', [GalleryController::class, 'store']);
action('gallery.delete', [GalleryController::class, 'destroy']);
```

`routes/web.php`：

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@gallery.list')->name('home');
post('/upload', '@gallery.store')->name('gallery.store');
post('/delete/{id}', '@gallery.delete')->name('gallery.delete');
```

---

## 第 6 步 — 控制器（Controller）

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

> **`File::upload('photo')`** 读取 `<input type="file" name="photo">` 字段。文件元数据保存在平台文件表中；你的模型只存储 `file_id`。

---

## 第 7 步 — Twig 模板（内联 CSS）

`theme/default/pages/gallery.twig`：

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

不要忘记在上传表单上添加 **`enctype="multipart/form-data"`**。

---

## 第 8 步 — 测试

1. 打开 `http://localhost/pinoox/gallery`。
2. 上传一张 JPG/PNG — 它会以缩略图的形式出现在网格中。
3. 点击图片 — 在新标签页中查看原图。
4. 删除 — 数据库记录和物理文件会通过 `File::remove()` 一并移除。

如果遇到权限错误，请检查项目 `storage/` 文件夹的写入权限。

---

## 后续步骤

| 升级方向 | 文档 |
|---------|-----|
| 多文件上传 | 循环调用 `File::upload()` 或使用多个字段 |
| 相册分类 | [Eloquent 关联（relationships）](../eloquent-orm/relationships.md) |
| 移动端 API | [笔记 API 实战演练](./simple-api-app.md) |
| S3 / CDN | [文件管理](../advanced/file-management.md) |

---

## 相关文档

- [验证（Validation）](../basic/validation.md) — `image|max` 规则
- [控制器（Controllers）](../basic/controllers.md)

---

[← 返回索引](../README.md)

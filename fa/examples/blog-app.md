# نمونه عملی: وبلاگ ساده

[← بازگشت به فهرست](../README.md)

یک وبلاگ کوچک می‌سازیم: **لیست مطالب**، **صفحه تک‌مطلب** با slug در URL، و **فرم نوشتن پست**. مناسب برای یادگیری route داینامیک و چند صفحه Twig.

**پکیج:** `com_acme_blog`  
**آدرس:** `http://localhost/pinoox/blog`  
**سورس کامل:** [docs/source/blog-app/](../../source/blog-app/) — کپی در `apps/`
---

## پیش‌نیاز

- [روتر](../basic/routers.md) — پارامتر `{slug}`
- [Eloquent](../eloquent-orm/getting-started.md)

---

## گام ۱ — ساخت اپ

```bash
php pinoox app:create com_acme_blog --simple
php pinoox app:router set /blog com_acme_blog
```

---

## گام ۲ — جدول posts

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

```php
<?php
namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('posts', 'com_acme_blog'));
    }
};
```

```bash
php pinoox migrate com_acme_blog
```

---

## گام ۳ — Model

```bash
php pinoox model:create Post com_acme_blog
```

```php
<?php
namespace App\com_acme_blog\Model;

use Pinoox\Component\Database\Model;

class PostModel extends Model
{
    protected $table = 'posts';

    protected $fillable = ['title', 'slug', 'body'];
}
```

---

## گام ۴ — مسیرها

`routes/actions.php`:

```php
<?php

use App\com_acme_blog\Controller\PostController;
use function Pinoox\Router\action;

action('post.list', [PostController::class, 'index']);
action('post.show', [PostController::class, 'show']);
action('post.create', [PostController::class, 'createForm']);
action('post.store', [PostController::class, 'store']);
```

`routes/web.php`:

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@post.list')->name('home');
get('/write', '@post.create')->name('post.create');
post('/write', '@post.store')->name('post.store');
get('/post/{slug}', '@post.show')->name('post.show');
```

> **Route داینامیک:** `{slug}` به پارامتر `$slug` در متد کنترلر می‌رسد.

---

## گام ۵ — کنترلر

```bash
php pinoox controller:create PostController com_acme_blog
```

```php
<?php
namespace App\com_acme_blog\Controller;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class PostController extends Controller
{
    public function index()
    {
        return View::render('pages/list', [
            'title' => 'وبلاگ',
            'posts' => PostModel::orderByDesc('id')->get(),
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $post = PostModel::where('slug', $slug)->first();

        if (!$post) {
            return View::render('pages/404', ['title' => 'یافت نشد'], status: 404);
        }

        return View::render('pages/show', [
            'title' => $post->title,
            'post' => $post,
        ]);
    }

    public function createForm()
    {
        return View::render('pages/write', [
            'title' => 'نوشتن مطلب',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string|min:20',
        ]);

        $slug = $this->makeSlug($data['title']);

        $post = PostModel::create([
            'title' => $data['title'],
            'slug' => $slug,
            'body' => $data['body'],
        ]);

        return redirect(url('post/' . $post->slug));
    }

    private function makeSlug(string $title): string
    {
        $base = strtolower(trim(preg_replace('/[^\p{L}\p{N}]+/u', '-', $title), '-'));
        $slug = $base ?: 'post';
        $n = 1;

        while (PostModel::where('slug', $slug)->exists()) {
            $slug = $base . '-' . ++$n;
        }

        return $slug;
    }
}
```

---

## گام ۶ — قالب‌ها (CSS inline)

`theme/default/pages/list.twig`:

```twig
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        body { font-family: Tahoma, system-ui, sans-serif; background: #f1f5f9; margin: 0; line-height: 1.5; color: #0f172a; }
        .page { max-width: 640px; margin: 0 auto; padding: 2rem 1rem; }
        .page-title { margin: 0 0 1rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: .4rem 1rem; border: 2px solid #2563eb; border-radius: 6px; color: #2563eb; text-decoration: none; font-weight: 600; }
        .post-list { list-style: none; padding: 0; margin: 0; }
        .post-list li { padding: .85rem 0; border-bottom: 2px solid #e2e8f0; }
        .post-list a { font-weight: 600; color: #0f172a; text-decoration: none; }
        .post-meta { color: #64748b; font-size: .85rem; margin-right: .5rem; }
        .empty { color: #64748b; font-style: italic; }
    </style>
</head>
<body>
<div class="page">
    <h1 class="page-title">{{ title }}</h1>
    <p><a class="btn" href="{{ url('write') }}">نوشتن مطلب جدید</a></p>
    <div class="panel">
        <ul class="post-list">
            {% for post in posts %}
            <li><a href="{{ url('post/' ~ post.slug) }}">{{ post.title }}</a><span class="post-meta">{{ post.created_at }}</span></li>
            {% else %}<li class="empty">هنوز مطلبی نیست.</li>{% endfor %}
        </ul>
    </div>
</div>
</body>
</html>
```

`theme/default/pages/show.twig`:

```twig
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        body { font-family: Tahoma, system-ui, sans-serif; background: #f1f5f9; margin: 0; line-height: 1.5; color: #0f172a; }
        .page { max-width: 640px; margin: 0 auto; padding: 2rem 1rem; }
        .link { color: #2563eb; font-weight: 600; text-decoration: none; }
        .page-title { margin: .5rem 0 1rem; }
        .article { border: 2px solid #cbd5e1; border-radius: 8px; padding: 1.25rem; background: #fff; line-height: 1.7; }
    </style>
</head>
<body>
<div class="page">
    <p><a class="link" href="{{ url('/') }}">← لیست مطالب</a></p>
    <h1 class="page-title">{{ post.title }}</h1>
    <article class="article">{{ post.body|nl2br }}</article>
</div>
</body>
</html>
```

`theme/default/pages/write.twig`:

```twig
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        body { font-family: Tahoma, system-ui, sans-serif; background: #f1f5f9; margin: 0; line-height: 1.5; }
        .page { max-width: 640px; margin: 0 auto; padding: 2rem 1rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.5rem; }
        .page-title { margin: 0 0 1rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; font-weight: 600; margin-bottom: .35rem; }
        .field input, .field textarea { width: 100%; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .btn { padding: .5rem 1.25rem; border: 2px solid #2563eb; border-radius: 6px; background: transparent; color: #2563eb; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
<div class="page">
    <h1 class="page-title">{{ title }}</h1>
    <div class="panel">
        <form method="post" action="{{ url('write') }}">
            <div class="field"><label>عنوان</label><input name="title" required maxlength="200"></div>
            <div class="field"><label>متن</label><textarea name="body" rows="8" required minlength="20"></textarea></div>
            <button type="submit" class="btn">انتشار</button>
        </form>
    </div>
</div>
</body>
</html>
```

---

## گام ۷ — تست

1. `/blog/write` — یک مطلب بنویسید.
2. بعد از submit به `/blog/post/your-slug` redirect می‌شوید.
3. `/blog` لیست را نشان می‌دهد.

---

## ایده‌های بعدی

| ارتقا | مستند |
|-------|--------|
| صفحه‌بندی لیست | [Pagination](../database/pagination.md) |
| محافظت `/write` | [Flow / Auth](../basic/flows.md) |
| API موبایل | [نمونه API](./simple-api-app.md) |

---

## مستندات مرتبط

- [Viewها — Views](../basic/views.md)
- [قالب Twig — Templates](../basic/templates.md)

---

[← بازگشت به فهرست](../README.md)

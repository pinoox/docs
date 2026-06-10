# شروع به کار Eloquent ORM

[← بازگشت به فهرست](../../readme-fa.md)

مدل‌های اپ در **`apps/{package}/Model/`** قرار می‌گیرند و از **`Pinoox\Component\Database\Model`** ارث می‌برند. این کلاس پایه پینوکس است: Eloquent را با اتصال دیتابیس و prefix خودکار اپ یکپارچه می‌کند.

---

## ساخت Model

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
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'title', 'body', 'status',
    ];
}
```

نام فیزیکی جدول از `DB::tableNameForModel()` resolve می‌شود — prefix اپ خودکار اعمال می‌گردد.

---

## Table constants

```php
<?php
namespace App\com_acme_blog\Model;

final class Table
{
    public const POSTS = 'posts';
    public const COMMENTS = 'comments';
}
```

```php
protected $table = Table::POSTS;
```

---

## CRUD پایه

```php
use App\com_acme_blog\Model\PostModel;

$post = PostModel::find(1);
$post = PostModel::where('status', 'published')->first();
$all = PostModel::where('user_id', 5)->get();

$post = PostModel::create([
    'title' => 'سلام پینوکس',
    'body' => '...',
    'status' => 'draft',
    'user_id' => Auth::id(),
]);

$post->update(['status' => 'published']);
$post->delete();
```

---

## Query scopes (زنجیره‌ای)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## اتصال دیتابیس

Model به‌صورت خودکار connection اپ را از namespace می‌گیرد:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

برای query دستی:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## prefix جدول — یادآوری

| سناریو | جدول `posts` |
|--------|--------------|
| DB مشترک، `com_acme_blog` | `blog_posts` (prefix مشتق از package) |
| DB اختصاصی، prefix خالی | `posts` |
| prefix صریح `shop_` | `shop_posts` |
| core | `pincore_user` و غیره |

---

## transaction در Model

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## نکات

- Model در `Model/` اپ — نه در pincore (مگر fork فریمورک).
- `$fillable` یا `$guarded` را تعریف کنید.
- برای جداول core از `Pinoox\Model\UserModel` و مدل‌های pincore استفاده کنید.

---

## مستندات مرتبط

- [شروع کار با دیتابیس](../database/getting-started.md)
- [روابط](./relationships.md)
- [Migration — مهاجرت](../database/migrations.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

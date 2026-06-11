# البدء مع Eloquent ORM

[← العودة إلى الفهرس](../README.md)

نماذج التطبيق في **`apps/{package}/Model/`** وترث **`Pinoox\Component\Database\Model`**. هذا هو الفئة الأساسية في Pinoox: تغلّف Eloquent مع اتصال التطبيق التلقائي ومعالجة بادئة الجدول.

---

## إنشاء نموذج

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

يُحلّ اسم الجدول الفعلي عبر `DB::tableNameForModel()` — تُطبَّق بادئة التطبيق تلقائيًا.

---

## ثوابت الجداول

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

## CRUD أساسي

```php
use App\com_acme_blog\Model\PostModel;

$post = PostModel::find(1);
$post = PostModel::where('status', 'published')->first();
$all = PostModel::where('user_id', 5)->get();

$post = PostModel::create([
    'title' => 'Hello Pinoox',
    'body' => '...',
    'status' => 'draft',
    'user_id' => Auth::id(),
]);

$post->update(['status' => 'published']);
$post->delete();
```

---

## نطاقات الاستعلام (chainable)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## اتصال قاعدة البيانات

يختار النموذج اتصال التطبيق تلقائيًا من مساحة أسمائه:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

للاستعلامات اليدوية:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## بادئة الجدول — تذكير

| السيناريو | جدول `posts` |
|----------|---------------|
| DB مشتركة، `com_acme_blog` | `blog_posts` (بادئة من الحزمة) |
| DB مخصصة، بادئة فارغة | `posts` |
| بادئة صريحة `shop_` | `shop_posts` |
| النواة | `pincore_user`، إلخ |

---

## Transaction على نموذج

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## نصائح

- النماذج في مجلد `Model/` للتطبيق — وليس في pincore (إلا إذا fork الإطار).
- عرّف `$fillable` أو `$guarded`.
- لجداول النواة، استخدم `Pinoox\Model\UserModel` ونماذج pincore الأخرى.

---

## وثائق ذات صلة

- [البدء مع قاعدة البيانات](../database/getting-started.md)
- [العلاقات (Relationships)](./relationships.md)
- [الترحيلات (Migrations)](../database/migrations.md)

---

[← العودة إلى الفهرس](../README.md)

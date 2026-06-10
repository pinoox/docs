# روابط Eloquent

[← بازگشت به فهرست](../../readme-fa.md)

روابط Eloquent در پینوکس 3.x با API استاندارد **`hasMany`، `belongsTo`** و … کار می‌کنند. مثال زیر یک وبلاگ ساده در اپ پینوکسی را نشان می‌دهد:

---

## مدل‌ها

```php
<?php
namespace App\com_acme_blog\Model;

use Pinoox\Component\Database\Model;
use Pinoox\Model\UserModel;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';

    protected $fillable = ['user_id', 'title', 'body', 'status'];

    public function author()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(CommentModel::class, 'post_id', 'post_id');
    }
}
```

```php
<?php
namespace App\com_acme_blog\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';

    protected $fillable = ['post_id', 'user_id', 'body'];

    public function post()
    {
        return $this->belongsTo(PostModel::class, 'post_id', 'post_id');
    }

    public function author()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
```

---

## eager loading

```php
$posts = PostModel::with(['author', 'comments'])->get();

foreach ($posts as $post) {
    echo $post->author->email;
    echo $post->comments->count();
}
```

---

## lazy loading

```php
$post = PostModel::find(1);
$comments = $post->comments;   // query جدا
$author = $post->author;
```

---

## ایجاد از طریق relation

```php
$post = PostModel::find(1);

$post->comments()->create([
    'user_id' => Auth::id(),
    'body' => 'نظر جدید',
]);
```

---

## whereHas

```php
$posts = PostModel::whereHas('comments', function ($q) {
    $q->where('created_at', '>=', now()->subDay());
})->get();
```

---

## belongsToMany (اختیاری)

```php
public function tags()
{
    return $this->belongsToMany(
        TagModel::class,
        'post_tags',
        'post_id',
        'tag_id',
    );
}
```

جدول واسط `post_tags` هم prefix اپ را می‌گیرد.

---

## نکات

- FK به `UserModel` core: `user_id` → `pincore_user.user_id`.
- همیشه `with()` برای لیست API تا N+1 query نشود.
- نام relation را مف singular/plural استاندارد Eloquent بگذارید.

---

## مستندات مرتبط

- [شروع به کار Eloquent](./getting-started.md)
- [مجموعه‌ها](./collections.md)
- [سریال‌سازی](./serialization.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

# Eloquent ilişkileri

[← Dizine dön](../README.md)

Pinoox 3.x'te Eloquent ilişkileri standart **`hasMany`**, **`belongsTo`** ve ilgili API'leri kullanır. Aşağıdaki örnek Pinoox uygulaması içinde basit bir blogdur:

---

## Model'ler

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

## Eager loading

```php
$posts = PostModel::with(['author', 'comments'])->get();

foreach ($posts as $post) {
    echo $post->author->email;
    echo $post->comments->count();
}
```

---

## Lazy loading

```php
$post = PostModel::find(1);
$comments = $post->comments;   // separate query
$author = $post->author;
```

---

## İlişki üzerinden oluşturma

```php
$post = PostModel::find(1);

$post->comments()->create([
    'user_id' => Auth::id(),
    'body' => 'New comment',
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

## belongsToMany (isteğe bağlı)

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

Pivot tablo `post_tags` da uygulama önekini alır.

---

## İpuçları

- Çekirdek `UserModel`'e FK: `user_id` → `pincore_user.user_id`.
- N+1 sorgularından kaçınmak için API listelerinde her zaman `with()` kullanın.
- Standart Eloquent tekil/çoğul ilişki adlarını kullanın.

---

## İlgili dokümantasyon

- [Eloquent'e başlarken](./getting-started.md)
- [Collection'lar](./collections.md)
- [Serileştirme](./serialization.md)

---

[← Dizine dön](../README.md)

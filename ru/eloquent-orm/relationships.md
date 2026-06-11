# Eloquent Relationships

[← Вернуться к оглавлению](../README.md)

Eloquent-связи в Pinoox 3.x используют стандартные API **`hasMany`**, **`belongsTo`** и связанные. Пример ниже — простой блог внутри приложения Pinoox:

---

## Модели

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
$comments = $post->comments;   // отдельный запрос
$author = $post->author;
```

---

## Создание через связь

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

## belongsToMany (опционально)

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

Pivot-таблица `post_tags` тоже получает префикс приложения.

---

## Советы

- FK к core `UserModel`: `user_id` → `pincore_user.user_id`.
- Всегда используйте `with()` для API-списков, чтобы избежать N+1 запросов.
- Используйте стандартные Eloquent-имена связей в единственном/множественном числе.

---

## Связанные документы

- [Eloquent — начало работы](./getting-started.md)
- [Collections](./collections.md)
- [Serialization](./serialization.md)

---

[← Вернуться к оглавлению](../README.md)

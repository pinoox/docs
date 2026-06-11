# Eloquent 关联关系

[← 返回索引](../README.md)

Pinoox 3.x 中的 Eloquent 关联使用标准的 **`hasMany`**、**`belongsTo`** 及相关 API。以下示例是 Pinoox 应用内的简单博客：

---

## 模型

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

## 预加载（Eager loading）

```php
$posts = PostModel::with(['author', 'comments'])->get();

foreach ($posts as $post) {
    echo $post->author->email;
    echo $post->comments->count();
}
```

---

## 懒加载（Lazy loading）

```php
$post = PostModel::find(1);
$comments = $post->comments;   // 单独查询
$author = $post->author;
```

---

## 通过关联创建

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

## belongsToMany（可选）

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

中间表 `post_tags` 也会应用应用前缀。

---

## 提示

- 关联核心 `UserModel`：`user_id` → `pincore_user.user_id`。
- API 列表务必使用 `with()`，避免 N+1 查询。
- 使用标准 Eloquent 单数/复数关联命名。

---

## 相关文档

- [Eloquent 入门](./getting-started.md)
- [集合](./collections.md)
- [序列化](./serialization.md)

---

[← 返回索引](../README.md)

# Eloquent Relationships

[← 索引に戻る](../README.md)

Pinoox 3.x の Eloquent リレーションは標準的な **`hasMany`**、**`belongsTo`** などの API を使用します。以下の例は Pinoox アプリ内のシンプルなブログです。

---

## Model

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
$comments = $post->comments;   // 別クエリ
$author = $post->author;
```

---

## リレーション経由で作成

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

## belongsToMany（任意）

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

中間テーブル `post_tags` にもアプリプレフィックスが付きます。

---

## ヒント

- コア `UserModel` への FK: `user_id` → `pincore_user.user_id`。
- N+1 クエリを避けるため API リストでは常に `with()` を使用。
- 標準的な Eloquent 単数/複数リレーション名を使用。

---

## 関連ドキュメント

- [Eloquent はじめに](./getting-started.md)
- [Collections](./collections.md)
- [Serialization](./serialization.md)

---

[← 索引に戻る](../README.md)

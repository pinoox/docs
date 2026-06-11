# Eloquent ORM 시작하기

[← 색인으로 돌아가기](../README.md)

앱 Model은 **`apps/{package}/Model/`**에 있으며 **`Pinoox\Component\Database\Model`**을 확장합니다. Pinoox base class로, 자동 앱 connection과 table prefix 처리를 포함한 Eloquent wrapper입니다.

---

## Model 생성

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

Physical table name은 `DB::tableNameForModel()`로 resolve — 앱 prefix가 자동 적용됩니다.

---

## Table constant

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

## 기본 CRUD

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

## Query scope (chainable)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Database connection

Model은 namespace에서 앱 connection을 자동 선택합니다:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

수동 query:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Table prefix — 요약

| Scenario | Table `posts` |
|----------|---------------|
| Shared DB, `com_acme_blog` | `blog_posts` (prefix from package) |
| Dedicated DB, empty prefix | `posts` |
| Explicit prefix `shop_` | `shop_posts` |
| Core | `pincore_user`, etc. |

---

## Model에서 transaction

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Tips

- Model은 앱 `Model/` 폴더에 — pincore 아님 (framework fork 제외).
- `$fillable` 또는 `$guarded` 정의.
- core table에는 `Pinoox\Model\UserModel` 등 pincore model 사용.

---

## 관련 문서

- [Database 시작하기](../database/getting-started.md)
- [Relationships](./relationships.md)
- [Migrations](../database/migrations.md)

---

[← 색인으로 돌아가기](../README.md)

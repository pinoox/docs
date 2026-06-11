# Eloquent ORM はじめに

[← 索引に戻る](../README.md)

アプリ Model は **`apps/{package}/Model/`** に置き、**`Pinoox\Component\Database\Model`** を継承します。これが Pinoox の基底クラスで、自動アプリ接続とテーブルプレフィックス処理で Eloquent をラップします。

---

## Model を作成

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

物理テーブル名は `DB::tableNameForModel()` 経由で解決 — アプリプレフィックスは自動適用されます。

---

## テーブル定数

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

## 基本 CRUD

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

## クエリスコープ（チェーン可能）

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Database 接続

Model は名前空間から自動的にアプリ接続を選択:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

手動クエリ:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## テーブルプレフィックス — リマインダー

| シナリオ | テーブル `posts` |
|----------|---------------|
| 共有 DB、`com_acme_blog` | `blog_posts`（パッケージからのプレフィックス） |
| 専用 DB、空プレフィックス | `posts` |
| 明示的プレフィックス `shop_` | `shop_posts` |
| Core | `pincore_user` など |

---

## Model 上の transaction

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## ヒント

- Model はアプリ `Model/` フォルダに — pincore ではない（フレームワークをフォークしない限り）
- `$fillable` または `$guarded` を定義
- コアテーブルには `Pinoox\Model\UserModel` など pincore Model を使用

---

## 関連ドキュメント

- [Database はじめに](../database/getting-started.md)
- [Relationships](./relationships.md)
- [Migrations](../database/migrations.md)

---

[← 索引に戻る](../README.md)

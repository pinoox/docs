# Eloquent ORM'ye başlarken

[← Dizine dön](../README.md)

Uygulama model'leri **`apps/{package}/Model/`** içinde yer alır ve **`Pinoox\Component\Database\Model`**'i genişletir. Bu Pinoox'un temel sınıfıdır: Eloquent'i otomatik uygulama bağlantısı ve tablo öneki işleme ile sarar.

---

## Model oluşturma

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

Fiziksel tablo adı `DB::tableNameForModel()` üzerinden çözümlenir — uygulama öneki otomatik uygulanır.

---

## Tablo sabitleri

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

## Temel CRUD

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

## Query scope'ları (zincirlenebilir)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Veritabanı bağlantısı

Model, namespace'inden uygulama bağlantısını otomatik seçer:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Manuel sorgular için:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Tablo öneki — hatırlatma

| Senaryo | Tablo `posts` |
|----------|---------------|
| Paylaşımlı DB, `com_acme_blog` | `blog_posts` (paketten önek) |
| Ayrılmış DB, boş önek | `posts` |
| Açık önek `shop_` | `shop_posts` |
| Çekirdek | `pincore_user` vb. |

---

## Model'de transaction

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## İpuçları

- Model'ler uygulama `Model/` klasöründe olmalı — pincore'da değil (framework'ü fork etmediğiniz sürece).
- `$fillable` veya `$guarded` tanımlayın.
- Çekirdek tablolar için `Pinoox\Model\UserModel` ve diğer pincore model'lerini kullanın.

---

## İlgili dokümantasyon

- [Veritabanına başlarken](../database/getting-started.md)
- [İlişkiler](./relationships.md)
- [Migration'lar](../database/migrations.md)

---

[← Dizine dön](../README.md)

# Primeiros passos com Eloquent ORM

[← Voltar ao índice](../README.md)

Models de app ficam em **`apps/{package}/Model/`** e estendem **`Pinoox\Component\Database\Model`**. Essa é a classe base do Pinoox: encapsula Eloquent com conexão automática do app e tratamento de prefixo de tabela.

---

## Criar um model

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

O nome físico da tabela é resolvido via `DB::tableNameForModel()` — o prefixo do app é aplicado automaticamente.

---

## Constantes de tabela

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

## CRUD básico

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

## Query scopes (encadeáveis)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Conexão de banco de dados

O model escolhe automaticamente a conexão do app a partir do namespace:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Para queries manuais:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Prefixo de tabela — lembrete

| Cenário | Tabela `posts` |
|----------|---------------|
| DB compartilhado, `com_acme_blog` | `blog_posts` (prefixo do pacote) |
| DB dedicado, prefixo vazio | `posts` |
| Prefixo explícito `shop_` | `shop_posts` |
| Núcleo | `pincore_user`, etc. |

---

## Transaction em um model

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Dicas

- Models ficam na pasta `Model/` do app — não no pincore (a menos que você faça fork do framework).
- Defina `$fillable` ou `$guarded`.
- Para tabelas do núcleo, use `Pinoox\Model\UserModel` e outros models do pincore.

---

## Documentação relacionada

- [Primeiros passos com banco de dados](../database/getting-started.md)
- [Relacionamentos](./relationships.md)
- [Migrations](../database/migrations.md)

---

[← Voltar ao índice](../README.md)

# Premiers pas avec Eloquent ORM

[← Retour à l'index](../README.md)

Les modèles d'app se trouvent dans **`apps/{package}/Model/`** et étendent **`Pinoox\Component\Database\Model`**. C'est la classe de base Pinoox : elle encapsule Eloquent avec gestion automatique de la connexion et du préfixe de table de l'app.

---

## Créer un modèle

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

Le nom de table physique est résolu via `DB::tableNameForModel()` — le préfixe de l'app est appliqué automatiquement.

---

## Constantes de table

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

## CRUD de base

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

## Scopes de requête (chaînables)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Connexion base de données

Le modèle choisit automatiquement la connexion de l'app depuis son espace de noms :

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Pour les requêtes manuelles :

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Préfixe de table — rappel

| Scénario | Table `posts` |
|----------|---------------|
| DB partagée, `com_acme_blog` | `blog_posts` (préfixe du paquet) |
| DB dédiée, préfixe vide | `posts` |
| Préfixe explicite `shop_` | `shop_posts` |
| Cœur | `pincore_user`, etc. |

---

## Transaction sur un modèle

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Conseils

- Les modèles appartiennent au dossier `Model/` de l'app — pas dans pincore (sauf fork du framework).
- Définissez `$fillable` ou `$guarded`.
- Pour les tables du cœur, utilisez `Pinoox\Model\UserModel` et les autres modèles pincore.

---

## Documentation associée

- [Premiers pas base de données](../database/getting-started.md)
- [Relations](./relationships.md)
- [Migrations](../database/migrations.md)

---

[← Retour à l'index](../README.md)

# Eloquent ORM — Erste Schritte

[← Zurück zum Index](../README.md)

App-Models liegen in **`apps/{package}/Model/`** und erweitern **`Pinoox\Component\Database\Model`**. Das ist die Pinoox-Basisklasse: Sie kapselt Eloquent mit automatischer App-Verbindung und Tabellenpräfix-Behandlung.

---

## Model erstellen

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

Der physische Tabellenname wird über `DB::tableNameForModel()` aufgelöst — das App-Präfix wird automatisch angewendet.

---

## Tabellenkonstanten

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

## Grundlegendes CRUD

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

## Query Scopes (verkettbar)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Datenbankverbindung

Das Model wählt automatisch die App-Verbindung aus seinem Namespace:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Für manuelle Abfragen:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Tabellenpräfix — Erinnerung

| Szenario | Tabelle `posts` |
|----------|---------------|
| Gemeinsame DB, `com_acme_blog` | `blog_posts` (Präfix aus Package) |
| Dedizierte DB, leeres Präfix | `posts` |
| Explizites Präfix `shop_` | `shop_posts` |
| Core | `pincore_user`, usw. |

---

## Transaktion auf einem Model

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Tipps

- Models gehören in den App-Ordner `Model/` — nicht in pincore (außer beim Fork des Frameworks).
- `$fillable` oder `$guarded` definieren.
- Für Core-Tabellen `Pinoox\Model\UserModel` und andere pincore-Models verwenden.

---

## Verwandte Dokumentation

- [Datenbank — Erste Schritte](../database/getting-started.md)
- [Beziehungen](./relationships.md)
- [Migrationen](../database/migrations.md)

---

[← Zurück zum Index](../README.md)

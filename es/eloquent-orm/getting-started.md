# Primeros pasos con Eloquent ORM

[← Volver al índice](../README.md)

Los modelos de app viven en **`apps/{package}/Model/`** y extienden **`Pinoox\Component\Database\Model`**. Es la clase base de Pinoox: envuelve Eloquent con conexión automática de app y manejo de prefijo de tabla.

---

## Crear un modelo

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

El nombre físico de tabla se resuelve vía `DB::tableNameForModel()` — el prefijo de la app se aplica automáticamente.

---

## Constantes de tabla

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

## Query scopes (encadenables)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Conexión de base de datos

El modelo elige automáticamente la conexión de la app desde su namespace:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Para consultas manuales:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Prefijo de tabla — recordatorio

| Escenario | Tabla `posts` |
|----------|---------------|
| DB compartida, `com_acme_blog` | `blog_posts` (prefijo del paquete) |
| DB dedicada, prefijo vacío | `posts` |
| Prefijo explícito `shop_` | `shop_posts` |
| Núcleo | `pincore_user`, etc. |

---

## Transacción en un modelo

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Consejos

- Los modelos pertenecen a la carpeta `Model/` de la app — no en pincore (salvo que bifurques el framework).
- Define `$fillable` o `$guarded`.
- Para tablas del núcleo, usa `Pinoox\Model\UserModel` y otros modelos pincore.

---

## Documentación relacionada

- [Primeros pasos con base de datos](../database/getting-started.md)
- [Relaciones](./relationships.md)
- [Migraciones](../database/migrations.md)

---

[← Volver al índice](../README.md)

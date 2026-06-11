# Serialización de modelos

[← Volver al índice](../README.md)

La serialización convierte un modelo Eloquent en array o JSON — para APIs, caché o logs. Pinoox 3.x sigue el comportamiento de Illuminate; `$hidden`, `$visible` y `$appends` desempeñan el papel principal.

---

## toArray

```php
$post = PostModel::find(1);
$array = $post->toArray();
```

---

## toJson

```php
$json = $post->toJson();
$json = $post->toJson(JSON_UNESCAPED_UNICODE);
```

---

## $hidden — excluir de la salida

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password se omite
```

---

## $visible — lista blanca

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

Cuando `$visible` está definido, solo esos campos aparecen en `toArray()` / `toJson()`.

---

## makeHidden / makeVisible (temporal)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // solo admin — usar con cuidado
```

---

## $appends — atributo virtual

```php
protected $appends = ['full_name'];

protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => "{$this->fname} {$this->lname}",
    );
}
```

---

## Relaciones en la salida

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// incluye clave 'comments' => [...]
```

Sin eager loading, la relación se carga de forma lazy.

---

## setHidden en una colección

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Enfoque | Caso de uso |
|----------|----------|
| `toArray()` / `toJson()` | Depuración, caché interna, exportación |
| `ApiResource` | API pública — control preciso de campos y forma anidada |

Para endpoints públicos, prefiere **ApiResource**.

---

## Casts en serialización

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` se convierte en array y `paid_at` en cadena ISO en JSON.

---

## Consejos

- Define `$hidden` para cada campo sensible.
- `$guarded` / `$fillable` son independientes de la serialización (asignación masiva).
- Usa `toArray()` sin contraseñas al registrar en logs.

---

## Documentación relacionada

- [Mutators y casts](./mutators-casts.md)
- [API resources](./api-resources.md)
- [Colecciones](./collections.md)

---

[← Volver al índice](../README.md)

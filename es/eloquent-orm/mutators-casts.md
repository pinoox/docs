# Mutators y casts

[← Volver al índice](../README.md)

Pinoox 3.x soporta **`$casts`** y accessors/mutators estándar de Illuminate Eloquent. Normalizan tipos de datos al leer o escribir en la base de datos.

---

## $casts

```php
<?php
namespace App\com_acme_shop\Model;

use Pinoox\Component\Database\Model;

class OrderModel extends Model
{
    protected $table = 'orders';

    protected $casts = [
        'metadata' => 'array',
        'total' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'items' => 'json',
    ];
}
```

| cast | Resultado |
|------|--------|
| `array` / `json` | Array PHP |
| `boolean` | true/false |
| `datetime` | Instancia Carbon |
| `decimal:2` | Número con dos decimales |
| `integer` / `float` | Tipo numérico |

---

## Ejemplo del núcleo — UserModel

```php
protected $casts = [
    'metadata' => 'array',
];
```

```php
$user->metadata['theme'] = 'dark';
$user->save();
```

---

## Accessor (get)

```php
protected function fullTitle(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => "[{$this->status}] {$this->title}",
    );
}
```

```php
echo $post->full_title;
```

---

## Mutator (set)

```php
protected function title(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        set: fn (string $value) => trim($value),
    );
}
```

---

## $appends

Campos calculados en salida JSON/array:

```php
protected $appends = ['full_name'];

protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => trim("{$this->fname} {$this->lname}"),
    );
}
```

---

## withCasts (temporal)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## Consejos

- Haz cast de columnas JSON con `'array'`, no `json_decode` manual.
- Usa `$appends` con cuidado en listas grandes cuando los accessors son costosos.
- Para hash de contraseña, usa `Pinoox\Portal\Hash` en un mutator o servicio, no un cast.

---

## Documentación relacionada

- [Serialización](./serialization.md)
- [Primeros pasos Eloquent](./getting-started.md)

---

[← Volver al índice](../README.md)

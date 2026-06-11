# Mutators et casts

[← Retour à l'index](../README.md)

Pinoox 3.x prend en charge les **`$casts`** et accessors/mutators Illuminate Eloquent standard. Ils normalisent les types de données à la lecture ou à l'écriture en base.

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

| cast | Résultat |
|------|--------|
| `array` / `json` | tableau PHP |
| `boolean` | true/false |
| `datetime` | instance Carbon |
| `decimal:2` | nombre à deux décimales |
| `integer` / `float` | type numérique |

---

## Exemple du cœur — UserModel

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

Champs calculés dans la sortie JSON/tableau :

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

## withCasts (temporaire)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## Conseils

- Castez les colonnes JSON avec `'array'`, pas un `json_decode` manuel.
- Utilisez `$appends` avec prudence sur de grandes listes lorsque les accessors sont coûteux.
- Pour le hachage de mot de passe, utilisez `Pinoox\Portal\Hash` dans un mutator ou un service, pas un cast.

---

## Documentation associée

- [Sérialisation](./serialization.md)
- [Premiers pas Eloquent](./getting-started.md)

---

[← Retour à l'index](../README.md)

# Mutators and Casts

[← Back to index](../../readme.md)

Pinoox 3.x supports standard Illuminate Eloquent **`$casts`** and accessors/mutators. They normalize data types when reading from or writing to the database.

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

| cast | Result |
|------|--------|
| `array` / `json` | PHP array |
| `boolean` | true/false |
| `datetime` | Carbon instance |
| `decimal:2` | Number with two decimal places |
| `integer` / `float` | Numeric type |

---

## Core example — UserModel

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

Computed fields in JSON/array output:

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

## withCasts (temporary)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## Tips

- Cast JSON columns with `'array'`, not manual `json_decode`.
- Use `$appends` carefully on large lists when accessors are expensive.
- For password hashing, use `Pinoox\Portal\Hash` in a mutator or service, not a cast.

---

## Related docs

- [Serialization](./serialization.md)
- [Eloquent getting started](./getting-started.md)

---

[← Back to index](../../readme.md)

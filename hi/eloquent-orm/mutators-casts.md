# Mutators and Casts

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x standard Illuminate Eloquent **`$casts`** और accessors/mutators support करता है। Database से read/write करते समय data types normalize करते हैं।

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

JSON/array output में computed fields:

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

- JSON columns `'array'` से cast करें, manual `json_decode` नहीं।
- Expensive accessors पर large lists में `$appends` सावधानी से उपयोग करें।
- Password hashing के लिए mutator या service में `Pinoox\Portal\Hash` उपयोग करें, cast नहीं।

---

## संबंधित docs

- [Serialization](./serialization.md)
- [Eloquent getting started](./getting-started.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

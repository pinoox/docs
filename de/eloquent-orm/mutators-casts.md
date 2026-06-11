# Mutatoren und Casts

[← Zurück zum Index](../README.md)

Pinoox 3.x unterstützt standardmäßige Illuminate-Eloquent-**`$casts`** sowie Accessors/Mutatoren. Sie normalisieren Datentypen beim Lesen aus oder Schreiben in die Datenbank.

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

| Cast | Ergebnis |
|------|--------|
| `array` / `json` | PHP-Array |
| `boolean` | true/false |
| `datetime` | Carbon-Instanz |
| `decimal:2` | Zahl mit zwei Dezimalstellen |
| `integer` / `float` | Numerischer Typ |

---

## Core-Beispiel — UserModel

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

Berechnete Felder in JSON-/Array-Ausgabe:

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

## withCasts (temporär)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## Tipps

- JSON-Spalten mit `'array'` casten, nicht manuell `json_decode`.
- `$appends` bei großen Listen vorsichtig verwenden, wenn Accessors teuer sind.
- Für Passwort-Hashing `Pinoox\Portal\Hash` in einem Mutator oder Service verwenden, nicht als Cast.

---

## Verwandte Dokumentation

- [Serialisierung](./serialization.md)
- [Eloquent — Erste Schritte](./getting-started.md)

---

[← Zurück zum Index](../README.md)

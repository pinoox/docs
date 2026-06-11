# Mutators и Casts

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x поддерживает стандартные Illuminate Eloquent **`$casts`** и accessors/mutators. Они нормализуют типы данных при чтении из базы или записи в неё.

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

| cast | Результат |
|------|--------|
| `array` / `json` | PHP-массив |
| `boolean` | true/false |
| `datetime` | экземпляр Carbon |
| `decimal:2` | число с двумя знаками после запятой |
| `integer` / `float` | числовой тип |

---

## Пример ядра — UserModel

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

Вычисляемые поля в JSON/array-выводе:

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

## withCasts (временно)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## Советы

- Приводите JSON-столбцы через `'array'`, а не вручную через `json_decode`.
- Используйте `$appends` осторожно на больших списках, когда accessors дорогие.
- Для хеширования пароля используйте `Pinoox\Portal\Hash` в mutator или сервисе, а не cast.

---

## Связанные документы

- [Serialization](./serialization.md)
- [Eloquent — начало работы](./getting-started.md)

---

[← Вернуться к оглавлению](../README.md)

# Mutators and Casts

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 표준 Illuminate Eloquent **`$casts`**와 accessor/mutator를 지원합니다. database에서 읽거나 쓸 때 data type을 정규화합니다.

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

## Core 예제 — UserModel

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

JSON/array output의 computed field:

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

- JSON column은 수동 `json_decode`가 아니라 `'array'` cast.
- accessor가 비용이 크면 큰 list에서 `$appends` 신중히 사용.
- password hashing은 cast가 아니라 mutator 또는 service에서 `Pinoox\Portal\Hash` 사용.

---

## 관련 문서

- [Serialization](./serialization.md)
- [Eloquent 시작하기](./getting-started.md)

---

[← 색인으로 돌아가기](../README.md)

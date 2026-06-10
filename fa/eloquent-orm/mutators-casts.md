# Mutatorها و Castها

پینوکس 3.x از **`$casts`** و accessor/mutator استاندارد Eloquent Illuminate پشتیبانی می‌کند. این‌ها نوع داده را هنگام خواندن/نوشتن از DB نرمال می‌کنند.

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

| cast | نتیجه |
|------|--------|
| `array` / `json` | آرایه PHP |
| `boolean` | true/false |
| `datetime` | Carbon instance |
| `decimal:2` | عدد با دو رقم اعشار |
| `integer` / `float` | عددی |

---

## مثال core — UserModel

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

فیلد محاسب‌شده در JSON/array:

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

## withCasts (موقت)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## نکات

- JSON ستون‌ها را با `'array'` cast کنید، نه manual `json_decode`.
- accessorهای سنگین را در لیست‌های بزرگ با `$appends` محتاطانه استفاده کنید.
- برای hash رمز از `Pinoox\Portal\Hash` در mutator یا Service استفاده کنید، نه cast.

---

## مستندات مرتبط

- [سریال‌سازی](./serialization.md)
- [شروع به کار Eloquent](./getting-started.md)

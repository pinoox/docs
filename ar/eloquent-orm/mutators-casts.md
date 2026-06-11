# Mutators و Casts

[← العودة إلى الفهرس](../README.md)

يدعم Pinoox 3.x **`$casts`** و accessors/mutators القياسية في Illuminate Eloquent. تُطبّع أنواع البيانات عند القراءة من أو الكتابة إلى قاعدة البيانات.

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

| cast | النتيجة |
|------|--------|
| `array` / `json` | مصفوفة PHP |
| `boolean` | true/false |
| `datetime` | مثيل Carbon |
| `decimal:2` | رقم بمنزلتين عشريتين |
| `integer` / `float` | نوع عددي |

---

## مثال النواة — UserModel

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

حقول محسوبة في مخرجات JSON/المصفوفة:

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

## withCasts (مؤقت)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## نصائح

- cast أعمدة JSON بـ `'array'`، وليس `json_decode` يدويًا.
- استخدم `$appends` بحذر على قوائم كبيرة عندما accessors مكلفة.
- لتجزئة كلمة المرور، استخدم `Pinoox\Portal\Hash` في mutator أو خدمة، وليس cast.

---

## وثائق ذات صلة

- [التسلسل (Serialization)](./serialization.md)
- [البدء مع Eloquent](./getting-started.md)

---

[← العودة إلى الفهرس](../README.md)

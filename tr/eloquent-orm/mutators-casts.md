# Mutator'lar ve cast'ler

[← Dizine dön](../README.md)

Pinoox 3.x standart Illuminate Eloquent **`$casts`** ve accessor/mutator'ları destekler. Veritabanından okurken veya yazarken veri türlerini normalize ederler.

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

| cast | Sonuç |
|------|--------|
| `array` / `json` | PHP dizisi |
| `boolean` | true/false |
| `datetime` | Carbon örneği |
| `decimal:2` | İki ondalık basamaklı sayı |
| `integer` / `float` | Sayısal tür |

---

## Çekirdek örneği — UserModel

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

JSON/dizi çıktısında hesaplanan alanlar:

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

## withCasts (geçici)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## İpuçları

- JSON sütunlarını manuel `json_decode` değil `'array'` ile cast edin.
- Accessor'lar pahalıysa büyük listelerde `$appends`'i dikkatli kullanın.
- Şifre hash'leme için cast değil, mutator veya serviste `Pinoox\Portal\Hash` kullanın.

---

## İlgili dokümantasyon

- [Serileştirme](./serialization.md)
- [Eloquent'e başlarken](./getting-started.md)

---

[← Dizine dön](../README.md)

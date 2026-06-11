# Mutators と Casts

[← 索引に戻る](../README.md)

Pinoox 3.x は標準的な Illuminate Eloquent **`$casts`** と accessor/mutator をサポートします。Database からの読み書き時にデータ型を正規化します。

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

| cast | 結果 |
|------|--------|
| `array` / `json` | PHP 配列 |
| `boolean` | true/false |
| `datetime` | Carbon インスタンス |
| `decimal:2` | 小数点以下 2 桁の数値 |
| `integer` / `float` | 数値型 |

---

## コア例 — UserModel

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

## Accessor（get）

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

## Mutator（set）

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

JSON/配列出力での計算フィールド:

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

## withCasts（一時的）

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## ヒント

- JSON カラムは手動 `json_decode` ではなく `'array'` で cast。
- accessor が重い大きなリストでは `$appends` を慎重に使用。
- パスワードハッシュには cast ではなく mutator またはサービスで `Pinoox\Portal\Hash` を使用。

---

## 関連ドキュメント

- [Serialization](./serialization.md)
- [Eloquent はじめに](./getting-started.md)

---

[← 索引に戻る](../README.md)

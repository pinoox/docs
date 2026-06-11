# 修改器与类型转换

[← 返回索引](../README.md)

Pinoox 3.x 支持标准 Illuminate Eloquent 的 **`$casts`** 以及访问器/修改器。它们在从数据库读取或写入时规范化数据类型。

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

| cast | 结果 |
|------|--------|
| `array` / `json` | PHP 数组 |
| `boolean` | true/false |
| `datetime` | Carbon 实例 |
| `decimal:2` | 保留两位小数的数字 |
| `integer` / `float` | 数值类型 |

---

## 核心示例 — UserModel

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

## 访问器（get）

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

## 修改器（set）

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

在 JSON/数组输出中的计算字段：

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

## withCasts（临时）

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## 提示

- JSON 列用 `'array'` 转换，不要手动 `json_decode`。
- 访问器开销大时，在大列表上谨慎使用 `$appends`。
- 密码哈希应在修改器或服务中使用 `Pinoox\Portal\Hash`，不要用 cast。

---

## 相关文档

- [序列化](./serialization.md)
- [Eloquent 入门](./getting-started.md)

---

[← 返回索引](../README.md)

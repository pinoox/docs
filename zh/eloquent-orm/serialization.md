# 模型序列化

[← 返回索引](../README.md)

序列化将 Eloquent 模型转换为数组或 JSON — 用于 API、缓存或日志。Pinoox 3.x 遵循 Illuminate 行为；`$hidden`、`$visible` 和 `$appends` 起主要作用。

---

## toArray

```php
$post = PostModel::find(1);
$array = $post->toArray();
```

---

## toJson

```php
$json = $post->toJson();
$json = $post->toJson(JSON_UNESCAPED_UNICODE);
```

---

## $hidden — 从输出中排除

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password 被省略
```

---

## $visible — 白名单

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

设置 `$visible` 后，只有这些字段会出现在 `toArray()` / `toJson()` 中。

---

## makeHidden / makeVisible（临时）

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // 仅管理员 — 谨慎使用
```

---

## $appends — 虚拟属性

```php
protected $appends = ['full_name'];

protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => "{$this->fname} {$this->lname}",
    );
}
```

---

## 输出中的关联

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// 包含键 'comments' => [...]
```

未预加载时，关联会懒加载。

---

## 在集合上 setHidden

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource 与 toArray

| 方式 | 用例 |
|----------|----------|
| `toArray()` / `toJson()` | 调试、内部缓存、导出 |
| `ApiResource` | 公开 API — 精确控制字段与嵌套结构 |

公开端点优先使用 **ApiResource**。

---

## 序列化中的类型转换

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` 变为数组，`paid_at` 在 JSON 中变为 ISO 字符串。

---

## 提示

- 为每个敏感字段设置 `$hidden`。
- `$guarded` / `$fillable` 与序列化无关（批量赋值）。
- 记录日志时使用不含密码的 `toArray()`。

---

## 相关文档

- [修改器与类型转换](./mutators-casts.md)
- [API 资源](./api-resources.md)
- [集合](./collections.md)

---

[← 返回索引](../README.md)

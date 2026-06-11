# Model Serialization

[← 색인으로 돌아가기](../README.md)

Serialization은 Eloquent model을 array 또는 JSON으로 변환합니다 — API, cache, log용. Pinoox 3.x는 Illuminate 동작을 따르며 `$hidden`, `$visible`, `$appends`가 주요 역할을 합니다.

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

## $hidden — output에서 제외

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password is omitted
```

---

## $visible — whitelist

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

`$visible`이 설정되면 `toArray()` / `toJson()`에 해당 field만 나타납니다.

---

## makeHidden / makeVisible (temporary)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // admin only — use with care
```

---

## $appends — virtual attribute

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

## output의 Relation

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// includes key 'comments' => [...]
```

Eager loading 없으면 relation이 lazy-load됩니다.

---

## collection에서 setHidden

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Approach | Use case |
|----------|----------|
| `toArray()` / `toJson()` | Debug, internal cache, export |
| `ApiResource` | Public API — precise field and nested shape control |

Public endpoint에는 **ApiResource**를 선호하세요.

---

## serialization의 Cast

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata`는 array가 되고 `paid_at`은 JSON에서 ISO string이 됩니다.

---

## Tips

- 모든 민감 field에 `$hidden` 설정.
- `$guarded` / `$fillable`은 serialization과 별개 (mass assignment).
- log에는 password 없이 `toArray()` 사용.

---

## 관련 문서

- [Mutators and casts](./mutators-casts.md)
- [API resources](./api-resources.md)
- [Collections](./collections.md)

---

[← 색인으로 돌아가기](../README.md)

# Model Serialization

[← Back to index](../../README.md)

Serialization converts an Eloquent model to an array or JSON — for APIs, cache, or logs. Pinoox 3.x follows Illuminate behavior; `$hidden`, `$visible`, and `$appends` play the main roles.

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

## $hidden — exclude from output

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

When `$visible` is set, only those fields appear in `toArray()` / `toJson()`.

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

## Relations in output

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// includes key 'comments' => [...]
```

Without eager loading, the relation is lazy-loaded.

---

## setHidden on a collection

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Approach | Use case |
|----------|----------|
| `toArray()` / `toJson()` | Debug, internal cache, export |
| `ApiResource` | Public API — precise field and nested shape control |

For public endpoints, prefer **ApiResource**.

---

## Casts in serialization

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` becomes an array and `paid_at` becomes an ISO string in JSON.

---

## Tips

- Set `$hidden` for every sensitive field.
- `$guarded` / `$fillable` are separate from serialization (mass assignment).
- Use `toArray()` without passwords when logging.

---

## Related docs

- [Mutators and casts](./mutators-casts.md)
- [API resources](./api-resources.md)
- [Collections](./collections.md)

---

[← Back to index](../../README.md)

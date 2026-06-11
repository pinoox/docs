# Model Serialization

[← इंडेक्स पर वापस जाएँ](../README.md)

Serialization Eloquent model को array या JSON में convert करता है — APIs, cache, या logs के लिए। Pinoox 3.x Illuminate behavior follow करता है; `$hidden`, `$visible`, और `$appends` मुख्य roles play करते हैं।

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

## $hidden — output से exclude

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

`$visible` set होने पर `toArray()` / `toJson()` में केवल वे fields appear होते हैं।

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

## Output में relations

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// includes key 'comments' => [...]
```

Eager loading के बिना relation lazy-loaded होता है।

---

## Collection पर setHidden

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Approach | Use case |
|----------|----------|
| `toArray()` / `toJson()` | Debug, internal cache, export |
| `ApiResource` | Public API — precise field and nested shape control |

Public endpoints के लिए **ApiResource** prefer करें।

---

## Serialization में casts

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

JSON में `metadata` array बनता है और `paid_at` ISO string।

---

## Tips

- हर sensitive field पर `$hidden` set करें।
- `$guarded` / `$fillable` serialization से अलग हैं (mass assignment)।
- Logging में passwords के बिना `toArray()` उपयोग करें।

---

## संबंधित docs

- [Mutators and casts](./mutators-casts.md)
- [API resources](./api-resources.md)
- [Collections](./collections.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

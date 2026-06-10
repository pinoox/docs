# سریال‌سازی Model

[← بازگشت به فهرست](../../readme-fa.md)

سریال‌سازی تبدیل Model Eloquent به آرایه یا JSON است — برای API، cache، یا log. پینوکس 3.x همان رفتار Illuminate را دارد؛ `$hidden`, `$visible`, و `$appends` نقش اصلی را بازی می‌کنند.

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

## $hidden — مخفی از خروجی

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password وجود ندارد
```

---

## $visible — whitelist

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

وقتی `$visible` set باشد، فقط همان فیلدها در `toArray()`/`toJson()` می‌آیند.

---

## makeHidden / makeVisible (موقت)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // فقط admin — با احتیاط
```

---

## $appends — attribute مجازی

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

## relations در خروجی

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// شامل کلید 'comments' => [...]
```

بدون eager load، relation lazy load می‌شود.

---

## setHidden روی Collection

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## تفاوت ApiResource و toArray

| روش | کاربرد |
|-----|--------|
| `toArray()` / `toJson()` | debug، cache داخلی، export |
| `ApiResource` | API عمومی — کنترل دقیق فیلد و nested shape |

برای endpoint عمومی همیشه **ApiResource** ترجیح دارد.

---

## casts در سریال‌سازی

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` به آرایه و `paid_at` به string ISO در JSON تبدیل می‌شود.

---

## نکات

- `$hidden` را برای هر فیلد حساس set کنید.
- `$guarded` / `$fillable` جدا از سریال‌سازی است (mass assignment).
- در log از `toArray()` بدون password استفاده کنید.

---

## مستندات مرتبط

- [Mutatorها و Castها](./mutators-casts.md)
- [منابع API](./api-resources.md)
- [مجموعه‌ها](./collections.md)

---

[← بازگشت به فهرست](../../readme-fa.md)

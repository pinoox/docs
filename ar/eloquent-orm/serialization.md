# تسلسل النموذج (Serialization)

[← العودة إلى الفهرس](../README.md)

التسلسل يحوّل نموذج Eloquent إلى مصفوفة أو JSON — لـ APIs أو cache أو السجلات. Pinoox 3.x يتبع سلوك Illuminate؛ `$hidden` و`$visible` و`$appends` يلعبون الدور الرئيسي.

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

## $hidden — استبعاد من المخرجات

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

## $visible — قائمة بيضاء

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

عند ضبط `$visible`، تظهر تلك الحقول فقط في `toArray()` / `toJson()`.

---

## makeHidden / makeVisible (مؤقت)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // admin only — use with care
```

---

## $appends — سمة افتراضية

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

## العلاقات في المخرجات

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// includes key 'comments' => [...]
```

بدون eager loading، تُحمَّل العلاقة كسولًا.

---

## setHidden على collection

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource مقابل toArray

| الأسلوب | حالة الاستخدام |
|----------|----------|
| `toArray()` / `toJson()` | تصحيح، cache داخلي، تصدير |
| `ApiResource` | API عام — تحكم دقيق بالحقول والشكل المتداخل |

لنقاط النهاية العامة، يُفضّل **ApiResource**.

---

## Casts في التسلسل

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` تصبح مصفوفة و`paid_at` تصبح سلسلة ISO في JSON.

---

## نصائح

- اضبط `$hidden` لكل حقل حساس.
- `$guarded` / `$fillable` منفصلان عن التسلسل (mass assignment).
- استخدم `toArray()` بدون كلمات مرور عند التسجيل.

---

## وثائق ذات صلة

- [Mutators و casts](./mutators-casts.md)
- [موارد API (API resources)](./api-resources.md)
- [المجموعات (Collections)](./collections.md)

---

[← العودة إلى الفهرس](../README.md)

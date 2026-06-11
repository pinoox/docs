# Model serileştirme

[← Dizine dön](../README.md)

Serileştirme bir Eloquent model'ini diziye veya JSON'a dönüştürür — API, önbellek veya loglar için. Pinoox 3.x Illuminate davranışını izler; `$hidden`, `$visible` ve `$appends` ana rolleri oynar.

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

## $hidden — çıktıdan hariç tut

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

## $visible — beyaz liste

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

`$visible` ayarlandığında `toArray()` / `toJson()` içinde yalnızca bu alanlar görünür.

---

## makeHidden / makeVisible (geçici)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // admin only — use with care
```

---

## $appends — sanal öznitelik

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

## Çıktıda ilişkiler

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// includes key 'comments' => [...]
```

Eager loading olmadan ilişki lazy-load edilir.

---

## Collection'da setHidden

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource ve toArray

| Yaklaşım | Kullanım |
|----------|----------|
| `toArray()` / `toJson()` | Debug, dahili önbellek, export |
| `ApiResource` | Genel API — kesin alan ve iç içe şekil kontrolü |

Genel endpoint'ler için **ApiResource** tercih edin.

---

## Serileştirmede cast'ler

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` diziye, `paid_at` JSON'da ISO dizesine dönüşür.

---

## İpuçları

- Her hassas alan için `$hidden` ayarlayın.
- `$guarded` / `$fillable` serileştirmeden ayrıdır (toplu atama).
- Loglarken şifresiz `toArray()` kullanın.

---

## İlgili dokümantasyon

- [Mutator'lar ve cast'ler](./mutators-casts.md)
- [API Resource'lar](./api-resources.md)
- [Collection'lar](./collections.md)

---

[← Dizine dön](../README.md)

# Сериализация модели

[← Вернуться к оглавлению](../README.md)

Сериализация преобразует Eloquent-модель в массив или JSON — для API, кэша или логов. Pinoox 3.x следует поведению Illuminate; основную роль играют `$hidden`, `$visible` и `$appends`.

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

## $hidden — исключение из вывода

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password опущен
```

---

## $visible — белый список

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

Когда `$visible` задан, только эти поля появляются в `toArray()` / `toJson()`.

---

## makeHidden / makeVisible (временно)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // только admin — используйте осторожно
```

---

## $appends — виртуальный атрибут

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

## Связи в выводе

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// включает ключ 'comments' => [...]
```

Без eager loading связь загружается лениво.

---

## setHidden на collection

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Подход | Случай использования |
|----------|----------|
| `toArray()` / `toJson()` | Отладка, внутренний кэш, экспорт |
| `ApiResource` | Публичный API — точный контроль полей и вложенной формы |

Для публичных endpoint предпочитайте **ApiResource**.

---

## Casts при сериализации

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` становится массивом, а `paid_at` — ISO-строкой в JSON.

---

## Советы

- Задайте `$hidden` для каждого чувствительного поля.
- `$guarded` / `$fillable` отделены от сериализации (mass assignment).
- Используйте `toArray()` без паролей при логировании.

---

## Связанные документы

- [Mutators и casts](./mutators-casts.md)
- [API resources](./api-resources.md)
- [Collections](./collections.md)

---

[← Вернуться к оглавлению](../README.md)

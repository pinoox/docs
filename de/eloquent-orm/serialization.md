# Model-Serialisierung

[← Zurück zum Index](../README.md)

Serialisierung wandelt ein Eloquent-Model in ein Array oder JSON um — für APIs, Cache oder Logs. Pinoox 3.x folgt dem Illuminate-Verhalten; `$hidden`, `$visible` und `$appends` spielen die Hauptrolle.

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

## $hidden — von der Ausgabe ausschließen

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password wird ausgelassen
```

---

## $visible — Whitelist

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

Wenn `$visible` gesetzt ist, erscheinen nur diese Felder in `toArray()` / `toJson()`.

---

## makeHidden / makeVisible (temporär)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // nur Admin — mit Vorsicht verwenden
```

---

## $appends — virtuelles Attribut

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

## Beziehungen in der Ausgabe

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// enthält Schlüssel 'comments' => [...]
```

Ohne Eager Loading wird die Beziehung lazy geladen.

---

## setHidden auf einer Collection

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs. toArray

| Ansatz | Anwendungsfall |
|----------|----------|
| `toArray()` / `toJson()` | Debug, interner Cache, Export |
| `ApiResource` | Öffentliche API — präzise Feld- und verschachtelte Formsteuerung |

Für öffentliche Endpunkte **ApiResource** bevorzugen.

---

## Casts bei der Serialisierung

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` wird ein Array und `paid_at` ein ISO-String in JSON.

---

## Tipps

- `$hidden` für jedes sensible Feld setzen.
- `$guarded` / `$fillable` sind von der Serialisierung getrennt (Mass Assignment).
- Beim Logging `toArray()` ohne Passwörter verwenden.

---

## Verwandte Dokumentation

- [Mutatoren und Casts](./mutators-casts.md)
- [API-Ressourcen](./api-resources.md)
- [Collections](./collections.md)

---

[← Zurück zum Index](../README.md)

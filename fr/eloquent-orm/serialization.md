# Sérialisation de modèle

[← Retour à l'index](../README.md)

La sérialisation convertit un modèle Eloquent en tableau ou JSON — pour les API, le cache ou les logs. Pinoox 3.x suit le comportement Illuminate ; `$hidden`, `$visible` et `$appends` jouent les rôles principaux.

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

## $hidden — exclure de la sortie

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password est omis
```

---

## $visible — liste blanche

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

Lorsque `$visible` est défini, seuls ces champs apparaissent dans `toArray()` / `toJson()`.

---

## makeHidden / makeVisible (temporaire)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // admin uniquement — avec prudence
```

---

## $appends — attribut virtuel

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

## Relations dans la sortie

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// inclut la clé 'comments' => [...]
```

Sans eager loading, la relation est chargée à la volée.

---

## setHidden sur une collection

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Approche | Cas d'usage |
|----------|----------|
| `toArray()` / `toJson()` | Debug, cache interne, export |
| `ApiResource` | API publique — contrôle précis des champs et de la forme imbriquée |

Pour les endpoints publics, préférez **ApiResource**.

---

## Casts dans la sérialisation

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` devient un tableau et `paid_at` une chaîne ISO en JSON.

---

## Conseils

- Définissez `$hidden` pour chaque champ sensible.
- `$guarded` / `$fillable` sont distincts de la sérialisation (mass assignment).
- Utilisez `toArray()` sans mots de passe lors du logging.

---

## Documentation associée

- [Mutators et casts](./mutators-casts.md)
- [Ressources API](./api-resources.md)
- [Collections](./collections.md)

---

[← Retour à l'index](../README.md)

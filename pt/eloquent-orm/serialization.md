# Serialização de Model

[← Voltar ao índice](../README.md)

Serialização converte um model Eloquent em array ou JSON — para APIs, cache ou logs. O Pinoox 3.x segue o comportamento do Illuminate; `$hidden`, `$visible` e `$appends` têm papel central.

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

## $hidden — excluir da saída

```php
class UserModel extends Model
{
    protected $hidden = ['password', 'session_id', 'app'];
}
```

```php
$user->toArray();   // password é omitido
```

---

## $visible — whitelist

```php
protected $visible = ['post_id', 'title', 'status', 'created_at'];
```

Quando `$visible` está definido, apenas esses campos aparecem em `toArray()` / `toJson()`.

---

## makeHidden / makeVisible (temporário)

```php
$user->makeHidden(['email'])->toArray();
$user->makeVisible(['password'])->toArray();   // admin only — use com cuidado
```

---

## $appends — atributo virtual

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

## Relações na saída

```php
$post = PostModel::with('comments')->find(1);
$array = $post->toArray();
// inclui chave 'comments' => [...]
```

Sem eager loading, a relação é carregada lazy.

---

## setHidden em uma coleção

```php
PostModel::all()->each->makeHidden(['body']);
```

---

## ApiResource vs toArray

| Abordagem | Caso de uso |
|----------|----------|
| `toArray()` / `toJson()` | Debug, cache interno, export |
| `ApiResource` | API pública — controle preciso de campos e forma aninhada |

Para endpoints públicos, prefira **ApiResource**.

---

## Casts na serialização

```php
protected $casts = ['metadata' => 'array', 'paid_at' => 'datetime'];
```

`metadata` vira array e `paid_at` vira string ISO em JSON.

---

## Dicas

- Defina `$hidden` para todo campo sensível.
- `$guarded` / `$fillable` são separados da serialização (mass assignment).
- Use `toArray()` sem senhas ao registrar logs.

---

## Documentação relacionada

- [Mutators e casts](./mutators-casts.md)
- [API resources](./api-resources.md)
- [Coleções](./collections.md)

---

[← Voltar ao índice](../README.md)

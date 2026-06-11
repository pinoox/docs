# Mutators e Casts

[← Voltar ao índice](../README.md)

O Pinoox 3.x suporta **`$casts`** e accessors/mutators padrão do Illuminate Eloquent. Eles normalizam tipos de dados ao ler ou gravar no banco de dados.

---

## $casts

```php
<?php
namespace App\com_acme_shop\Model;

use Pinoox\Component\Database\Model;

class OrderModel extends Model
{
    protected $table = 'orders';

    protected $casts = [
        'metadata' => 'array',
        'total' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'items' => 'json',
    ];
}
```

| cast | Resultado |
|------|--------|
| `array` / `json` | Array PHP |
| `boolean` | true/false |
| `datetime` | Instância Carbon |
| `decimal:2` | Número com duas casas decimais |
| `integer` / `float` | Tipo numérico |

---

## Exemplo do núcleo — UserModel

```php
protected $casts = [
    'metadata' => 'array',
];
```

```php
$user->metadata['theme'] = 'dark';
$user->save();
```

---

## Accessor (get)

```php
protected function fullTitle(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => "[{$this->status}] {$this->title}",
    );
}
```

```php
echo $post->full_title;
```

---

## Mutator (set)

```php
protected function title(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        set: fn (string $value) => trim($value),
    );
}
```

---

## $appends

Campos calculados na saída JSON/array:

```php
protected $appends = ['full_name'];

protected function fullName(): \Illuminate\Database\Eloquent\Casts\Attribute
{
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
        get: fn () => trim("{$this->fname} {$this->lname}"),
    );
}
```

---

## withCasts (temporário)

```php
PostModel::withCasts(['view_count' => 'integer'])->get();
```

---

## Dicas

- Faça cast de colunas JSON com `'array'`, não `json_decode` manual.
- Use `$appends` com cuidado em listas grandes quando accessors forem caros.
- Para hash de senha, use `Pinoox\Portal\Hash` em mutator ou serviço, não um cast.

---

## Documentação relacionada

- [Serialização](./serialization.md)
- [Primeiros passos com Eloquent](./getting-started.md)

---

[← Voltar ao índice](../README.md)

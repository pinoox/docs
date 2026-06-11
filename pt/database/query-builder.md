# Query Builder

[← Voltar ao índice](../README.md)

O Query Builder do Pinoox é o **Illuminate Query Builder** acessado pelo portal **`DB`** com conexão no escopo do app. Use para queries que não precisam de um model completo ou para joins complexos.

---

## Primeiros passos

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

O nome lógico da tabela (`orders`) recebe automaticamente o prefixo do app.

---

## where

```php
DB::app()->table('orders')
    ->where('status', 'paid')
    ->where('total', '>', 100000)
    ->orderBy('created_at', 'desc')
    ->get();

DB::app()->table('orders')
    ->whereIn('status', ['pending', 'paid'])
    ->whereNull('deleted_at')
    ->get();

DB::app()->table('orders')
    ->where(function ($q) {
        $q->where('status', 'paid')
          ->orWhere('status', 'shipped');
    })
    ->get();
```

---

## insert / update / delete

```php
$id = DB::app()->table('orders')->insertGetId([
    'user_id' => 1,
    'total' => 250000,
    'status' => 'pending',
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::app()->table('orders')
    ->where('order_id', $id)
    ->update(['status' => 'paid']);

DB::app()->table('orders')
    ->where('order_id', $id)
    ->delete();
```

---

## join

```php
$rows = DB::app()->table('orders as o')
    ->join('order_items as i', 'o.order_id', '=', 'i.order_id')
    ->where('o.status', 'paid')
    ->select('o.order_id', 'o.total', 'i.product_name', 'i.qty')
    ->get();
```

Join com tabela do núcleo:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

Prefira relacionamentos Eloquent para legibilidade quando possível.

---

## agregados

```php
$count = DB::app()->table('orders')->where('status', 'paid')->count();
$sum = DB::app()->table('orders')->where('status', 'paid')->sum('total');
$avg = DB::app()->table('orders')->avg('total');
```

---

## transaction

```php
DB::app()->transaction(function () {
    $orderId = DB::app()->table('orders')->insertGetId([...]);
    DB::app()->table('order_items')->insert([...]);
});
```

---

## Nome físico da tabela

```php
$table = DB::tableName('orders');           // com prefixo do app ativo
$table = DB::tableName('orders', 'com_my_shop');
```

---

## DB::core()

```php
$user = DB::core()->table('user')
    ->where('email', $email)
    ->first();
```

---

## Dicas

- Use `select()` explícito em joins grandes.
- Prefira models Eloquent para CRUD de tabela única.
- `DB::enableQueryLog()` é útil ao depurar SQL.

---

## Documentação relacionada

- [Primeiros passos com banco de dados](./getting-started.md)
- [Paginação](./pagination.md)
- [Eloquent — primeiros passos](../eloquent-orm/getting-started.md)

---

[← Voltar ao índice](../README.md)

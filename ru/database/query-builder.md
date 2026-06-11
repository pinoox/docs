# Query Builder

[← Вернуться к оглавлению](../README.md)

Query Builder Pinoox — это **Illuminate Query Builder**, доступный через Portal **`DB`** с подключением, привязанным к приложению. Используйте его для запросов, которым не нужна полная модель, или для сложных join.

---

## Начало работы

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

Логическое имя таблицы (`orders`) автоматически получает префикс приложения.

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

Join с таблицей ядра:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

По возможности предпочитайте Eloquent-связи для читаемости.

---

## aggregate

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

## Физическое имя таблицы

```php
$table = DB::tableName('orders');           // с префиксом активного приложения
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

## Советы

- Используйте явный `select()` для больших join.
- Предпочитайте Eloquent-модели для CRUD одной таблицы.
- `DB::enableQueryLog()` полезен при отладке SQL.

---

## Связанные документы

- [Начало работы с базой данных](./getting-started.md)
- [Пагинация](./pagination.md)
- [Eloquent — начало работы](../eloquent-orm/getting-started.md)

---

[← Вернуться к оглавлению](../README.md)

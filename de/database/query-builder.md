# Query Builder

[← Zurück zum Index](../README.md)

Der Pinoox Query Builder ist **Illuminate Query Builder**, aufgerufen über das **`DB`**-Portal mit einer app-spezifischen Verbindung. Verwenden Sie ihn für Abfragen ohne vollständiges Model oder für komplexe Joins.

---

## Erste Schritte

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

Der logische Tabellenname (`orders`) erhält automatisch das App-Präfix.

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

Join mit einer Core-Tabelle:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

Wenn möglich Eloquent-Beziehungen für bessere Lesbarkeit bevorzugen.

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

## Physischer Tabellenname

```php
$table = DB::tableName('orders');           // mit aktivem App-Präfix
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

## Tipps

- Bei großen Joins explizites `select()` verwenden.
- Für Single-Table-CRUD Eloquent-Models bevorzugen.
- `DB::enableQueryLog()` ist beim Debuggen von SQL hilfreich.

---

## Verwandte Dokumentation

- [Datenbank — Erste Schritte](./getting-started.md)
- [Paginierung](./pagination.md)
- [Eloquent — Erste Schritte](../eloquent-orm/getting-started.md)

---

[← Zurück zum Index](../README.md)

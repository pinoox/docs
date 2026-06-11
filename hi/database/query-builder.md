# Query Builder

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox Query Builder **Illuminate Query Builder** है जो app-scoped connection के साथ **`DB`** portal के ज़रिए access होता है। Full model की ज़रूरत न हो या complex joins हों तो उपयोग करें।

---

## Getting started

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

Logical table name (`orders`) automatically app prefix receive करता है।

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

Core table के साथ join:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

Readability के लिए जहाँ संभव Eloquent relationships prefer करें।

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

## Physical table name

```php
$table = DB::tableName('orders');           // with active app prefix
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

## Tips

- Large joins के लिए explicit `select()` उपयोग करें।
- Single-table CRUD के लिए Eloquent models prefer करें।
- SQL debug करते समय `DB::enableQueryLog()` उपयोगी है।

---

## संबंधित docs

- [Database getting started](./getting-started.md)
- [Pagination](./pagination.md)
- [Eloquent — getting started](../eloquent-orm/getting-started.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

# Query Builder

[← بازگشت به فهرست](../README.md)

Query Builder پینوکس همان **Illuminate Query Builder** است، از Portal **`DB`** با اتصال scoped اپ. برای کوئری‌هایی که به Model کامل نیاز ندارید یا joinهای پیچیده دارید، این روش پیشنهادی است.

---

## شروع

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

نام جدول منطقی (`orders`) خودکار prefix اپ را می‌گیرد.

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

join با جدول core:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

یا از Eloquent relationship استفاده کنید (ترجیحاً برای خوانایی).

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

## نام فیزیکی جدول

```php
$table = DB::tableName('orders');           // با prefix اپ فعال
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

## نکات

- از `select()` صریح برای joinهای بزرگ استفاده کنید.
- برای CRUD تک‌جدولی Eloquent Model ترجیح دارد.
- `DB::enableQueryLog()` در debug برای بررسی SQL مفید است.

---

## مستندات مرتبط

- [شروع کار با دیتابیس](./getting-started.md)
- [صفحه‌بندی](./pagination.md)
- [Eloquent — شروع به کار](../eloquent-orm/getting-started.md)

---

[← بازگشت به فهرست](../README.md)

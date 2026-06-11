# Query Builder

[← العودة إلى الفهرس](../README.md)

Query Builder في Pinoox هو **Illuminate Query Builder** عبر portal **`DB`** مع اتصال محدود بالتطبيق. استخدمه لاستعلامات لا تحتاج نموذجًا كاملًا أو لـ joins معقدة.

---

## البدء

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

اسم الجدول المنطقي (`orders`) يتلقى بادئة التطبيق تلقائيًا.

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

Join مع جدول نواة:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

يُفضّل علاقات Eloquent للقراءة عند الإمكان.

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

## اسم الجدول الفعلي

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

## نصائح

- استخدم `select()` صريحًا للـ joins الكبيرة.
- يُفضّل نماذج Eloquent لـ CRUD على جدول واحد.
- `DB::enableQueryLog()` مفيد عند تصحيح SQL.

---

## وثائق ذات صلة

- [البدء مع قاعدة البيانات](./getting-started.md)
- [التصفح (Pagination)](./pagination.md)
- [Eloquent — البدء](../eloquent-orm/getting-started.md)

---

[← العودة إلى الفهرس](../README.md)

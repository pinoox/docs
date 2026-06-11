# 查询构建器（Query Builder）

[← 返回索引](../README.md)

Pinoox 的查询构建器就是 **Illuminate Query Builder**，通过 **`DB`** Portal 以应用作用域的连接进行访问。适用于不需要完整模型的查询或复杂的 join。

---

## 入门

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

逻辑表名（`orders`）会自动加上应用前缀。

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

与核心表 join：

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

在可能的情况下，优先使用 Eloquent 关联以提高可读性。

---

## 聚合（aggregate）

```php
$count = DB::app()->table('orders')->where('status', 'paid')->count();
$sum = DB::app()->table('orders')->where('status', 'paid')->sum('total');
$avg = DB::app()->table('orders')->avg('total');
```

---

## 事务（transaction）

```php
DB::app()->transaction(function () {
    $orderId = DB::app()->table('orders')->insertGetId([...]);
    DB::app()->table('order_items')->insert([...]);
});
```

---

## 物理表名

```php
$table = DB::tableName('orders');           // 带当前活动应用的前缀
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

## 提示

- 大型 join 请使用显式的 `select()`。
- 单表 CRUD 优先使用 Eloquent 模型。
- 调试 SQL 时，`DB::enableQueryLog()` 很有用。

---

## 相关文档

- [数据库入门](./getting-started.md)
- [分页（Pagination）](./pagination.md)
- [Eloquent —— 入门](../eloquent-orm/getting-started.md)

---

[← 返回索引](../README.md)

# Query Builder

[← 索引に戻る](../README.md)

Pinoox Query Builder は **Illuminate Query Builder** を **`DB`** Portal 経由でアプリスコープ接続付きでアクセスします。完全な Model が不要なクエリや複雑な join に使用します。

---

## はじめに

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

論理テーブル名（`orders`）には自動的にアプリプレフィックスが付きます。

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

コアテーブルとの join:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

可能なら可読性のため Eloquent リレーションを優先してください。

---

## 集計

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

## 物理テーブル名

```php
$table = DB::tableName('orders');           // アクティブアプリプレフィックス付き
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

## ヒント

- 大きな join には明示的な `select()` を使用
- 単一テーブル CRUD には Eloquent Model を優先
- SQL デバッグには `DB::enableQueryLog()` が有用

---

## 関連ドキュメント

- [Database はじめに](./getting-started.md)
- [Pagination](./pagination.md)
- [Eloquent はじめに](../eloquent-orm/getting-started.md)

---

[← 索引に戻る](../README.md)

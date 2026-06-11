# Query Builder

[← 색인으로 돌아가기](../README.md)

Pinoox Query Builder는 앱 scope connection을 가진 **`DB`** portal을 통해 접근하는 **Illuminate Query Builder**입니다. full model이 필요 없거나 복잡한 join에 사용하세요.

---

## 시작하기

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

논리적 table 이름(`orders`)에 앱 prefix가 자동 적용됩니다.

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

Core table과 join:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

가능하면 가독성을 위해 Eloquent relationship을 선호하세요.

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

- 큰 join에는 explicit `select()` 사용
- 단일 table CRUD에는 Eloquent model 선호
- SQL 디버깅에 `DB::enableQueryLog()` 유용

---

## 관련 문서

- [Database 시작하기](./getting-started.md)
- [Pagination](./pagination.md)
- [Eloquent — 시작하기](../eloquent-orm/getting-started.md)

---

[← 색인으로 돌아가기](../README.md)

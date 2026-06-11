# Query Builder

[← Dizine dön](../README.md)

Pinoox Query Builder, uygulama kapsamlı bağlantıyla **`DB`** portal'ı üzerinden erişilen **Illuminate Query Builder**'dır. Tam model gerektirmeyen veya karmaşık join'ler için kullanın.

---

## Başlarken

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

Mantıksal tablo adı (`orders`) otomatik olarak uygulama önekini alır.

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

Çekirdek tablo ile join:

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

Mümkün olduğunda okunabilirlik için Eloquent ilişkilerini tercih edin.

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

## Fiziksel tablo adı

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

## İpuçları

- Büyük join'ler için açık `select()` kullanın.
- Tek tablo CRUD için Eloquent model'lerini tercih edin.
- SQL debug ederken `DB::enableQueryLog()` faydalıdır.

---

## İlgili dokümantasyon

- [Veritabanına başlarken](./getting-started.md)
- [Sayfalama](./pagination.md)
- [Eloquent — başlarken](../eloquent-orm/getting-started.md)

---

[← Dizine dön](../README.md)

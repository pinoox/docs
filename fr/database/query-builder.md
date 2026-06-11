# Query Builder

[← Retour à l'index](../README.md)

Le Query Builder Pinoox est **Illuminate Query Builder** accessible via le portal **`DB`** avec une connexion limitée à l'app. Utilisez-le pour les requêtes qui n'ont pas besoin d'un modèle complet ou pour les jointures complexes.

---

## Premiers pas

```php
use Pinoox\Portal\Database\DB;

$orders = DB::app()->table('orders')->get();
$order = DB::app()->table('orders')->where('order_id', 5)->first();
```

Le nom logique de table (`orders`) reçoit automatiquement le préfixe de l'app.

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

Jointure avec une table du cœur :

```php
$rows = DB::app()->table('orders as o')
    ->join(DB::core()->raw(DB::tableName('user') . ' as u'), 'u.user_id', '=', 'o.user_id')
    ->select('o.order_id', 'u.email')
    ->get();
```

Préférez les relations Eloquent pour la lisibilité lorsque c'est possible.

---

## agrégats

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

## Nom de table physique

```php
$table = DB::tableName('orders');           // avec préfixe de l'app active
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

## Conseils

- Utilisez un `select()` explicite pour les grosses jointures.
- Préférez les modèles Eloquent pour le CRUD mono-table.
- `DB::enableQueryLog()` est utile pour déboguer le SQL.

---

## Documentation associée

- [Premiers pas base de données](./getting-started.md)
- [Pagination](./pagination.md)
- [Eloquent — premiers pas](../eloquent-orm/getting-started.md)

---

[← Retour à l'index](../README.md)

# Database 시작하기

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x database 계층은 **Illuminate Database**(Eloquent + Query Builder)와 **`Pinoox\Portal\Database\DB`** portal을 통해 제공됩니다. 각 앱은 `app.php`에서 connection을 정의하고, platform 자격 증명은 프로젝트 `.env`에 있습니다.

---

## DB portal

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // active app connection
DB::app('com_my_shop')->table('orders')->get();      // specific app connection
DB::core()->table('user')->get();                     // pincore tables
DB::tableName('orders');                             // physical name with prefix
```

---

## Platform default

```php
// app.php
'database' => null,
```

Model과 query는 프로젝트 default connection(`.env`의 `DB_CONNECTION`)을 사용합니다.

---

## Named platform connection

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox는 platform block에서 `app_{package}_default` connection을 clone합니다.

---

## Table prefix

### 공유 DB의 앱 (전용 database 없음)

Default: package 이름에서 파생된 짧은 prefix.

```php
'database' => null,
// com_pinoox_manager + table notifications → manager_notifications
```

### Explicit prefix

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// or
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### 전용 DB — prefix 없음

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Core table

항상 **`pincore_`** prefix: `pincore_user`, `pincore_token`, `pincore_file`.

---

## 전용 database 전체 설정

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

여러 connection:

```php
'database' => [
    'default' => 'primary',
    'connections' => [
        'primary' => ['driver' => 'sqlite', 'database' => ':memory:'],
        'analytics' => ['use' => 'mysql', 'prefix' => 'an_'],
    ],
],
```

```php
DB::app('com_my_shop', 'analytics')->table('events')->get();
```

---

## App .env key

| Key | Maps to |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | dedicated credentials |

앱 `.env`에서 **`DB_CONNECTION`을 사용하지 마세요** — 무시됩니다.

---

## database 폴더 레이아웃

```text
apps/{package}/
├── patches/                 ← one-time data patches
└── database/
    migrations/
    seed/
```

---

## Table name resolve

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Tips

- business logic은 Model/Component에; Controller는 얇게 유지
- migration과 seed는 앱 `database/` 폴더에만 — pincore 아님
- Pinker가 `database.use`와 `database.prefix`를 override할 수 있음

---

## 관련 문서

- [Query Builder](./query-builder.md)
- [Migrations](./migrations.md)
- [Eloquent — 시작하기](../eloquent-orm/getting-started.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← 색인으로 돌아가기](../README.md)

# Veritabanına başlarken

[← Dizine dön](../README.md)

Pinoox 3.x veritabanı katmanını **Illuminate Database** (Eloquent + Query Builder) ve **`Pinoox\Portal\Database\DB`** portal'ı üzerinden sağlar. Her uygulama bağlantısını `app.php` içinde tanımlar; platform kimlik bilgileri proje `.env` dosyasında yer alır.

---

## DB portal'ı

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // active app connection
DB::app('com_my_shop')->table('orders')->get();      // specific app connection
DB::core()->table('user')->get();                     // pincore tables
DB::tableName('orders');                             // physical name with prefix
```

---

## Platform varsayılanı

```php
// app.php
'database' => null,
```

Model'ler ve sorgular proje varsayılan bağlantısını kullanır (`.env` içinde `DB_CONNECTION`).

---

## Adlandırılmış platform bağlantısı

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox, platform bloğundan `app_{package}_default` adlı bir bağlantı klonlar.

---

## Tablo öneki

### Paylaşımlı DB'deki uygulama (ayrılmış veritabanı yok)

Varsayılan: paket adından türetilen kısa önek.

```php
'database' => null,
// com_pinoox_manager + table notifications → manager_notifications
```

### Açık önek

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

### Ayrılmış DB — önek yok

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Çekirdek tablolar

Her zaman **`pincore_`** öneki: `pincore_user`, `pincore_token`, `pincore_file`.

---

## Tam ayrılmış veritabanı

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Birden fazla bağlantı:

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

## Uygulama .env anahtarları

| Anahtar | Eşlendiği |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | ayrılmış kimlik bilgileri |

Uygulama `.env` dosyasında `DB_CONNECTION` **kullanmayın** — yok sayılır.

---

## database klasör düzeni

```text
apps/{package}/
├── patches/                 ← one-time data patches
└── database/
    migrations/
    seed/
```

---

## Tablo adlarını çözümleme

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## İpuçları

- İş mantığını Model/Component'te tutun; controller'lar ince kalsın.
- Migration'lar ve seed'ler yalnızca uygulama `database/` klasöründe — pincore'da değil.
- Pinker `database.use` ve `database.prefix` değerlerini geçersiz kılabilir.

---

## İlgili dokümantasyon

- [Query Builder](./query-builder.md)
- [Migration'lar](./migrations.md)
- [Eloquent — başlarken](../eloquent-orm/getting-started.md)
- [Uygulama veritabanı yapılandırması (app.php)](../start/app-manifest.md)

---

[← Dizine dön](../README.md)

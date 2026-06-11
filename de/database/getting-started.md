# Erste Schritte mit der Datenbank

[← Zurück zur Übersicht](../README.md)

Pinoox 3.x stellt die Datenbankschicht über **Illuminate Database** (Eloquent + Query Builder) und das Portal **`Pinoox\Portal\Database\DB`** bereit. Jede App definiert ihre Connection in `app.php`; die Plattform-Zugangsdaten liegen in der Projekt-`.env`.

---

## DB-Portal

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // Connection der aktiven App
DB::app('com_my_shop')->table('orders')->get();      // Connection einer bestimmten App
DB::core()->table('user')->get();                     // pincore-Tabellen
DB::tableName('orders');                             // physischer Name mit Präfix
```

---

## Plattform-Standard

```php
// app.php
'database' => null,
```

Models und Queries verwenden die Standard-Connection des Projekts (`DB_CONNECTION` in `.env`).

---

## Benannte Plattform-Connection

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox klont eine Connection namens `app_{package}_default` aus dem Plattform-Block.

---

## Tabellenpräfix

### App auf einer gemeinsamen DB (keine dedizierte Datenbank)

Standard: kurzes Präfix, abgeleitet vom Paketnamen.

```php
'database' => null,
// com_pinoox_manager + Tabelle notifications → manager_notifications
```

### Explizites Präfix

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// oder
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### Dedizierte DB — kein Präfix

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Core-Tabellen

Immer mit Präfix **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

---

## Vollständig dedizierte Datenbank

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Mehrere Connections:

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

## .env-Schlüssel der App

| Schlüssel | Entspricht |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | dedizierte Zugangsdaten |

Verwenden Sie **nicht** `DB_CONNECTION` in der App-`.env` — es wird ignoriert.

---

## Aufbau des database-Ordners

```text
apps/{package}/
├── patches/                 ← einmalige Daten-Patches
└── database/
    migrations/
    seed/
```

---

## Tabellennamen auflösen

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Tipps

- Halten Sie Geschäftslogik in Model/Component; Controller bleiben schlank.
- Migrationen und Seeds liegen nur im `database/`-Ordner der App — nicht in pincore.
- Pinker kann `database.use` und `database.prefix` überschreiben.

---

## Verwandte Dokumente

- [Query Builder](./query-builder.md)
- [Migrationen](./migrations.md)
- [Eloquent — erste Schritte](../eloquent-orm/getting-started.md)
- [App-Datenbankkonfiguration (app.php)](../start/app-manifest.md)

---

[← Zurück zur Übersicht](../README.md)

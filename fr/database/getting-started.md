# Premiers pas avec la base de données

[← Retour à l'index](../README.md)

Pinoox 3.x fournit la couche base de données via **Illuminate Database** (Eloquent + Query Builder) et le portail **`Pinoox\Portal\Database\DB`**. Chaque application définit sa connexion dans `app.php` ; les identifiants de la plateforme se trouvent dans le `.env` du projet.

---

## Portail DB

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // connexion de l'application active
DB::app('com_my_shop')->table('orders')->get();      // connexion d'une application spécifique
DB::core()->table('user')->get();                     // tables pincore
DB::tableName('orders');                             // nom physique avec préfixe
```

---

## Valeur par défaut de la plateforme

```php
// app.php
'database' => null,
```

Les modèles et les requêtes utilisent la connexion par défaut du projet (`DB_CONNECTION` dans `.env`).

---

## Connexion de plateforme nommée

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox clone une connexion nommée `app_{package}_default` à partir du bloc de la plateforme.

---

## Préfixe de table

### Application sur une DB partagée (sans base de données dédiée)

Par défaut : préfixe court dérivé du nom du paquet.

```php
'database' => null,
// com_pinoox_manager + table notifications → manager_notifications
```

### Préfixe explicite

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// ou
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### DB dédiée — sans préfixe

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Tables du cœur

Toujours le préfixe **`pincore_`** : `pincore_user`, `pincore_token`, `pincore_file`.

---

## Base de données entièrement dédiée

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Connexions multiples :

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

## Clés .env de l'application

| Clé | Correspond à |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | identifiants dédiés |

N'utilisez **pas** `DB_CONNECTION` dans le `.env` de l'application — il est ignoré.

---

## Organisation du dossier database

```text
apps/{package}/
├── patches/                 ← patchs de données ponctuels
└── database/
    migrations/
    seed/
```

---

## Résoudre les noms de tables

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Conseils

- Gardez la logique métier dans Model/Component ; les contrôleurs restent légers.
- Les migrations et les seeds résident uniquement dans le dossier `database/` de l'application — pas dans pincore.
- Pinker peut surcharger `database.use` et `database.prefix`.

---

## Documentation associée

- [Query Builder](./query-builder.md)
- [Migrations](./migrations.md)
- [Eloquent — premiers pas](../eloquent-orm/getting-started.md)
- [Configuration de la base de données de l'application (app.php)](../start/app-manifest.md)

---

[← Retour à l'index](../README.md)

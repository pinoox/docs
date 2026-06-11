# Primeros pasos con la base de datos

[← Volver al índice](../README.md)

Pinoox 3.x proporciona la capa de base de datos mediante **Illuminate Database** (Eloquent + Query Builder) y el portal **`Pinoox\Portal\Database\DB`**. Cada app define su conexión en `app.php`; las credenciales de plataforma viven en el `.env` del proyecto.

---

## Portal DB

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // conexión de la app activa
DB::app('com_my_shop')->table('orders')->get();      // conexión de una app concreta
DB::core()->table('user')->get();                     // tablas pincore
DB::tableName('orders');                             // nombre físico con prefijo
```

---

## Plataforma por defecto

```php
// app.php
'database' => null,
```

Los modelos y consultas usan la conexión por defecto del proyecto (`DB_CONNECTION` en `.env`).

---

## Conexión de plataforma con nombre

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox clona una conexión llamada `app_{package}_default` desde el bloque de plataforma.

---

## Prefijo de tabla

### App en DB compartida (sin base de datos dedicada)

Por defecto: prefijo corto derivado del nombre del paquete.

```php
'database' => null,
// com_pinoox_manager + tabla notifications → manager_notifications
```

### Prefijo explícito

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// o
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### DB dedicada — sin prefijo

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Tablas del núcleo

Siempre prefijo **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

---

## Base de datos dedicada completa

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Varias conexiones:

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

## Claves .env de la app

| Clave | Mapea a |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | credenciales dedicadas |

**No** uses `DB_CONNECTION` en el `.env` de la app — se ignora.

---

## Estructura de carpeta database

```text
apps/{package}/
├── patches/                 ← parches de datos únicos
└── database/
    migrations/
    seed/
```

---

## Resolver nombres de tabla

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Consejos

- Mantén la lógica de negocio en Model/Component; los controllers deben ser delgados.
- Migraciones y seeds viven solo en la carpeta `database/` de la app — no en pincore.
- Pinker puede sobrescribir `database.use` y `database.prefix`.

---

## Documentación relacionada

- [Query Builder](./query-builder.md)
- [Migraciones](./migrations.md)
- [Eloquent — primeros pasos](../eloquent-orm/getting-started.md)
- [Configuración de base de datos en app.php](../start/app-manifest.md)

---

[← Volver al índice](../README.md)

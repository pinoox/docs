# Migraciones

[← Volver al índice](../README.md)

Las migraciones versionan los cambios de **esquema** en la base de datos. En Pinoox 3.x, los archivos de app viven en `apps/{package}/database/migrations/` y los del núcleo en `system/database/migrations/`.

---

## Crear una migración

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Salida:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## Estructura del archivo

```php
<?php
namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('posts', 'com_acme_blog'));
    }
};
```

`$this->table('posts', $package)` aplica el prefijo correcto de la app.

---

## Ejecutar migraciones

```bash
# migración de app
php pinoox migrate com_acme_blog

# migración del núcleo
php pinoox migrate pincore

# migración de plataforma (tablas pinx_*)
php pinoox migrate platform
```

---

## Estado y rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Migración del núcleo (ejemplo)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Tablas del núcleo: prefijo **`pincore_`** (o `pinx_` para ámbito platform).

---

## Namespaces

| Ubicación | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Núcleo | `Pinoox\Database\migrations` |

---

## Ruta legacy

Pinoox sigue leyendo la carpeta antigua `apps/{package}/migrations/`, pero los archivos **nuevos** se crean en `database/migrations/`.

---

## Migración vs Seed vs Patch

| Tipo | Propósito | Comando |
|------|---------|---------|
| Migration | Esquema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Datos iniciales | `php pinoox seeder:run {package}` |
| Patch | Cambio de datos único | `php pinoox patch:run {package}` |

Guía completa de patches: [Patches (actualizaciones de datos)](../advanced/patches.md).

---

## Buenas prácticas

- Un cambio lógico por migración (una tabla o un ALTER).
- Escribe siempre `down()`.
- No edites una migración que ya se ejecutó — crea una nueva.
- Las claves foráneas a tablas del núcleo usan `$this->table(Table::FILE, 'platform')`.

---

## Documentación relacionada

- [Primeros pasos con base de datos](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [Configuración de base de datos en app.php](../start/app-manifest.md)

---

[← Volver al índice](../README.md)

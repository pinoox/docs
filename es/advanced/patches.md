# Patches (actualizaciones de datos)

[← Volver al índice](../README.md)

Un **patch** en Pinoox 3.x es un **cambio operativo de una sola vez**: corregir datos, mover registros, sincronizar config o ejecutar lógica post-actualización. No es una **migración** (esquema) ni un **seeder** (datos de seed repetibles).

---

## Cuándo usar un patch

| Herramienta | Propósito |
|------|---------|
| **Migration** | CREATE/ALTER tablas y columnas |
| **Seeder** | Datos iniciales o de muestra (ejecuciones manuales) |
| **Patch** | Ejecutar una vez y registrar en `history` |

Ejemplos de patch:

- Corregir filas inválidas tras un bug
- Rellenar valores por defecto en registros antiguos
- Renombrar valores de config en DB
- Lógica post-actualización tras una nueva versión

---

## Ubicaciones de archivos

```text
vendor/pinoox/pincore/patches/     ← plataforma (CLI: platform)
apps/{package}/patches/            ← tu app
```

> La ruta legacy `database/patches/` **no se usa**. Los patches viven junto a `app.php`, no bajo `database/`.

---

## Crear un patch

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

La CLI escribe un archivo con timestamp, p. ej.:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Forma del stub (clase anónima):

```php
<?php
namespace App\com_acme_shop\patches;

use Pinoox\Component\Database\Patch\PatchBase;
use Pinoox\Portal\Database\DB;

return new class extends PatchBase
{
    public function description(): string
    {
        return 'Set empty contact status to active';
    }

    public function shouldRun(): bool
    {
        return DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->exists();
    }

    public function canRollback(): bool
    {
        return false;
    }

    public function up(): void
    {
        DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->update(['status' => 'active']);
    }
}
```

Namespace de plataforma: `Pinoox\Patches`.

---

## Métodos PatchBase

| Método | Rol |
|--------|------|
| `up()` | Lógica principal (llamada vía `run()`) |
| `down()` | Revertir cuando `canRollback()` es true |
| `shouldRun()` | Si es false, el patch se registra como **skipped** |
| `canRollback()` | Si el rollback está permitido |
| `description()` | Texto legible en history |
| `metadata()` | JSON extra guardado en history |

---

## Comandos CLI

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Nota:** `patch:run` ejecuta primero las **migraciones de plataforma** y luego los patches del paquete seleccionado.

Alias: `php pinoox patch` = `patch:run`.

---

## Tabla history

Migraciones y patches comparten la tabla **`history`**:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Los patches exitosos no se vuelven a ejecutar automáticamente.

---

## Instalador

La app del sistema `com_pinoox_installer` ejecuta migraciones y patches durante la instalación vía `SetupService`.

---

## Buenas prácticas

- No edites un patch que ya se ejecutó — crea uno nuevo.
- Usa migraciones para esquema, no patches.
- Implementa `shouldRun()` para que comprobaciones idempotentes omitan trabajo innecesario.
- Habilita rollback solo cuando `down()` sea seguro.

---

## Documentación relacionada

- [Migraciones](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [Referencia CLI](../start/cli-reference.md)

---

[← Volver al índice](../README.md)

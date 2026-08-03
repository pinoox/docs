# Datos de prueba — Seeders

[← Volver al índice](../README.md)

Pinoox 3.x no incluye **Model Factory** (estilo Laravel) en la CLI. El enfoque recomendado para datos iniciales y de desarrollo son los **Seeders** con `SeederBase` en `apps/{package}/database/seeders/`.

---

## Crear un seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Estructura

```php
<?php
namespace App\com_acme_blog\database\seeders;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;
use Pinoox\Portal\Hash;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::insert([
            [
                'user_id' => 1,
                'title' => 'First post',
                'body' => 'Sample content',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Second post',
                'body' => '...',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
```

---

## Llamar a otro seeder

```php
public function run(): void
{
    $this->call([
        'RoleSeeder',
        'UserSeeder',
    ]);

    // datos dependientes tras usuarios
    PostModel::factory(); // ❌ no disponible — usa insert o create manualmente
}
```

---

## create con Model

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "Post #{$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## Ejecutar seeders

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
php pinoox seeder:run platform
```

`-c` matches the **file basename** (e.g. `PostSeeder`). App install does **not** auto-run seeders.

---

## Call Seeders From Code

Use `Pinoox\Portal\Database\Seeder`, or `$this->seed()` / `$this->seedAll()` in migrations and patches.

```php
use Pinoox\Portal\Database\Seeder;

Seeder::run('PostSeeder');
Seeder::run('PostSeeder', 'com_acme_blog');
Seeder::run(['RoleSeeder', 'PostSeeder']);
Seeder::run(DatabaseSeeder::class);
Seeder::runAll();
Seeder::runAll('platform');
```

```php
// migration / patch
$this->seed('GatewaySeeder');
$this->seedAll();
```

```php
// from another seeder
$this->call(['RoleSeeder', 'UserSeeder']);
```

See English docs: [Factories and seeders](../../en/eloquent-orm/factories.md#call-seeders-from-code).

---

## Orden recomendado

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders en producción

- Solo datos **esenciales** (roles, configuración por defecto).
- Protege datos fake/dev con `APP_ENV`:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // datos de muestra
}
```

---

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| Datos iniciales / de muestra | Corrección única de datos existentes |
| `seeder:run` — repetible con precaución | `patch:run` — registrado una vez |

---

## Consejos

- Escribe seeders idempotentes (`firstOrCreate` en lugar de `insert` a ciegas).
- No subas credenciales reales en seeders.
- Para tests unitarios, usa fixtures Pest o sqlite `:memory:`.

---

## Documentación relacionada

- [Migraciones](../database/migrations.md)
- [Primeros pasos Eloquent](./getting-started.md)
- [Configuración de base de datos en app.php](../start/app-manifest.md)

---

[← Volver al índice](../README.md)

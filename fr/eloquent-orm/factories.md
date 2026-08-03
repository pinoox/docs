# Données de test — Seeders

[← Retour à l'index](../README.md)

Pinoox 3.x n'inclut pas de **Model Factory** (style Laravel) dans la CLI. L'approche recommandée pour les données initiales et de développement est les **Seeders** avec `SeederBase` dans `apps/{package}/database/seeders/`.

---

## Créer un seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Structure

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

## Appeler un autre seeder

```php
public function run(): void
{
    $this->call([
        'RoleSeeder',
        'UserSeeder',
    ]);

    // données dépendantes après les utilisateurs
    PostModel::factory(); // ❌ non disponible — utilisez insert ou create manuellement
}
```

---

## create avec Model

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

## Exécuter les seeders

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

## Ordre recommandé

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders en production

- Uniquement les données **essentielles** (rôles, paramètres par défaut).
- Protégez les fausses données de dev avec `APP_ENV` :

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // données d'exemple
}
```

---

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| Données initiales / d'exemple | Correction ponctuelle sur données existantes |
| `seeder:run` — répétable avec prudence | `patch:run` — suivi une seule fois |

---

## Conseils

- Écrivez des seeders idempotents (`firstOrCreate` au lieu d'un `insert` aveugle).
- Ne commitez pas de vrais identifiants dans les seeders.
- Pour les tests unitaires, utilisez des fixtures Pest ou sqlite `:memory:`.

---

## Documentation associée

- [Migrations](../database/migrations.md)
- [Premiers pas Eloquent](./getting-started.md)
- [Configuration base de données de l'app (app.php)](../start/app-manifest.md)

---

[← Retour à l'index](../README.md)

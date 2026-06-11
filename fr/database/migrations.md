# Migrations

[← Retour à l'index](../README.md)

Les migrations versionnent les changements de **schéma** dans la base de données. Dans Pinoox 3.x, les fichiers d'application se trouvent dans `apps/{package}/database/migrations/` et les fichiers du cœur dans `system/database/migrations/`.

---

## Créer une migration

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Sortie :

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## Structure du fichier

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

`$this->table('posts', $package)` applique le préfixe d'application correct.

---

## Exécuter les migrations

```bash
# migration d'application
php pinoox migrate com_acme_blog

# migration du cœur
php pinoox migrate pincore

# migration de plateforme (tables pinx_*)
php pinoox migrate platform
```

---

## Statut et rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Migration du cœur (exemple)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Tables du cœur : préfixe **`pincore_`** (ou `pinx_` pour la portée plateforme).

---

## Namespaces

| Emplacement | Namespace |
|----------|-----------|
| Application | `App\{package}\database\migrations` |
| Cœur | `Pinoox\Database\migrations` |

---

## Chemin hérité (legacy)

Pinoox lit toujours l'ancien dossier `apps/{package}/migrations/`, mais les **nouveaux** fichiers sont créés dans `database/migrations/`.

---

## Migration vs Seed vs Patch

| Type | Rôle | Commande |
|------|---------|---------|
| Migration | Schéma (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Données initiales | `php pinoox seeder:run {package}` |
| Patch | Modification de données ponctuelle | `php pinoox patch:run {package}` |

Guide complet des patchs : [Patches (mises à jour de données)](./patches.md).

---

## Bonnes pratiques

- Un changement logique par migration (une table ou un ALTER).
- Écrivez toujours `down()`.
- Ne modifiez pas une migration déjà exécutée — créez-en une nouvelle.
- Les clés étrangères vers les tables du cœur utilisent `$this->table(Table::FILE, 'platform')`.

---

## Documentation associée

- [Premiers pas avec la base de données](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [Configuration de la base de données de l'application (app.php)](../start/app-manifest.md)

---

[← Retour à l'index](../README.md)

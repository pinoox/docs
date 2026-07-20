# Patchs (mises à jour de données)

[← Retour à l'index](../README.md)

Un **patch** dans Pinoox 3.x est une **modification opérationnelle ponctuelle** : corriger des données, déplacer des enregistrements, synchroniser la config ou exécuter une logique post-mise à jour. Ce n'est pas une **migration** (schéma) ni un **seeder** (données répétables).

---

## Quand utiliser un patch

| Outil | Rôle |
|------|---------|
| **Migration** | CREATE/ALTER tables et colonnes |
| **Seeder** | Données initiales ou d'exemple (exécutions manuelles) |
| **Patch** | Exécution unique et suivi dans `history` |

Exemples de patchs :

- Corriger des lignes invalides après un bug
- Remplir les valeurs par défaut pour d'anciens enregistrements
- Renommer des valeurs de config en DB
- Logique post-mise à jour après une nouvelle version

---

## Emplacements des fichiers

```text
vendor/pinoox/pincore/patches/     ← plateforme (CLI : platform)
apps/{package}/patches/            ← votre app
```

> L'ancien chemin `database/patches/` **n'est pas utilisé**. Les patchs vivent à côté de `app.php`, pas sous `database/`.

---

## Créer un patch

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

La CLI écrit un fichier horodaté, par ex. :

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Forme du stub (classe anonyme) :

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

Espace de noms plateforme : `Pinoox\Patches`.

---

## Méthodes PatchBase

| Méthode | Rôle |
|--------|------|
| `up()` | Logique principale (appelée via `run()`) |
| `down()` | Annulation lorsque `canRollback()` est true |
| `shouldRun()` | Si false, le patch est enregistré comme **skipped** |
| `canRollback()` | Si le rollback est autorisé |
| `description()` | Texte lisible dans l'historique |
| `metadata()` | JSON supplémentaire stocké dans l'historique |

---

## Commandes CLI

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Note :** `patch:run` exécute d'abord les **migrations plateforme**, puis les patchs du paquet sélectionné.

Alias : `php pinoox patch` = `patch:run`.

---

## Table history

Les migrations et les patchs partagent la table **`history`** :

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Les patchs réussis ne sont pas réexécutés automatiquement.

---

## Installateur

L'app système `com_pinoox_installer` exécute migrations et patchs pendant l'installation via `SetupService`.

---

## Bonnes pratiques

- Ne modifiez pas un patch déjà exécuté — créez-en un nouveau.
- Utilisez les migrations pour le schéma, pas les patchs.
- Implémentez `shouldRun()` pour que les vérifications idempotentes ignorent le travail inutile.
- Activez le rollback uniquement lorsque `down()` est sûr.

---

## Documentation associée

- [Migrations](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [Référence CLI](../start/cli-reference.md)

---

[← Retour à l'index](../README.md)

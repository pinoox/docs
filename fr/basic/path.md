# Chemin de fichier

[← Retour à l'index](../README.md)

Utilisez **`path()`** et le Portal **`Pinoox\Portal\Path`** pour accéder aux fichiers et dossiers sur le disque. Cela garde le code indépendant de l'emplacement d'installation du projet et du nom du dossier `apps/`.

---

## Approche standard — `path()`

```php
// Chemin relatif à l'app active
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Fichier de config dans une autre app
$configFile = path('config/payment.php', 'com_acme_shop');

// Racine de l'app
$appRoot = path('', 'com_acme_shop');
// ou
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## Usages courants

### Lire / écrire des fichiers

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Chemin d'un fichier de traduction

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Chemin du thème

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

Même comportement que `path()` avec une API explicite :

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // app courante
Path::app('com_pinoox_manager'); // app spécifique
```

---

## `path()` vs `url()`

| Helper | Sortie | Exemple |
|--------|--------|---------|
| `path()` | Chemin physique sur le serveur | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | URL HTTP pour le navigateur | `https://site.com/pinoox/shop/products` |

---

## Exemple : service d'upload

N'écrivez pas les uploads manuellement avec `path()` + `move_uploaded_file()` — utilisez le portal **`File`** pour que les fichiers atterrissent dans le dossier `storage/` du projet :

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // stocké sous storage/apps/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

Voir [Gestion des fichiers](../advanced/file-management.md) pour l'API complète d'upload.

---

## Conseils

- Pour les chemins accessibles au navigateur, utilisez `url()` ou `assets()`, pas `path()`.
- Passez un nom de paquet uniquement lorsque vous avez besoin d'une app non active.
- Joignez les segments de chemin avec `/` ; Path gère le bon séparateur OS.

---

## Documentation associée

- [URL et liens](./url.md)
- [Config](./config.md)
- [Services d'app](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← Retour à l'index](../README.md)

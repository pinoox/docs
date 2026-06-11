# Gestion des fichiers

[← Retour à l'index](../README.md)

L'upload et le stockage dans Pinoox 3.x passent par un portail unique : **`Pinoox\Portal\File`**. Les métadonnées se trouvent dans `pincore_file` (ou dans une portée de transport partagée) et les fichiers physiques sur le disque (local, S3, …).

---

## Point d'entrée

```php
use Pinoox\Portal\File;
```

| Besoin | API |
|------|-----|
| Upload + enregistrement DB + URL | `File::upload(...)->save()` |
| Rechercher / supprimer / URL | `File::find()`, `File::url()`, `File::remove()` |
| Accès brut au disque | `File::storage()->put(...)` |

N'utilisez pas `Storage::` directement pour les uploads des utilisateurs — le préfixe, le disque et l'URL restent cohérents avec `File::`.

---

## Configuration app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // ou 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

Disques globaux dans `config/filesystems.config.php` et `.env` :

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Upload avec enregistrement en base de données

```php
$result = File::upload('avatar')
    ->to('avatar')                  // → storage/apps/{package}/avatar
    ->group('avatar')
    ->thumb()
    ->maxSize('2MB')
    ->extensions('jpg,jpeg,png,webp')
    ->save();

if ($result->success) {
    $fileId = $result->id;
    $url = $result->url;
    $thumb = $result->thumb;
}
```

---

## Depuis la Request

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Attacher à un modèle

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Remplacer un fichier précédent :

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Disque uniquement (sans DB)

```php
$result = File::upload('file')
    ->to('packages')
    ->diskOnly()
    ->save();

if ($result->success) {
    $path = $result->path;
}
```

---

## Lire et supprimer

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — méthodes clés

| Méthode | Description |
|--------|-------------|
| `to($dir)` | Dossier de destination |
| `group($name)` | Groupe logique en DB |
| `thumb($w, $h)` | Miniature d'image |
| `maxSize('2MB')` | Taille maximale du fichier |
| `extensions('jpg,png')` | Extensions autorisées |
| `disk('s3')` | Remplacer le disque |
| `attach($model, $column)` | Définir la clé étrangère après l'upload |
| `replaceOn($model, $column)` | Supprimer l'ancien + uploader le nouveau |
| `save()` | Exécuter → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // chemin absolu
$result->record;    // FileModel
$result->error;     // message d'erreur
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// ou par upload
File::upload('doc')->to('docs')->disk('s3')->save();
```

Fichiers privés sur S3 :

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Conseils

- Validez dans une FormRequest avant `File::upload()`.
- `user_id` est rempli à partir de `Auth::id()`.
- Avec `transport.file_storage => platform`, les fichiers sont partagés entre les applications de la plateforme.

---

## Documentation associée

- [Gestion des utilisateurs](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [Tutoriel galerie d'images](../examples/gallery-app.md)

---

[← Retour à l'index](../README.md)

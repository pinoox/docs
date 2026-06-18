# Gestión de archivos

[← Volver al índice](../README.md)

La subida y el almacenamiento en Pinoox 3.x pasan por un único portal: **`Pinoox\Portal\File`**. Los metadatos viven en `pincore_file` (o un ámbito transport compartido) y los archivos físicos en disco (local, S3, …).

---

## Punto de entrada

```php
use Pinoox\Portal\File;
```

| Necesidad | API |
|------|-----|
| Subida + registro DB + URL | `File::upload(...)->save()` |
| Buscar / eliminar / URL | `File::find()`, `File::url()`, `File::remove()` |
| Acceso directo al disco | `File::storage()->put(...)` |

No uses `Storage::` directamente para subidas de usuario — el prefijo, disco y URL se mantienen coherentes con `File::`.

---

## Configuración app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // o 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

Discos globales en `config/filesystems.config.php` y `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Subida con registro en base de datos

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

## Desde Request

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Adjuntar a un modelo

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Reemplazar un archivo anterior:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Solo disco (sin DB)

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

## Lectura y eliminación

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — métodos clave

| Método | Descripción |
|--------|-------------|
| `to($dir)` | Carpeta de destino |
| `group($name)` | Grupo lógico en DB |
| `thumb($w, $h)` | Miniatura de imagen |
| `maxSize('2MB')` | Tamaño máximo |
| `extensions('jpg,png')` | Extensiones permitidas |
| `disk('s3')` | Sobrescribir disco |
| `attach($model, $column)` | Establecer FK tras subida |
| `replaceOn($model, $column)` | Eliminar antiguo + subir nuevo |
| `save()` | Ejecutar → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // ruta absoluta
$result->record;    // FileModel
$result->error;     // mensaje de error
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// o por subida
File::upload('doc')->to('docs')->disk('s3')->save();
```

Archivos privados en S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Consejos

- Valida en FormRequest antes de `File::upload()`.
- `user_id` se rellena desde `Auth::id()`.
- Con `transport.file_storage => platform`, los archivos se comparten entre apps de la plataforma.

---


---

## Archivos grandes

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## Documentación relacionada

- [protocolo Pinion](./pinion.md)
- [Gestión de usuarios](./user-management.md)
- [Transport](./transport.md)
- [Validación](../basic/validation.md)
- [Tutorial galería de imágenes](../examples/gallery-app.md)

---

[← Volver al índice](../README.md)

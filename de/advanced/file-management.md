# Dateiverwaltung (File Management)

[← Zurück zur Übersicht](../README.md)

Upload und Speicherung laufen in Pinoox 3.x über ein einziges Portal: **`Pinoox\Portal\File`**. Die Metadaten liegen in `pincore_file` (oder einem gemeinsamen Transport-Scope), die physischen Dateien auf der Festplatte (lokal, S3, …).

---

## Einstiegspunkt

```php
use Pinoox\Portal\File;
```

| Bedarf | API |
|------|-----|
| Upload + DB-Eintrag + URL | `File::upload(...)->save()` |
| Finden / Löschen / URL | `File::find()`, `File::url()`, `File::remove()` |
| Direkter Festplattenzugriff | `File::storage()->put(...)` |

Verwenden Sie `Storage::` nicht direkt für Benutzer-Uploads — mit `File::` bleiben Präfix, Disk und URL konsistent.

---

## Konfiguration in app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // oder 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

Globale Disks in `config/filesystems.config.php` und `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Upload mit Datenbank-Eintrag

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

## Aus dem Request

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## An ein Model anhängen

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Eine vorherige Datei ersetzen:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Nur Festplatte (ohne DB)

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

## Lesen und Löschen

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — wichtigste Methoden

| Methode | Beschreibung |
|--------|-------------|
| `to($dir)` | Zielordner |
| `group($name)` | Logische Gruppe in der DB |
| `thumb($w, $h)` | Bild-Thumbnail |
| `maxSize('2MB')` | Maximale Dateigröße |
| `extensions('jpg,png')` | Erlaubte Dateiendungen |
| `disk('s3')` | Disk überschreiben |
| `attach($model, $column)` | FK nach dem Upload setzen |
| `replaceOn($model, $column)` | Alte Datei entfernen + neue hochladen |
| `save()` | Ausführen → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // absoluter Pfad
$result->record;    // FileModel
$result->error;     // Fehlermeldung
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// oder pro Upload
File::upload('doc')->to('docs')->disk('s3')->save();
```

Private Dateien auf S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Tipps

- Validieren Sie im FormRequest, bevor Sie `File::upload()` aufrufen.
- `user_id` wird aus `Auth::id()` befüllt.
- Mit `transport.file_storage => platform` werden Dateien zwischen Plattform-Apps geteilt.

---


---

## Große Dateien

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## Verwandte Dokumente

- [Pinion-Protokoll](./pinion.md)
- [Benutzerverwaltung (User management)](./user-management.md)
- [Transport](./transport.md)
- [Validierung (Validation)](../basic/validation.md)
- [Bildergalerie-Walkthrough](../examples/gallery-app.md)

---

[← Zurück zur Übersicht](../README.md)

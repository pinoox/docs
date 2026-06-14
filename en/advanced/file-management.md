# File Management

[← Back to index](../README.md)

Upload and storage in Pinoox 3.x go through a single portal: **`Pinoox\Portal\File`**. Metadata lives in `pincore_file` (or a shared transport scope) and physical files on disk (local, S3, …).

---

## Entry point

```php
use Pinoox\Portal\File;
```

| Need | API |
|------|-----|
| Upload + DB record + URL | `File::upload(...)->save()` |
| Find / delete / URL | `File::find()`, `File::url()`, `File::remove()` |
| Raw disk access | `File::storage()->put(...)` |

Do not use `Storage::` directly for user uploads — prefix, disk, and URL stay consistent with `File::`.

---

## app.php configuration

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // or 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

Global disks in `config/filesystems.config.php` and `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Upload with a database record

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

## From Request

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Attach to a model

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Replace a previous file:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Disk only (no DB)

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

## Read and delete

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — key methods

| Method | Description |
|--------|-------------|
| `to($dir)` | Destination folder |
| `group($name)` | Logical group in DB |
| `thumb($w, $h)` | Image thumbnail |
| `maxSize('2MB')` | Max file size |
| `extensions('jpg,png')` | Allowed extensions |
| `disk('s3')` | Override disk |
| `attach($model, $column)` | Set FK after upload |
| `replaceOn($model, $column)` | Remove old + upload new |
| `save()` | Execute → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // absolute path
$result->record;    // FileModel
$result->error;     // error message
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// or per upload
File::upload('doc')->to('docs')->disk('s3')->save();
```

Private files on S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Tips

- Validate in FormRequest before `File::upload()`.
- `user_id` is filled from `Auth::id()`.
- With `transport.file_storage => platform`, files are shared across platform apps.

---

## CLI (terminal)

List and maintain `FileModel` records and storage assets from the terminal:

| Command | Purpose |
|---------|---------|
| `file:list {package}` | List files with storage status |
| `file:show {file}` | Details by `file_id` or `hash_id` |
| `file:update {file}` | Update metadata JSON, access, or name |
| `file:delete {file}` | Default: DB row **and** storage (original + thumb) |
| `file:purge` | Bulk delete by group or age |

Delete modes for `file:delete`:

| Flag | Effect |
|------|--------|
| *(default)* | Delete model row; model hook removes storage |
| `--db-only` | Remove DB row only (`FileModel::withoutEvents`) |
| `--storage-only` | Remove files on disk/S3 only; keep DB row |
| `--force` | Skip confirmation |

```bash
php pinoox file:list com_my_shop
php pinoox file:show abc123hash
php pinoox file:delete 12 --db-only --force
php pinoox file:purge com_my_shop --group=avatar --force
```

Alias: `files` → `file:list`.

See [CLI reference](../start/cli-reference.md).

---

## Related docs

- [User management](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [Image gallery walkthrough](../examples/gallery-app.md)

---

[← Back to index](../README.md)

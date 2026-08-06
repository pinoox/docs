# Gestion des fichiers

[← Retour à l'index](../README.md)

Upload and storage go through **`Pinoox\Portal\File`**. Metadata lives in the file table (platform or app transport). Bytes live on **disks**.

```text
storage/                      ← root (always denied from the web)
├── local/{package}/…         ← disk `local`  (protect: lock)  → /file/{hash}
├── public/{package}/…        ← disk `public` (protect: unlock) → /storage/public/…
└── tmp/                      ← disk `temp`   (protect: lock)
```

| Call | Disk | Internal `file_access` | URL |
|------|------|------------------------|-----|
| `->public()` | `public` | `public` | `/storage/public/{package}/…` |
| `->private()` | `local` (or app `filesystem.disk`) | `private` | `/file/{hash}` |
| `->disk('s3')` | `s3` | `private` | `/file/{hash}` (or remote URL) |

Without `public()` / `private()`, mode follows **`filesystem.disk` only**: `public` ⇒ public uploads; any other disk ⇒ private.

`access()` is only for edge cases (e.g. a shared link while the file stays on a private disk). Prefer `disk()` / `public()` / `private()`.

---

## Entry point

```php
use Pinoox\Portal\File;
```

| Need | API |
|------|-----|
| Upload + DB + URL | `File::upload(...)->save()` |
| Find / delete / URL | `File::find()`, `File::url()`, `File::remove()` |
| Temporary signed URL | `File::temporaryUrl($file, 1800)` |
| Raw disk I/O | `File::storage()->put(...)` |

Do not use `Storage::` directly for user uploads if you need DB records, `hash_id`, and consistent URLs — use `File::`.

---

## app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform', // or 'local'
    ],
    'filesystem' => [
        'disk' => 'local',            // public disk ⇒ public uploads; else private
        'hash_length' => 8,           // hash_id length (4–50)
        'file_policy' => 'owner',     // default policy for private downloads
        'groups' => [
            // 'avatar' => 'public',
            // 'docs' => 'login',
            // 'admin' => 'role:admin',
            // 'staff' => 'roles:admin,editor',
            // 'reports' => 'permission:reports.view',
            // 'custom' => 'callback',
        ],
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

### Private download policies

| Policy | Who may download via `/file/{hash}` |
|--------|-------------------------------------|
| `owner` | Logged-in owner (`user_id`) |
| `login` / `auth` | Any logged-in user |
| `public` | Everyone (still via dispatcher if not on the public disk) |
| `callback` | Only if `FileDispatcher::auth` / `authFor` allows |
| `role:admin` | User with that `role_key` / `group_key` |
| `roles:a,b` | Any listed role |
| `permission:x.y` | `Access::can(...)` |
| `permissions:a,b` | Any listed permission |

Priority: valid temporary signature → public disk / `file_access=public` → package auth callback → `groups[file_group]` → `file_policy`.

```php
// boot.php — optional custom gate
use Pinoox\Component\File\FileDispatcher;

FileDispatcher::auth(function ($file, $user) {
    return $user && $user->user_id === $file->user_id;
});
```

---

## Global disks (`filesystems.config.php`)

```php
'local' => [
    'driver' => 'local',
    'root' => '~storage/local',
    'protect' => 'lock',          // default for every disk if omitted
],
'public' => [
    'driver' => 'local',
    'root' => '~storage/public',
    'protect' => 'unlock',        // must be set explicitly to serve over HTTP
    'url' => rtrim(env('APP_URL'), '/') . '/storage/public',
],
'temp' => [
    'driver' => 'local',
    'root' => '~storage/tmp',
    'protect' => 'lock',
],
```

`protect` defaults to **`lock`**. Only `unlock` opens the folder to the web (Apache/IIS stubs; Nginx/Caddy stubs are guides).

```env
FILESYSTEM_DISK=local
FILESYSTEM_LOCAL_ROOT=~storage/local
FILESYSTEM_PUBLIC_ROOT=~storage/public
FILESYSTEM_TEMP_ROOT=~storage/tmp
FILESYSTEM_PUBLIC_LINK=public/storage
FILE_HASH_LENGTH=8
FILE_LOOKUP_CACHE_TTL=60
FILE_XSENDFILE=auto
FILE_XACCEL=false
```

---

## Upload (with DB record)

Laravel-style **disk** is the source of truth. `public()` / `private()` are shortcuts.

```php
$result = File::upload('avatar')
    ->to('avatar')                  // → storage/public/{package}/avatar
    ->public()                      // or ->disk('public')
    ->group('avatar')
    ->thumb()
    ->maxSize('2MB')
    ->extensions('jpg,jpeg,png,webp')
    ->save();

if ($result->success) {
    $fileId = $result->id;
    $url = $result->url;            // /storage/public/...
    $thumb = $result->thumb;
}
```

Private file (File Dispatcher):

```php
$result = File::upload('invoice')
    ->to('invoices')                // → storage/local/{package}/invoices
    ->private()                     // or ->disk('local')
    ->group('invoice')
    ->save();

// URL: /file/{hash_id}   (thumb: /file/{hash_id}/thumb)
```

---

## From Request

```php
// 2nd argument = disk name (Laravel-style), not an access string
$result = $request->file('photo')->store('gallery', 'public')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();

// Shortcuts: 'private' → private disk; omit disk → app filesystem.disk
$request->store('doc', 'invoices', 'private')->save();
```

---

## Attach / replace on a model

```php
$result = File::upload('cover')
    ->to('posts')
    ->private()
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();

$result = File::upload('avatar')
    ->to('avatar')
    ->public()
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

## Read, temporary URL, delete

```php
$record = File::find($fileId);           // file_id or hash_id
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$temp = File::temporaryUrl($fileId, 1800); // S3 signed or /file/{hash}?expires=&signature=
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder

| Method | Description |
|--------|-------------|
| `to($dir)` | Folder under the package disk root |
| `disk('public'\|'local'\|'s3')` | Target disk (sets internal access) |
| `public()` / `private()` | Shortcuts for public / private disks |
| `group($name)` | Logical group (`filesystem.groups` policies) |
| `thumb($w, $h)` | Image thumbnail |
| `maxSize('2MB')` | Max size |
| `extensions('jpg,png')` | Allow-list |
| `attach($model, $column)` | Set FK after upload |
| `replaceOn($model, $column)` | Delete old file + upload |
| `access($mode)` | Edge-case override |
| `diskOnly()` | Skip DB record |
| `save()` | → `UploadResult` |

### UploadResult

```php
$result->success; // bool
$result->id;      // file_id
$result->url;
$result->thumb;
$result->path;
$result->record;  // FileModel
$result->error;
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// or per upload
File::upload('doc')->to('docs')->disk('s3')->save();

$url = File::temporaryUrl($file, now()->addHour());
// native:
$url = File::storage('s3')->temporaryUrl('path/doc.pdf', now()->addHour());
```

---

## CLI

```bash
php pinoox storage:setup
php pinoox storage:lock local
php pinoox storage:unlock public
php pinoox storage:link
php pinoox storage:unlink

php pinoox file:list com_my_shop
php pinoox file:show a1b2c3d4
php pinoox file:delete 12 --force
php pinoox file:purge com_my_shop --group=avatar --force
```

| Command | Purpose |
|---------|---------|
| `storage:setup` | Apply root deny + each disk’s `protect` |
| `storage:lock [disk]` | Force lock (omit disk → storage root) |
| `storage:unlock [disk]` | Force unlock (omit disk → public) |
| `storage:link` / `unlink` | Symlink from `filesystems.links` |
| `file:list` / `show` / `update` / `delete` / `purge` | Manage `FileModel` + assets |

See also [CLI reference](../start/cli-reference.md).

---

## Large files (Pinion)

For resume/progress beyond `upload_max_filesize`, use **[Pinion](./pinion.md)**. Chunks stage under `storage/pinion`; on `complete`, `StorageCompletion` publishes through `File` using session `disk` / `public()` / `private()`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

```php
protected function pinionDefaults(): array
{
    return [
        'destination' => 'uploads/media',
        'disk' => 'public',
        'mode' => 'auto',
        'record' => true,
        'group' => 'media',
    ];
}
```

---

## Tips

- Validate in a FormRequest before `File::upload()`.
- `user_id` is filled from `Auth::id()`.
- `transport.file_storage => platform` shares the file table across platform apps.
- Run `php pinoox storage:setup` after deploy so `protect` stubs exist.
- Public URLs are `/storage/public/{package}/…` (1:1 with the disk folder; Apache/PHP serve that path directly).

---

## Related

- [Pinion](./pinion.md)
- [User management](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [Image gallery example](../examples/gallery-app.md)

---

[← Retour à l'index](../README.md)

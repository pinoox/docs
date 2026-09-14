# Dosya yönetimi

[← Dizine dön](../README.md)

Upload and storage go through **`Pinoox\Portal\File`**. Metadata lives in the file table (platform or app transport). Bytes live on **disks**.

## Concepts (quick map)

| Concept | What it is |
|---------|------------|
| **Disk** | Where bytes live (`local`, `public`, `s3`, or a custom name) |
| **`protect`** | Web access to the folder: `lock` (default) or `unlock` |
| **`file_access`** | Internal DB flag (`public` / `private`), usually synced from the disk |
| **`hash_id`** | Short public id for private downloads (`/file/{hash}`) |
| **`file_policy` / `groups`** | Who may download a **private** file via the dispatcher |

```text
storage/                         ← project storage root (always web-denied)
├── local/{package}/…            ← disk `local`   protect:lock   → /file/{hash}
├── public/{package}/…           ← disk `public`  protect:unlock → /storage/public/{package}/…
├── tmp/                         ← disk `temp`    protect:lock
└── {your-disk}/{package}/…      ← custom disks follow the same pattern
```

| Call | Disk | Internal `file_access` | Typical URL |
|------|------|------------------------|-------------|
| `->public()` | `public` | `public` | `/storage/public/{package}/…` |
| `->private()` | `local` (or app `filesystem.disk`) | `private` | `/file/{hash}` |
| `->disk('s3')` | `s3` | `private` | `/file/{hash}` (or remote URL) |
| `->disk('contracts')` | custom | `private` unless disk is `public` | `/file/{hash}` (if locked) |

Without `public()` / `private()`, mode follows **`filesystem.disk` only**: disk name `public` ⇒ public uploads; anything else ⇒ private.

Prefer `disk()` / `public()` / `private()`. Use `access()` only for edge cases (e.g. a shared link while the file stays on a private disk).

---

## Entry point

```php
use Pinoox\Portal\File;
use Pinoox\Portal\Storage;
```

| Need | API |
|------|-----|
| Upload + DB + URL | `File::upload(...)->save()` |
| Find / delete / URL | `File::find()`, `File::url()`, `File::remove()` |
| Temporary signed URL | `File::temporaryUrl($file, 1800)` |
| Package-scoped disk | `Storage::app($package, 'local')` |
| Raw disk I/O | `File::storage('public')->put(...)` or `Storage::disk('local')` |

Do not use `Storage::` alone for user uploads if you need DB records, `hash_id`, and consistent URLs — use `File::`.

---

## Configure the app (`app.php`)

```php
return [
    'package' => 'com_acme_shop',
    'transport' => [
        // 'platform' = shared file table; 'local' = app-owned scope
        'file_storage' => 'platform',
    ],
    'filesystem' => [
        'disk' => 'local',            // default upload disk when you omit public()/private()
        'hash_length' => 8,           // hash_id length (4–50)
        'dispatcher' => 'file',       // private URL prefix → /file/{hash} (e.g. 'direct', 'link/to')
        'file_policy' => 'owner',     // default private-download policy
        'groups' => [
            'avatar' => 'public',                 // anyone via /file/{hash} if still private disk
            'invoice' => 'login',                 // any logged-in user
            'hr' => 'role:admin',
            'finance' => 'permissions:pay.view,pay.export',
            'custom' => 'callback',               // needs FileDispatcher::auth
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

**Priority:** valid temporary signature → public disk / `file_access=public` → package auth callback → `groups[file_group]` → `file_policy`.

```php
// boot.php — optional custom gate for policy "callback"
use Pinoox\Component\File\FileDispatcher;

FileDispatcher::auth(function ($file, $user) {
    return $user && (int) $user->user_id === (int) $file->user_id;
});

// Or only for this package:
FileDispatcher::authFor('com_acme_shop', function ($file, $user) {
    return $user && $user->hasPermission('files.download');
});
```

---

## Built-in disks (`filesystems.config.php`)

Global config ships with:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => '~storage/local',
        'protect' => 'lock',
        'visibility' => 'private',
    ],
    'public' => [
        'driver' => 'local',
        'root' => '~storage/public',
        'protect' => 'unlock',   // must be explicit — default is lock
        'url' => rtrim(env('APP_URL'), '/') . '/storage/public',
        'visibility' => 'public',
    ],
    'temp' => [
        'driver' => 'local',
        'root' => '~storage/tmp',
        'protect' => 'lock',
    ],
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        // ...
    ],
],
```

`protect` defaults to **`lock`**. Only `unlock` makes the folder web-readable (Apache/IIS stubs; Nginx/Caddy stubs are guides).

```env
FILESYSTEM_DISK=local
FILESYSTEM_LOCAL_ROOT=~storage/local
FILESYSTEM_PUBLIC_ROOT=~storage/public
FILESYSTEM_TEMP_ROOT=~storage/tmp
FILESYSTEM_PUBLIC_URL=   # defaults to {APP_URL}/storage/public
FILE_HASH_LENGTH=8
FILE_DISPATCHER=file     # private download prefix: /file/{hash}
FILE_LOOKUP_CACHE_TTL=60
```

After changing disks or on deploy:

```bash
php pinoox storage:setup
```

---

## Create a custom disk in your app

You usually add disks in one of these ways.

### 1) Project config (recommended for permanent disks)

Add or override in the project `config/filesystems.config.php` (or your Pinker override of `~filesystems`):

```php
// config/filesystems.config.php  (merge/override disks)
return [
    // ... keep other keys from core, or full file
    'disks' => [
        // ...existing local/public/temp/s3...

        // Private contracts vault (locked)
        'contracts' => [
            'driver' => 'local',
            'root' => '~storage/contracts',
            'protect' => 'lock',
            'visibility' => 'private',
            'throw' => false,
        ],

        // Extra public CDN-like folder (unlocked)
        'media' => [
            'driver' => 'local',
            'root' => '~storage/media',
            'protect' => 'unlock',
            'url' => rtrim(env('APP_URL'), '/') . '/storage/media',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],
];
```

Then create folders and apply protect stubs:

```bash
mkdir -p storage/contracts storage/media
php pinoox storage:setup
# or per disk:
php pinoox storage:lock contracts
php pinoox storage:unlock media
```

**Package scoping:** for local drivers, `File::upload(...)->disk('contracts')` and `Storage::app($package, 'contracts')` store under:

`storage/contracts/{package}/…`

Same rule as `local` / `public`.

### 2) Register at runtime in `boot.php`

Useful for app-specific disks without editing the global config file:

```php
// apps/com_acme_shop/boot.php
use Pinoox\Portal\Config;

Config::name('~filesystems')->set('disks.contracts', [
    'driver' => 'local',
    'root' => '~storage/contracts',
    'protect' => 'lock',
    'visibility' => 'private',
]);
```

Call `storage:setup` (or `StorageSetup::ensureDisk('contracts')`) so `protect` stubs exist on first use / deploy.

### 3) One-off disk with `Storage::build()`

When you need a disposable root (no named disk):

```php
use Pinoox\Portal\Storage;

$disk = Storage::build([
    'driver' => 'local',
    'root' => path('~storage/exports'),
    'protect' => 'lock',
]);

$disk->put('report.csv', $csv);
```

Or register it under a name for the request lifecycle:

```php
Storage::set('exports', Storage::build([
    'driver' => 'local',
    'root' => path('~storage/exports'),
    'protect' => 'lock',
]));

File::upload($file)->to('reports')->disk('exports')->save();
```

### Use the custom disk

```php
// Private upload → /file/{hash} + file_policy / groups
File::upload($request->file('pdf'))
    ->to('2026')
    ->disk('contracts')
    ->group('invoice')
    ->extensions('pdf')
    ->maxSize('20MB')
    ->save();

// Public custom disk (protect:unlock + url set)
File::upload($request->file('banner'))
    ->to('home')
    ->disk('media')
    ->save();
// → URL under /storage/media/{package}/home/...
```

Set the app default disk so omitted `public()`/`private()` uses your disk:

```php
// app.php
'filesystem' => [
    'disk' => 'contracts',
],
```

---

## Examples

### Example A — Public avatar from a controller

```php
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\File;
use Pinoox\Portal\View;

class ProfileController extends Controller
{
    public function uploadAvatar()
    {
        $result = File::upload('avatar')
            ->to('avatars')
            ->public()                 // storage/public/{package}/avatars
            ->group('avatar')
            ->thumb(256, 256)
            ->maxSize('2MB')
            ->extensions('jpg,jpeg,png,webp')
            ->replaceOn(auth()->user(), 'avatar_id')
            ->save();

        if (!$result->success) {
            return View::json(['error' => $result->error], 422);
        }

        return View::json([
            'file_id' => $result->id,
            'url' => $result->url,           // /storage/public/...
            'thumb' => $result->thumb,
        ]);
    }
}
```

### Example B — Private invoice PDF (login required)

```php
// app.php → filesystem.groups.invoice = 'login'

$result = File::upload($request->file('pdf'))
    ->to('invoices/' . date('Y'))
    ->private()                    // storage/local/{package}/invoices/2026
    ->group('invoice')
    ->extensions('pdf')
    ->maxSize('15MB')
    ->metadata(['order_id' => $orderId])
    ->save();

// Share link for the owner / logged-in users:
$url = File::url($result->id);                 // /file/{hash}
$temp = File::temporaryUrl($result->id, 3600); // signed, expires in 1h
```

### Example C — From `Request` (Laravel-style disk argument)

```php
// 2nd arg = disk name (not an access string)
$result = $request->file('photo')->store('gallery', 'public')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();

// Shortcuts: 'private' → private disk; omit disk → app filesystem.disk
$request->store('contract', '2026', 'contracts')->group('invoice')->save();
```

### Example D — Attach to a model

```php
$result = File::upload('cover')
    ->to('posts')
    ->private()
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();

// Later
$post->cover_id;                 // file_id
File::url($post->cover_id);      // /file/{hash}
```

### Example E — Disk only (no DB row)

```php
$result = File::upload($zip)
    ->to('imports')
    ->disk('local')
    ->diskOnly()
    ->extensions('zip')
    ->save();

if ($result->success) {
    $absolute = $result->path;
}
```

### Example F — Read, list, delete

```php
$record = File::find($fileId);        // file_id or hash_id
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);                // DB + storage (via model hooks)
```

### Example G — S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// or per upload
File::upload('doc')->to('docs')->disk('s3')->save();

$url = File::temporaryUrl($file, now()->addHour());
// native Flysystem:
$url = File::storage('s3')->temporaryUrl('path/doc.pdf', now()->addHour());
```

---

## UploadBuilder reference

| Method | Description |
|--------|-------------|
| `to($dir)` | Folder under the package disk root |
| `disk('public'\|'local'\|'s3'\|…)` | Target disk (sets internal access) |
| `public()` / `private()` | Shortcuts for public / private disks |
| `group($name)` | Logical group → `filesystem.groups` policy |
| `thumb($w, $h)` | Image thumbnail |
| `maxSize('2MB')` | Max size |
| `extensions('jpg,png')` | Allow-list |
| `metadata([...])` | JSON metadata on the row |
| `attach($model, $column)` | Set FK after upload |
| `replaceOn($model, $column)` | Delete old file + upload |
| `access($mode)` | Edge-case override |
| `diskOnly()` | Skip DB record |
| `package($name)` | Override package scope |
| `save()` | → `UploadResult` |

### UploadResult

```php
$result->success; // bool
$result->id;      // file_id
$result->url;
$result->thumb;
$result->path;    // absolute path when available
$result->record;  // FileModel
$result->error;
```

---

## CLI

```bash
php pinoox storage:setup
php pinoox storage:lock local
php pinoox storage:lock contracts
php pinoox storage:unlock public
php pinoox storage:unlock media
php pinoox storage:link
php pinoox storage:unlink

php pinoox file:list com_acme_shop
php pinoox file:show a1b2c3d4
php pinoox file:delete 12 --force
php pinoox file:purge com_acme_shop --group=avatar --force
```

| Command | Purpose |
|---------|---------|
| `storage:setup` | Apply root deny + each disk’s `protect` |
| `storage:lock [disk]` | Force lock (omit disk → storage root) |
| `storage:unlock [disk]` | Force unlock (omit disk → public) |
| `storage:link` / `unlink` | Symlink from `filesystems.links` |
| `file:*` | Manage `FileModel` + assets |

See also [CLI reference](../start/cli-reference.md).

---

## Large files (Pinion)

For resume/progress beyond `upload_max_filesize`, use **[Pinion](./pinion.md)**. Chunks stage under `storage/pinion`; on `complete`, files publish through `File` using session `disk` / `public()` / `private()`.

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
        'disk' => 'public',       // or 'contracts', 's3', …
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
- Run `php pinoox storage:setup` after adding disks or deploying.
- Public URLs are `/storage/public/{package}/…` (1:1 with the disk folder).
- Name custom disks after their folder when possible (`contracts` → `storage/contracts`).

---

## Related

- [Pinion](./pinion.md)
- [User management](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [Image gallery example](../examples/gallery-app.md)
- [Boot and events](./boot-and-events.md)

---

[← Dizine dön](../README.md)

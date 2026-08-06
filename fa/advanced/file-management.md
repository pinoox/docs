# مدیریت فایل

[← بازگشت به فهرست](../README.md)

آپلود و ذخیره‌سازی از **`Pinoox\Portal\File`** انجام می‌شود. متادیتا در جدول فایل (platform یا transport اپ) و بایت‌ها روی **دیسک** هستند.

```text
storage/                      ← ریشه (همیشه از وب deny)
├── local/{package}/…         ← دیسک `local`  (protect: lock)  → /file/{hash}
├── public/{package}/…        ← دیسک `public` (protect: unlock) → /storage/public/…
└── tmp/                      ← دیسک `temp`   (protect: lock)
```

| فراخوانی | دیسک | `file_access` داخلی | URL |
|------|------|------------------------|-----|
| `->public()` | `public` | `public` | `/storage/public/{package}/…` |
| `->private()` | `local` (یا `filesystem.disk` اپ) | `private` | `/file/{hash}` |
| `->disk('s3')` | `s3` | `private` | `/file/{hash}` (یا URL ریموت) |

بدون `public()` / `private()`، حالت فقط از **`filesystem.disk`** می‌آید: `public` ⇒ آپلود عمومی؛ هر دیسک دیگر ⇒ خصوصی.

`access()` فقط برای موارد خاص است (مثلاً لینک اشتراکی روی دیسک خصوصی). ترجیح با `disk()` / `public()` / `private()`.

---

## نقطه ورود

```php
use Pinoox\Portal\File;
```

| نیاز | API |
|------|-----|
| آپلود + DB + URL | `File::upload(...)->save()` |
| پیدا کردن / حذف / URL | `File::find()`, `File::url()`, `File::remove()` |
| URL موقت امضاشده | `File::temporaryUrl($file, 1800)` |
| I/O خام دیسک | `File::storage()->put(...)` |

برای آپلود کاربر اگر رکورد DB، `hash_id` و URL یکدست می‌خواهید، مستقیم از `Storage::` استفاده نکنید — از `File::` استفاده کنید.

---

## app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform', // یا 'local'
    ],
    'filesystem' => [
        'disk' => 'local',            // دیسک public ⇒ آپلود عمومی؛ وگرنه خصوصی
        'hash_length' => 8,           // طول hash_id (۴–۵۰)
        'file_policy' => 'owner',     // سیاست پیش‌فرض دانلود خصوصی
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

### سیاست‌های دانلود خصوصی

| سیاست | چه کسی از `/file/{hash}` می‌تواند بگیرد |
|--------|-------------------------------------|
| `owner` | مالک لاگین‌شده (`user_id`) |
| `login` / `auth` | هر کاربر لاگین‌شده |
| `public` | همه (اگر روی دیسک public نباشد، همچنان از dispatcher) |
| `callback` | فقط اگر `FileDispatcher::auth` / `authFor` اجازه دهد |
| `role:admin` | کاربر با آن `role_key` / `group_key` |
| `roles:a,b` | هر نقش لیست‌شده |
| `permission:x.y` | `Access::can(...)` |
| `permissions:a,b` | هر permission لیست‌شده |

اولویت: امضای موقت معتبر → دیسک public / `file_access=public` → callback پکیج → `groups[file_group]` → `file_policy`.

```php
// boot.php — گیت سفارشی اختیاری
use Pinoox\Component\File\FileDispatcher;

FileDispatcher::auth(function ($file, $user) {
    return $user && $user->user_id === $file->user_id;
});
```

---

## دیسک‌های سراسری (`filesystems.config.php`)

```php
'local' => [
    'driver' => 'local',
    'root' => '~storage/local',
    'protect' => 'lock',          // اگر حذف شود هم پیش‌فرض lock است
],
'public' => [
    'driver' => 'local',
    'root' => '~storage/public',
    'protect' => 'unlock',        // برای سرو HTTP باید صریح باشد
    'url' => rtrim(env('APP_URL'), '/') . '/storage/public',
],
'temp' => [
    'driver' => 'local',
    'root' => '~storage/tmp',
    'protect' => 'lock',
],
```

`protect` پیش‌فرض **`lock`** است. فقط `unlock` پوشه را برای وب باز می‌کند (stubهای Apache/IIS؛ Nginx/Caddy راهنما هستند).

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

## آپلود (با رکورد DB)

**disk** به سبک لاراول منبع حقیقت است. `public()` / `private()` میانبرند.

```php
$result = File::upload('avatar')
    ->to('avatar')                  // → storage/public/{package}/avatar
    ->public()                      // یا ->disk('public')
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

فایل خصوصی (File Dispatcher):

```php
$result = File::upload('invoice')
    ->to('invoices')                // → storage/local/{package}/invoices
    ->private()                     // یا ->disk('local')
    ->group('invoice')
    ->save();

// URL: /file/{hash_id}   (thumb: /file/{hash_id}/thumb)
```

---

## از Request

```php
// آرگومان دوم = نام دیسک (Laravel-style)، نه access
$result = $request->file('photo')->store('gallery', 'public')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();

// میانبر: 'private' → دیسک خصوصی؛ بدون disk → filesystem.disk اپ
$request->store('doc', 'invoices', 'private')->save();
```

---

## اتصال / جایگزینی روی مدل

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

## فقط دیسک (بدون DB)

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

## خواندن، URL موقت، حذف

```php
$record = File::find($fileId);           // file_id یا hash_id
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$temp = File::temporaryUrl($fileId, 1800); // امضای S3 یا /file/{hash}?expires=&signature=
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder

| متد | توضیح |
|--------|-------------|
| `to($dir)` | پوشه زیر ریشه دیسک پکیج |
| `disk('public'\|'local'\|'s3')` | دیسک هدف (access داخلی را ست می‌کند) |
| `public()` / `private()` | میانبر دیسک عمومی / خصوصی |
| `group($name)` | گروه منطقی (سیاست‌های `filesystem.groups`) |
| `thumb($w, $h)` | تصویر بندانگشتی |
| `maxSize('2MB')` | سقف حجم |
| `extensions('jpg,png')` | پسوندهای مجاز |
| `attach($model, $column)` | ست کردن FK بعد از آپلود |
| `replaceOn($model, $column)` | حذف فایل قبلی + آپلود |
| `access($mode)` | override مورد خاص |
| `diskOnly()` | بدون رکورد DB |
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

// یا per upload
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

| دستور | کاربرد |
|---------|---------|
| `storage:setup` | deny ریشه + `protect` هر دیسک |
| `storage:lock [disk]` | قفل اجباری (بدون disk → ریشه storage) |
| `storage:unlock [disk]` | باز کردن اجباری (بدون disk → public) |
| `storage:link` / `unlink` | symlink از `filesystems.links` |
| `file:list` / `show` / `update` / `delete` / `purge` | مدیریت `FileModel` و فایل‌ها |

همچنین [مرجع CLI](../start/cli-reference.md).

---

## فایل‌های بزرگ (Pinion)

برای resume/progress بالاتر از `upload_max_filesize` از **[Pinion](./pinion.md)** استفاده کنید. چانک‌ها زیر `storage/pinion`؛ در `complete`، `StorageCompletion` با `disk` / `public()` / `private()` از طریق `File` منتشر می‌کند.

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

## نکات

- قبل از `File::upload()` در FormRequest اعتبارسنجی کنید.
- `user_id` از `Auth::id()` پر می‌شود.
- `transport.file_storage => platform` جدول فایل را بین اپ‌های platform مشترک می‌کند.
- بعد از دیپلوی `php pinoox storage:setup` را اجرا کنید تا stubهای `protect` ساخته شوند.
- URL عمومی `/storage/public/{package}/…` است (یکی با پوشه دیسک؛ Apache/PHP همان مسیر را مستقیم سرو می‌کنند).

---

## مرتبط

- [Pinion](./pinion.md)
- [مدیریت کاربر](./user-management.md)
- [Transport](./transport.md)
- [اعتبارسنجی](../basic/validation.md)
- [نمونه گالری تصاویر](../examples/gallery-app.md)

---

[← بازگشت به فهرست](../README.md)

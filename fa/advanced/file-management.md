# مدیریت فایل

[← بازگشت به فهرست](../README.md)

آپلود و ذخیره‌سازی از **`Pinoox\Portal\File`** انجام می‌شود. متادیتا در جدول فایل (platform یا transport اپ) و بایت‌ها روی **دیسک** هستند.

## مفاهیم (نقشه سریع)

| مفهوم | یعنی چه |
|---------|------------|
| **Disk** | محل فیزیکی فایل (`local`، `public`، `s3` یا نام سفارشی) |
| **`protect`** | دسترسی وب به پوشه: `lock` (پیش‌فرض) یا `unlock` |
| **`file_access`** | فلگ داخلی DB (`public` / `private`)، معمولاً از دیسک sync می‌شود |
| **`hash_id`** | شناسه کوتاه برای دانلود خصوصی (`/file/{hash}`) |
| **`file_policy` / `groups`** | چه کسی فایل **خصوصی** را از dispatcher بگیرد |

```text
storage/                         ← ریشه storage پروژه (از وب deny)
├── local/{package}/…            ← دیسک `local`   protect:lock   → /file/{hash}
├── public/{package}/…           ← دیسک `public`  protect:unlock → /storage/public/{package}/…
├── tmp/                         ← دیسک `temp`    protect:lock
└── {دیسک-شما}/{package}/…       ← دیسک‌های سفارشی همین الگو را دارند
```

| فراخوانی | دیسک | `file_access` داخلی | URL معمول |
|------|------|------------------------|-------------|
| `->public()` | `public` | `public` | `/storage/public/{package}/…` |
| `->private()` | `local` (یا `filesystem.disk` اپ) | `private` | `{app}/file/{hash}` |
| `->disk('s3')` | `s3` | `private` | `{app}/file/{hash}` (یا URL ریموت) |
| `->disk('media')` | سفارشی، `protect:unlock` | `public` | `/storage/media/{package}/…` |
| `->disk('contracts')` | سفارشی، `protect:lock` | `private` | `{app}/file/{hash}` |

بدون `public()` / `private()`، حالت از **`filesystem.disk`** می‌آید: دیسک باز/عمومی ⇒ آپلود عمومی؛ هر چیز دیگر ⇒ خصوصی.

ترجیح با `disk()` / `public()` / `private()`. `access()` فقط برای موارد خاص (مثلاً لینک اشتراکی روی دیسک خصوصی).

---

## نقطه ورود

```php
use Pinoox\Portal\File;
use Pinoox\Portal\Storage;
```

| نیاز | API |
|------|-----|
| آپلود + DB + URL | `File::upload(...)->save()` |
| پیدا کردن / حذف / URL | `File::find()`, `File::url()`, `File::remove()` |
| لینک دانلود (تشخیص خودکار دیسک) | `file_url($file)`، `url()->file($file)`، `Url::file($file)` |
| لینک بندانگشتی | `file_thumb($file)`، `url()->fileThumb($file)` |
| URL موقت امضاشده | `File::temporaryUrl($file, 1800)`، `file_temporary_url($file, 1800)` |
| دیسک scoped به پکیج | `Storage::app($package, 'local')` |
| I/O خام دیسک | `File::storage('public')->put(...)` یا `Storage::disk('local')` |

اگر رکورد DB، `hash_id` و URL یکدست می‌خواهید، برای آپلود کاربر فقط از `Storage::` استفاده نکنید — از `File::` استفاده کنید.

---

## لینک دانلود (تشخیص خودکار)

موقع ساخت لینک لازم نیست public یا private بودن را خودتان انتخاب کنید. `file_id`، `hash_id` یا `FileModel` را بدهید — پینوکس دیسک را از روی کانفیگ تشخیص می‌دهد:

| دیسک | تشخیص | URL |
|------|--------|-----|
| `public` داخلی | نام دیسک | `/storage/public/{package}/…` |
| سفارشی باز (`protect: unlock`) | کانفیگ دیسک | `/storage/{disk}/{package}/…` |
| ریموت عمومی (`visibility: public` + `url`) | کانفیگ دیسک | URL ریموت / CDN |
| قفل‌شده (`local`، `temp`، …) | بقیه موارد | دیسپچر اپ مالک `{app}/file/{hash}` |

```php
use Pinoox\Portal\File;
use Pinoox\Portal\Url;

// همه همین resolver هستند — هر سبکی راحت‌تر است
File::url($fileId);
file_url($fileId);
url()->file($fileId);
Url::file($fileId);

File::thumb($fileId);
file_thumb($fileId);
url()->fileThumb($fileId);

File::temporaryUrl($fileId, 1800);
file_temporary_url($fileId, 1800);
url()->temporaryFile($fileId, 1800);
```

Twig:

```twig
<a href="{{ url().file(post.cover_id) }}">دانلود</a>
<img src="{{ file_thumb(post.cover_id) }}" alt="">
```

`$result->url` بعد از `File::upload(...)->save()` همین resolver است.

---

## تنظیم اپ (`app.php`)

```php
return [
    'package' => 'com_acme_shop',
    'transport' => [
        // 'platform' = جدول فایل مشترک؛ 'local' = scope خود اپ
        'file_storage' => 'platform',
    ],
    'filesystem' => [
        'disk' => 'local',            // دیسک پیش‌فرض وقتی public()/private() ننویسید
        'hash_length' => 8,           // طول hash_id (۴–۵۰)
        'dispatcher' => 'file',       // پیشوند URL خصوصی → /file/{hash} (مثلاً 'direct' یا 'link/to')
        'file_policy' => 'owner',     // سیاست پیش‌فرض دانلود خصوصی
        'groups' => [
            'avatar' => 'public',
            'invoice' => 'login',
            'hr' => 'role:admin',
            'finance' => 'permissions:pay.view,pay.export',
            'custom' => 'callback',
        ],
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

### پیشوند سفارشی URL دیسپچر

دانلود خصوصی به‌طور پیش‌فرض `/file/{hash}` است. در هر اپ (یا سراسری با `FILE_DISPATCHER` / `~filesystems.dispatcher`) عوض کنید:

```php
'filesystem' => [
    'dispatcher' => 'direct',   // → /direct/{hash} و /direct/{hash}/thumb
    // 'dispatcher' => 'link/to', // → /link/to/{hash}
],
```

`File::url()` و URL موقت از پیشوند **پکیج مالک** فایل (`file.app`) استفاده می‌کنند.

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

**اولویت:** امضای موقت معتبر → دیسک public / `file_access=public` → callback پکیج → `groups[file_group]` → `file_policy`.

```php
// boot.php — گیت سفارشی برای سیاست callback
use Pinoox\Component\File\FileDispatcher;

FileDispatcher::auth(function ($file, $user) {
    return $user && (int) $user->user_id === (int) $file->user_id;
});

FileDispatcher::authFor('com_acme_shop', function ($file, $user) {
    return $user && $user->hasPermission('files.download');
});
```

---

## دیسک‌های داخلی (`filesystems.config.php`)

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
        'protect' => 'unlock',   // باید صریح باشد — پیش‌فرض lock است
        'url' => rtrim(env('APP_URL'), '/') . '/storage/public',
        'visibility' => 'public',
    ],
    'temp' => [
        'driver' => 'local',
        'root' => '~storage/tmp',
        'protect' => 'lock',
    ],
    's3' => [ /* ... */ ],
],
```

`protect` پیش‌فرض **`lock`** است. فقط `unlock` پوشه را برای وب باز می‌کند.

```env
FILESYSTEM_DISK=local
FILESYSTEM_LOCAL_ROOT=~storage/local
FILESYSTEM_PUBLIC_ROOT=~storage/public
FILESYSTEM_TEMP_ROOT=~storage/tmp
FILESYSTEM_PUBLIC_URL=
FILE_HASH_LENGTH=8
FILE_DISPATCHER=file
```

بعد از تغییر دیسک‌ها یا دیپلوی:

```bash
php pinoox storage:setup
```

---

## ساخت دیسک سفارشی در اپ

### ۱) کانفیگ پروژه (پیشنهادی برای دیسک دائمی)

در `config/filesystems.config.php` پروژه (یا override پینکر `~filesystems`):

```php
return [
    'disks' => [
        // ... local/public/temp/s3 موجود

        'contracts' => [
            'driver' => 'local',
            'root' => '~storage/contracts',
            'protect' => 'lock',
            'visibility' => 'private',
            'throw' => false,
        ],

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

```bash
mkdir -p storage/contracts storage/media
php pinoox storage:setup
php pinoox storage:lock contracts
php pinoox storage:unlock media
```

برای درایور local، فایل‌ها زیر `storage/{disk}/{package}/…` می‌روند.

### ۲) ثبت در `boot.php`

```php
use Pinoox\Portal\Config;

Config::name('~filesystems')->set('disks.contracts', [
    'driver' => 'local',
    'root' => '~storage/contracts',
    'protect' => 'lock',
    'visibility' => 'private',
]);
```

### ۳) دیسک یک‌بارمصرف با `Storage::build()`

```php
use Pinoox\Portal\Storage;

$disk = Storage::build([
    'driver' => 'local',
    'root' => path('~storage/exports'),
    'protect' => 'lock',
]);

Storage::set('exports', $disk);
File::upload($file)->to('reports')->disk('exports')->save();
```

### استفاده

```php
File::upload($request->file('pdf'))
    ->to('2026')
    ->disk('contracts')
    ->group('invoice')
    ->extensions('pdf')
    ->save();

File::upload($request->file('banner'))
    ->to('home')
    ->disk('media')
    ->save();
```

پیش‌فرض اپ:

```php
'filesystem' => ['disk' => 'contracts'],
```

---

## مثال‌ها

### الف — آواتار عمومی

```php
$result = File::upload('avatar')
    ->to('avatars')
    ->public()
    ->group('avatar')
    ->thumb(256, 256)
    ->maxSize('2MB')
    ->extensions('jpg,jpeg,png,webp')
    ->replaceOn(auth()->user(), 'avatar_id')
    ->save();
```

### ب — فاکتور خصوصی (login)

```php
// app.php → groups.invoice = 'login'

$result = File::upload($request->file('pdf'))
    ->to('invoices/' . date('Y'))
    ->private()
    ->group('invoice')
    ->extensions('pdf')
    ->save();

$url = File::url($result->id);
$temp = File::temporaryUrl($result->id, 3600);
```

### ج — از Request

```php
$result = $request->file('photo')->store('gallery', 'public')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();

$request->store('contract', '2026', 'contracts')->group('invoice')->save();
```

### د — اتصال به مدل

```php
File::upload('cover')
    ->to('posts')
    ->private()
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

### ه — فقط دیسک (بدون DB)

```php
File::upload($zip)->to('imports')->disk('local')->diskOnly()->extensions('zip')->save();
```

### و — خواندن / حذف

```php
$record = File::find($fileId);
File::url($fileId);
File::listByGroup('avatar');
File::remove($fileId);
```

### ز — S3

```php
File::upload('doc')->to('docs')->disk('s3')->save();
File::temporaryUrl($file, now()->addHour());
```

---

## مرجع UploadBuilder

| متد | توضیح |
|--------|-------------|
| `to($dir)` | پوشه زیر ریشه دیسک پکیج |
| `disk(...)` | دیسک هدف |
| `public()` / `private()` | میانبر |
| `group($name)` | گروه → سیاست `filesystem.groups` |
| `thumb` / `maxSize` / `extensions` | محدودیت‌ها و بندانگشتی |
| `attach` / `replaceOn` | اتصال به مدل |
| `access($mode)` | override مورد خاص |
| `diskOnly()` | بدون رکورد DB |
| `save()` | → `UploadResult` |

---

## CLI

```bash
php pinoox storage:setup
php pinoox storage:lock contracts
php pinoox storage:unlock media
php pinoox file:list com_acme_shop
php pinoox file:show a1b2c3d4
```

جزئیات بیشتر: [مرجع CLI](../start/cli-reference.md).

---

## فایل‌های بزرگ (Pinion)

برای resume/progress از **[Pinion](./pinion.md)** استفاده کنید. در `pinionDefaults` می‌توانید `'disk' => 'public'` یا دیسک سفارشی بگذارید.

---

## نکات

- قبل از آپلود در FormRequest اعتبارسنجی کنید.
- بعد از افزودن دیسک، `storage:setup` را اجرا کنید.
- از `file_url($id)` / `url()->file($id)` استفاده کنید — دیسک عمومی `/storage/{disk}/{package}/…`؛ دیسک قفل‌شده `{app}/file/{hash}`.
- نام دیسک سفارشی را با پوشه‌اش هم‌نام کنید (`contracts` → `storage/contracts`).

---

## مرتبط

- [Pinion](./pinion.md)
- [مدیریت کاربر](./user-management.md)
- [Transport](./transport.md)
- [اعتبارسنجی](../basic/validation.md)
- [نمونه گالری](../examples/gallery-app.md)

---

[← بازگشت به فهرست](../README.md)

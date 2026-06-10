# مدیریت فایل

آپلود و ذخیره فایل در پینوکس 3.x از یک Portal واحد انجام می‌شود: **`Pinoox\Portal\File`**. متادیتا در `pincore_file` (یا scope مشترک transport) و فایل فیزیکی روی دیسک (local، S3، …) نگه‌داری می‌شود.

---

## نقطه ورود

```php
use Pinoox\Portal\File;
```

| نیاز | API |
|------|-----|
| آپلود + رکورد DB + URL | `File::upload(...)->save()` |
| جستجو / حذف / URL | `File::find()`, `File::url()`, `File::remove()` |
| دسترسی خام به دیسک | `File::storage()->put(...)` |

برای آپلود کاربر از `Storage::` مستقیم استفاده نکنید — prefix، دیسک و URL با `File::` یکپارچه می‌ماند.

---

## تنظیم app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // یا 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

دیسک‌های global در `config/filesystems.config.php` و `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## آپلود با رکورد دیتابیس

```php
$result = File::upload('avatar')
    ->to('uploads/avatar')
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

## از Request

```php
$result = $request->store('photo', 'uploads/gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## اتصال به مدل

```php
$result = File::upload('cover')
    ->to('uploads/posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

جایگزینی فایل قبلی:

```php
$result = File::upload('avatar')
    ->to('uploads/avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## فقط دیسک (بدون DB)

```php
$result = File::upload('file')
    ->to('uploads/apps')
    ->diskOnly()
    ->save();

if ($result->success) {
    $path = $result->path;
}
```

---

## خواندن و حذف

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — متدهای مهم

| متد | توضیح |
|-----|-------|
| `to($dir)` | پوشه مقصد |
| `group($name)` | گروه منطقی در DB |
| `thumb($w, $h)` | بندانگشتی تصویر |
| `maxSize('2MB')` | حداکثر حجم |
| `extensions('jpg,png')` | پسوند مجاز |
| `disk('s3')` | override دیسک |
| `attach($model, $column)` | ست کردن FK بعد از آپلود |
| `replaceOn($model, $column)` | حذف قبلی + آپلود جدید |
| `save()` | اجرا → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // مسیر مطلق
$result->record;    // FileModel
$result->error;     // پیام خطا
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// یا per-upload
File::upload('doc')->to('uploads/docs')->disk('s3')->save();
```

فایل‌های private روی S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## نکات

- قبل از `File::upload()` اعتبارسنجی را در FormRequest انجام دهید.
- `user_id` آپلودکننده از `Auth::id()` پر می‌شود.
- با `transport.file_storage => platform` فایل‌ها بین اپ‌های پلتفرم مشترک می‌مانند.

---

## مستندات مرتبط

- [مدیریت کاربران](./user-management.md)
- [Transport](../../pinoox%20docs/pinoox-transport.md)
- [Validation](../basic/validation.md)

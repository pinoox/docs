# إدارة الملفات (File Management)

[← العودة إلى الفهرس](../README.md)

يمر الرفع والتخزين في Pinoox 3.x عبر بوابة واحدة: **`Pinoox\Portal\File`**. تُحفَظ البيانات الوصفية (Metadata) في `pincore_file` (أو في نطاق نقل مشترك Transport)، بينما تُخزَّن الملفات الفعلية على القرص (محلي، S3، …).

---

## نقطة الدخول

```php
use Pinoox\Portal\File;
```

| الحاجة | واجهة API |
|------|-----|
| رفع + سجل في قاعدة البيانات + رابط URL | `File::upload(...)->save()` |
| بحث / حذف / رابط URL | `File::find()`، `File::url()`، `File::remove()` |
| وصول مباشر إلى القرص | `File::storage()->put(...)` |

لا تستخدم `Storage::` مباشرةً لملفات المستخدمين المرفوعة — فالبادئة (Prefix) والقرص (Disk) والرابط (URL) تبقى متّسقة مع `File::`.

---

## إعدادات app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // أو 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

الأقراص العامة (Global disks) في `config/filesystems.config.php` و `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## الرفع مع سجل في قاعدة البيانات

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

## من الطلب (Request)

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## الإرفاق بنموذج (Model)

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

استبدال ملف سابق:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## القرص فقط (بدون قاعدة بيانات)

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

## القراءة والحذف

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — الدوال الأساسية

| الدالة | الوصف |
|--------|-------------|
| `to($dir)` | مجلد الوجهة |
| `group($name)` | مجموعة منطقية في قاعدة البيانات |
| `thumb($w, $h)` | صورة مصغّرة (Thumbnail) |
| `maxSize('2MB')` | الحد الأقصى لحجم الملف |
| `extensions('jpg,png')` | الامتدادات المسموح بها |
| `disk('s3')` | تجاوز القرص الافتراضي |
| `attach($model, $column)` | تعيين المفتاح الأجنبي (FK) بعد الرفع |
| `replaceOn($model, $column)` | حذف القديم + رفع الجديد |
| `save()` | التنفيذ → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // المسار المطلق
$result->record;    // FileModel
$result->error;     // رسالة الخطأ
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// أو لكل عملية رفع على حدة
File::upload('doc')->to('docs')->disk('s3')->save();
```

الملفات الخاصة (Private) على S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## نصائح

- تحقّق من الصحة (Validate) في FormRequest قبل `File::upload()`.
- يُملأ `user_id` من `Auth::id()`.
- مع `transport.file_storage => platform`، تُشارك الملفات بين تطبيقات المنصة.

---

## وثائق ذات صلة

- [إدارة المستخدمين](./user-management.md)
- [النقل (Transport)](./transport.md)
- [التحقق من الصحة (Validation)](../basic/validation.md)
- [شرح تطبيق معرض الصور](../examples/gallery-app.md)

---

[← العودة إلى الفهرس](../README.md)

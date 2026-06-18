# फ़ाइल प्रबंधन (File Management)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x में अपलोड और स्टोरेज एक ही portal से होकर गुजरते हैं: **`Pinoox\Portal\File`**। मेटाडेटा `pincore_file` (या किसी साझा transport scope) में रहता है और भौतिक फ़ाइलें डिस्क पर (local, S3, …)।

---

## प्रवेश बिंदु (Entry point)

```php
use Pinoox\Portal\File;
```

| आवश्यकता | API |
|------|-----|
| अपलोड + DB रिकॉर्ड + URL | `File::upload(...)->save()` |
| खोजें / हटाएँ / URL | `File::find()`, `File::url()`, `File::remove()` |
| Raw डिस्क एक्सेस | `File::storage()->put(...)` |

उपयोगकर्ता अपलोड के लिए `Storage::` का सीधे उपयोग न करें — `File::` के साथ prefix, disk और URL सुसंगत रहते हैं।

---

## app.php कॉन्फ़िगरेशन

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // या 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

ग्लोबल disks `config/filesystems.config.php` और `.env` में:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## डेटाबेस रिकॉर्ड के साथ अपलोड

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

## Request से

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## किसी model से जोड़ना (Attach)

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

पिछली फ़ाइल को बदलना:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## केवल डिस्क (DB के बिना)

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

## पढ़ना और हटाना

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — मुख्य मेथड्स

| मेथड | विवरण |
|--------|-------------|
| `to($dir)` | गंतव्य फ़ोल्डर |
| `group($name)` | DB में लॉजिकल group |
| `thumb($w, $h)` | इमेज thumbnail |
| `maxSize('2MB')` | अधिकतम फ़ाइल आकार |
| `extensions('jpg,png')` | अनुमत extensions |
| `disk('s3')` | Disk को override करें |
| `attach($model, $column)` | अपलोड के बाद FK सेट करें |
| `replaceOn($model, $column)` | पुरानी हटाएँ + नई अपलोड करें |
| `save()` | निष्पादित करें → `UploadResult` |

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

// या प्रत्येक अपलोड के लिए अलग से
File::upload('doc')->to('docs')->disk('s3')->save();
```

S3 पर निजी (private) फ़ाइलें:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## सुझाव

- `File::upload()` से पहले FormRequest में validate करें।
- `user_id` अपने आप `Auth::id()` से भरा जाता है।
- `transport.file_storage => platform` के साथ, फ़ाइलें platform ऐप्स के बीच साझा होती हैं।

---


---

## बड़ी फ़ाइलें

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## संबंधित दस्तावेज़

- [Pinion प्रोटोकॉल](./pinion.md)
- [User management](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [Image gallery walkthrough](../examples/gallery-app.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

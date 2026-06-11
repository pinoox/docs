# File Management

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x upload와 storage는 단일 portal **`Pinoox\Portal\File`**을 통해 처리합니다. Metadata는 `pincore_file`(또는 shared transport scope)에, physical file은 disk(local, S3, …)에 있습니다.

---

## 진입점

```php
use Pinoox\Portal\File;
```

| Need | API |
|------|-----|
| Upload + DB record + URL | `File::upload(...)->save()` |
| Find / delete / URL | `File::find()`, `File::url()`, `File::remove()` |
| Raw disk access | `File::storage()->put(...)` |

사용자 upload에 `Storage::` 직접 사용 금지 — prefix, disk, URL이 `File::`과 일관되게 유지됩니다.

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

`config/filesystems.config.php`와 `.env`의 global disk:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Database record와 upload

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

## Request에서

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Model에 attach

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

이전 file 교체:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Disk only (DB 없음)

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

## Read와 delete

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — 주요 method

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

S3 private file:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Tips

- `File::upload()` 전 FormRequest에서 validate.
- `user_id`는 `Auth::id()`에서 채워짐.
- `transport.file_storage => platform`이면 file이 platform app 간 공유.

---

## 관련 문서

- [User management](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [이미지 갤러리 실습 가이드](../examples/gallery-app.md)

---

[← 색인으로 돌아가기](../README.md)

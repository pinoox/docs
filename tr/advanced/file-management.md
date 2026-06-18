# Dosya yönetimi

[← Dizine dön](../README.md)

Pinoox 3.x'te yükleme ve depolama tek bir portal üzerinden yapılır: **`Pinoox\Portal\File`**. Meta veri `pincore_file` içinde (veya paylaşılan transport kapsamında) ve fiziksel dosyalar diskte (local, S3, …) yer alır.

---

## Giriş noktası

```php
use Pinoox\Portal\File;
```

| İhtiyaç | API |
|------|-----|
| Yükleme + DB kaydı + URL | `File::upload(...)->save()` |
| Bul / sil / URL | `File::find()`, `File::url()`, `File::remove()` |
| Ham disk erişimi | `File::storage()->put(...)` |

Kullanıcı yüklemeleri için doğrudan `Storage::` kullanmayın — önek, disk ve URL `File::` ile tutarlı kalır.

---

## app.php yapılandırması

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

Global disk'ler `config/filesystems.config.php` ve `.env` içinde:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Veritabanı kaydı ile yükleme

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

## Request'ten

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Model'e ekleme

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Önceki dosyayı değiştirme:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Yalnızca disk (DB yok)

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

## Okuma ve silme

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — temel metotlar

| Metot | Açıklama |
|--------|-------------|
| `to($dir)` | Hedef klasör |
| `group($name)` | DB'de mantıksal grup |
| `thumb($w, $h)` | Görsel küçük resim |
| `maxSize('2MB')` | Maksimum dosya boyutu |
| `extensions('jpg,png')` | İzin verilen uzantılar |
| `disk('s3')` | Disk geçersiz kılma |
| `attach($model, $column)` | Yüklemeden sonra FK ayarla |
| `replaceOn($model, $column)` | Eskiyi kaldır + yenisini yükle |
| `save()` | Çalıştır → `UploadResult` |

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

S3'te özel dosyalar:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## İpuçları

- `File::upload()` öncesinde FormRequest'te doğrulayın.
- `user_id` `Auth::id()`'den doldurulur.
- `transport.file_storage => platform` ile dosyalar platform uygulamaları arasında paylaşılır.

---


---

## Büyük dosyalar

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## İlgili dokümantasyon

- [Pinion protokolü](./pinion.md)
- [Kullanıcı yönetimi](./user-management.md)
- [Transport](./transport.md)
- [Validasyon](../basic/validation.md)
- [Resim galerisi uygulamalı rehber](../examples/gallery-app.md)

---

[← Dizine dön](../README.md)

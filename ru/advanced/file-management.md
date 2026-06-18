# Управление файлами (File Management)

[← Назад к оглавлению](../README.md)

Загрузка и хранение файлов в Pinoox 3.x выполняются через единый портал: **`Pinoox\Portal\File`**. Метаданные хранятся в `pincore_file` (или в общей области transport), а физические файлы — на диске (local, S3, …).

---

## Точка входа

```php
use Pinoox\Portal\File;
```

| Задача | API |
|------|-----|
| Загрузка + запись в БД + URL | `File::upload(...)->save()` |
| Поиск / удаление / URL | `File::find()`, `File::url()`, `File::remove()` |
| Прямой доступ к диску | `File::storage()->put(...)` |

Не используйте `Storage::` напрямую для пользовательских загрузок — префикс, диск и URL остаются согласованными при работе через `File::`.

---

## Конфигурация app.php

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // или 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

Глобальные диски в `config/filesystems.config.php` и `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## Загрузка с записью в базу данных

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

## Из Request

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Привязка к модели

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

Замена предыдущего файла:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## Только диск (без БД)

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

## Чтение и удаление

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — ключевые методы

| Метод | Описание |
|--------|-------------|
| `to($dir)` | Папка назначения |
| `group($name)` | Логическая группа в БД |
| `thumb($w, $h)` | Миниатюра изображения |
| `maxSize('2MB')` | Максимальный размер файла |
| `extensions('jpg,png')` | Разрешённые расширения |
| `disk('s3')` | Переопределение диска |
| `attach($model, $column)` | Установка FK после загрузки |
| `replaceOn($model, $column)` | Удаление старого + загрузка нового |
| `save()` | Выполнить → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // абсолютный путь
$result->record;    // FileModel
$result->error;     // сообщение об ошибке
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// или для отдельной загрузки
File::upload('doc')->to('docs')->disk('s3')->save();
```

Приватные файлы в S3:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## Советы

- Выполняйте валидацию в FormRequest до `File::upload()`.
- `user_id` заполняется из `Auth::id()`.
- При `transport.file_storage => platform` файлы являются общими для всех приложений платформы.

---


---

## Большие файлы

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## Связанные документы

- [протокол Pinion](./pinion.md)
- [Управление пользователями](./user-management.md)
- [Transport](./transport.md)
- [Валидация](../basic/validation.md)
- [Пошаговый пример: галерея изображений](../examples/gallery-app.md)

---

[← Назад к оглавлению](../README.md)

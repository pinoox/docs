# 文件管理（File Management）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，上传与存储都通过一个统一的 Portal 完成：**`Pinoox\Portal\File`**。元数据保存在 `pincore_file`（或共享的 transport 作用域）中，物理文件保存在磁盘上（local、S3 等）。

---

## 入口

```php
use Pinoox\Portal\File;
```

| 需求 | API |
|------|-----|
| 上传 + 数据库记录 + URL | `File::upload(...)->save()` |
| 查找 / 删除 / URL | `File::find()`、`File::url()`、`File::remove()` |
| 直接访问磁盘 | `File::storage()->put(...)` |

不要直接使用 `Storage::` 处理用户上传 —— 使用 `File::` 可以保证前缀、磁盘和 URL 的一致性。

---

## app.php 配置

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // 或 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

全局磁盘配置位于 `config/filesystems.config.php` 和 `.env`：

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## 带数据库记录的上传

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

## 从 Request 上传

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## 附加到模型（Model）

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

替换之前的文件：

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## 仅写入磁盘（不写数据库）

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

## 读取与删除

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder —— 关键方法

| 方法 | 说明 |
|--------|-------------|
| `to($dir)` | 目标文件夹 |
| `group($name)` | 数据库中的逻辑分组 |
| `thumb($w, $h)` | 图片缩略图 |
| `maxSize('2MB')` | 最大文件大小 |
| `extensions('jpg,png')` | 允许的扩展名 |
| `disk('s3')` | 覆盖磁盘设置 |
| `attach($model, $column)` | 上传后设置外键 |
| `replaceOn($model, $column)` | 删除旧文件 + 上传新文件 |
| `save()` | 执行 → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // 绝对路径
$result->record;    // FileModel
$result->error;     // 错误信息
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// 或者针对单次上传
File::upload('doc')->to('docs')->disk('s3')->save();
```

S3 上的私有文件：

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## 提示

- 在调用 `File::upload()` 之前，先在 FormRequest 中进行验证。
- `user_id` 会从 `Auth::id()` 自动填充。
- 设置 `transport.file_storage => platform` 后，文件可在平台应用之间共享。

---


---

## 大文件

For files that exceed `upload_max_filesize` or need resume/progress, use the **[Pinion](./pinion.md)** protocol. Pinion stages chunks under `storage/pinion`, then on `complete` publishes to your app disk (local or S3) via `Portal\File` when `mode` is `auto` or `storage`.

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
});
```

---

## 相关文档

- [Pinion 协议](./pinion.md)
- [用户管理（User management）](./user-management.md)
- [Transport](./transport.md)
- [验证（Validation）](../basic/validation.md)
- [图库应用实战演练](../examples/gallery-app.md)

---

[← 返回索引](../README.md)

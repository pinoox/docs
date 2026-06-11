# ファイル管理

[← 索引に戻る](../README.md)

Pinoox 3.x のアップロードとストレージは単一 Portal **`Pinoox\Portal\File`** 経由です。メタデータは `pincore_file`（または共有 transport スコープ）に、物理ファイルはディスク上（local、S3 など）にあります。

---

## エントリーポイント

```php
use Pinoox\Portal\File;
```

| 必要な操作 | API |
|------|-----|
| アップロード + DB レコード + URL | `File::upload(...)->save()` |
| 検索 / 削除 / URL | `File::find()`、`File::url()`、`File::remove()` |
| 生ディスクアクセス | `File::storage()->put(...)` |

ユーザーアップロードに `Storage::` を直接使用しない — プレフィックス、disk、URL は `File::` と一貫します。

---

## app.php 設定

```php
return [
    'transport' => [
        'file_storage' => 'platform',   // または 'local'
    ],
    'filesystem' => [
        'disk' => 'local',
        'default_access' => 'public',
        'thumb_width' => 512,
        'thumb_height' => 512,
    ],
];
```

グローバル disk は `config/filesystems.config.php` と `.env`:

```env
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=...
AWS_BUCKET=...
AWS_URL=https://cdn.example.com
```

---

## データベースレコード付きアップロード

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

## Request から

```php
$result = $request->store('photo', 'gallery')
    ->group('gallery')
    ->thumb(256, 256)
    ->save();
```

---

## Model に添付

```php
$result = File::upload('cover')
    ->to('posts')
    ->group('post_cover')
    ->attach($post, 'cover_id')
    ->save();
```

以前のファイルを置き換え:

```php
$result = File::upload('avatar')
    ->to('avatar')
    ->group('avatar')
    ->replaceOn($user, 'avatar_id')
    ->thumb()
    ->save();
```

---

## disk のみ（DB なし）

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

## 読み取りと削除

```php
$record = File::find($fileId);
$url = File::url($fileId);
$thumb = File::thumb($fileId);
$list = File::listByGroup('avatar');

File::remove($fileId);
```

---

## UploadBuilder — 主要メソッド

| メソッド | 説明 |
|--------|-------------|
| `to($dir)` | 保存先フォルダ |
| `group($name)` | DB 内の論理グループ |
| `thumb($w, $h)` | 画像サムネイル |
| `maxSize('2MB')` | 最大ファイルサイズ |
| `extensions('jpg,png')` | 許可拡張子 |
| `disk('s3')` | disk を上書き |
| `attach($model, $column)` | アップロード後 FK を設定 |
| `replaceOn($model, $column)` | 古いファイル削除 + 新規アップロード |
| `save()` | 実行 → `UploadResult` |

---

## UploadResult

```php
$result->success;   // bool
$result->id;        // file_id
$result->url;       // file_link
$result->thumb;     // thumb_link
$result->path;      // 絶対パス
$result->record;    // FileModel
$result->error;     // エラーメッセージ
```

---

## S3

```php
// app.php
'filesystem' => ['disk' => 's3'],

// またはアップロードごと
File::upload('doc')->to('docs')->disk('s3')->save();
```

S3 のプライベートファイル:

```php
$url = File::storage('s3')->temporaryUrl('private/doc.pdf', now()->addHour());
```

---

## ヒント

- `File::upload()` の前に FormRequest で Validation
- `user_id` は `Auth::id()` から自動入力
- `transport.file_storage => platform` の場合、ファイルはプラットフォームアプリ間で共有

---

## 関連ドキュメント

- [ユーザー管理](./user-management.md)
- [Transport](./transport.md)
- [Validation](../basic/validation.md)
- [画像ギャラリー ウォークスルー](../examples/gallery-app.md)

---

[← 索引に戻る](../README.md)

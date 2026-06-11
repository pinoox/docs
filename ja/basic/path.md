# ファイルパス

[← 索引に戻る](../README.md)

ディスク上のファイルとフォルダへのアクセスには **`path()`** と **`Pinoox\Portal\Path`** Portal を使用します。これにより、プロジェクトのインストール場所や `apps/` フォルダ名に依存しないコードを保てます。

---

## 標準的な方法 — `path()`

```php
// アクティブアプリからの相対パス
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// 別アプリの設定ファイル
$configFile = path('config/payment.php', 'com_acme_shop');

// アプリルート
$appRoot = path('', 'com_acme_shop');
// または
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## よく使う用途

### ファイルの読み書き

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### 翻訳ファイルパス

```php
$langFile = path('lang/en/welcome.lang.php');
```

### テーマパス

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

`path()` と同じ動作を明示的 API で提供:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // 現在のアプリ
Path::app('com_pinoox_manager'); // 特定のアプリ
```

---

## `path()` vs `url()`

| ヘルパー | 出力 | 例 |
|--------|--------|---------|
| `path()` | サーバー上の物理パス | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | ブラウザ向け HTTP URL | `https://site.com/pinoox/shop/products` |

---

## 例: アップロードサービス

`path()` + `move_uploaded_file()` で手動アップロードしない — **`File`** Portal を使用して、ファイルをプロジェクトの `storage/` フォルダに保存します。

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // storage/apps/com_acme_shop/{subdir} 配下に保存
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

完全なアップロード API については [ファイル管理](../advanced/file-management.md) を参照してください。

---

## ヒント

- ブラウザからアクセス可能なパスには `path()` ではなく `url()` または `assets()` を使用
- 非アクティブアプリが必要な場合のみパッケージ名を渡す
- パスセグメントは `/` で結合。Path が正しい OS スラッシュを処理

---

## 関連ドキュメント

- [URL とリンク](./url.md)
- [Config](./config.md)
- [App Services](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← 索引に戻る](../README.md)

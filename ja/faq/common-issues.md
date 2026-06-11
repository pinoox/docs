# よくある問題

[← 索引に戻る](../README.md)

Pinoox のインストール、実行時、開発中によく発生するエラーへの実践的な修正。各セクションでは **1 つのアプローチ** を推奨します。

---

## `composer install` が失敗する

**症状:** 拡張機能不足、PHP バージョンが低い、ネットワークタイムアウト。

**修正:**

1. PHP 8.1+ と拡張機能 `mysqli`、`zip`、`mbstring`、`json` を有効化。
2. インストール前にプラットフォームチェックを実行:

```bash
php launcher/check.php
```

3. 再インストール:

```bash
composer install --no-interaction
```

共有ホスティングで `composer` が PATH にない場合、ローカルで vendor をビルドしてアップロードしてください。

---

## 権限エラー（ファイルアクセス）

**症状:** `cache/`、`storage/`、`pinker/` に書き込めない。

**修正（Linux/macOS）:**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

Web サーバーユーザー（例: `www-data` または `apache`）が書き込み可能フォルダに書き込める必要があります。Windows/MAMP ではプロジェクトフォルダを `Program Files` 外に置いてください。

---

## `.htaccess` / rewrite が動作しない

**症状:** `index.php` 以外のすべての URL で 404。ブラウザで API が JSON を返さない。

**修正:**

1. Apache `mod_rewrite` を有効化。
2. DocumentRoot に `AllowOverride All` を設定。
3. プロジェクトルートに `.htaccess` が存在することを確認。
4. クイックテスト: `http://localhost/pinoox/api/v1/ping` — JSON が表示されれば rewrite は動作。

nginx では `.htaccess` の代わりにサーバー設定で `try_files` と `index.php` ルールを記述してください。

---

## Database 接続が失敗する

**症状:** `SQLSTATE[HY000] [2002] Connection refused` または access denied。

**修正:**

1. MySQL/MariaDB が起動していることを確認。
2. `config/database.config.php` または `.env` の値を確認:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. 事前に Database を作成（`CREATE DATABASE ... utf8mb4`）。
4. cPanel ではホストが `localhost` でない場合があります — パネルのホスト名を使用。

---

## Pinker 再ビルドが必要

**症状:** 古い config または routes。`app.php` の変更が反映されない。

**修正:**

```bash
php pinoox pinker:rebuild com_my_shop
# またはエイリアス:
php pinoox bake com_my_shop

# すべてのアプリ:
php pinoox pinker:rebuild all
```

routes、config を変更した後、または本番デプロイ後は通常再ビルドが必要です。

---

## ルートが見つからない（エンドポイントで 404）

**症状:** コードでルートが定義されているが 404 になる。

**修正:**

1. ルートファイルが `apps/{package}/routes/` にあり、`app.php` → `router.routes` に一覧されていることを確認。
2. アプリプレフィックス（`app:router`）と URL を一致:

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Pinker 再ビルドを実行（上記参照）。
4. 正しい HTTP メソッド（`GET` vs `POST`）を使用。

---

## 404 — アプリが解決されない

**症状:** デフォルトページまたは 404。間違ったアプリが読み込まれる。

**修正:**

1. パス/ホストマッピングを確認:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. `config/domain.config.php`（または関連マップ）でホストとパスを正しく設定。
3. アプリの `app.php` で `'enable' => true` を確認。
4. アプリフォルダ名は `app.php` の `'package'` と一致（例: `com_my_shop`）。

---

## テストが失敗する

```bash
php pinoox test com_my_shop
```

- 別 DB 用の `.env.testing`
- migration 実行: `php pinoox migrate com_my_shop`
- `fakeApp()` 後 → `deleteFakeApp()`

詳細: [テストはじめに](../test/getting-started.md)

---

## 関連ドキュメント

- [Pinoox のインストール](../start/installing-pinoox.md)
- [プロジェクト構造](../start/structure.md)
- [Router](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker（Pinker）](../advanced/pinker.md)
- [Database はじめに](../database/getting-started.md)
- [サポートへのお問い合わせ](./contact-support.md)

---

[← 索引に戻る](../README.md)

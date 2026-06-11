# Pinoox のインストール

[← 索引に戻る](../README.md)

このガイドでは Pinoox 3.x のインストール方法を説明します。開始方法は 2 つあります。

| ルート | 最適な用途 |
|-------|----------|
| **A. [Pinx CLI](./pinx-cli.md) によるシングルアプリ** | 1 つのアプリを構築 — 最速の開始、Manager UI 不要 |
| **B. フルプラットフォーム（クラシック）** | グラフィカルインストーラーと Manager で複数アプリをホスト |

---

## 要件

| ツール | バージョン |
|------|---------|
| PHP | 8.1 以上（ext-mysqli、ext-zip が必要） |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js（任意） | 18+ — フロントエンドテーマのビルド時のみ |

---

## ルート A — Pinx CLI によるシングルアプリ

[Pinx CLI](./pinx-cli.md) を一度インストールし、新しいアプリを作成して実行します。

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # com_my_shop を提案 — ウィザードで確認または編集
cd my-shop
cp .env.example .env          # Database を使う場合は DB_* を設定
pinx setup                    # platform + app を migrate、Seeder を実行
pinx dev                      # http://127.0.0.1:8000
```

グローバルインストールなしで、プロジェクトテンプレート経由の場合:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

いつでも `pinx doctor` を実行して PHP、env、DB、ビルドの準備状況を確認できます。日常のワークフローとコマンドリファレンスについては、完全な [Pinx CLI ガイド](./pinx-cli.md) を参照してください。

---

## ルート B — フルプラットフォーム（クラシック）

### 1. プロジェクトを取得

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

または、[GitHub](https://github.com/pinoox/pinoox) から最新リリースをダウンロードし、展開してから `composer install` を実行します。

---

### 2. Web サーバーに配置

プロジェクトフォルダをドキュメントルートに置きます。

| 環境 | 例のパス |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

ドキュメントルートは **`index.php` を含むプロジェクトルート**（`public` サブフォルダではない）に設定してください。

---

### 3. Database を作成

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. インストーラーを実行

ブラウザで開きます。

```
http://localhost/pinoox
```

システムアプリ `com_pinoox_installer` が起動します。GUI の手順は次のとおりです。

1. PHP 要件の確認
2. ライセンス契約への同意
3. Database 認証情報の入力
4. 管理者アカウントの作成
5. インストールの完了

---

### 5. インストール後

メインレイアウト:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← アプリ
├── vendor/pinoox/pincore/  ← コア
└── config/             ← プロジェクト設定
```

最初のアプリを作成:

```bash
php pinoox app:create com_acme_blog
```

---

## クイックトラブルシューティング

| 問題 | 対処 |
|---------|-----|
| 白い画面 | `composer install` を実行し、PHP エラーログを確認 |
| サブルートで 404 | mod_rewrite / `.htaccess` を有効化 |
| 拡張機能不足エラー | php.ini で ext-mysqli と ext-zip を有効化 |
| インストーラーが開かない | ドキュメントルートと runtime フォルダの書き込み権限を確認 |

---

## 関連ドキュメント

- [Pinx CLI（シングルアプリ）](./pinx-cli.md)
- [最初のアプリ](./your-first-app.md)
- [プロジェクト構造](./structure.md)
- [Pinoox とは？](../introduction/what-is-pinoox.md)

---

[← 索引に戻る](../README.md)

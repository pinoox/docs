# Pinker と Cache

[← 索引に戻る](../README.md)

**Pinker** は Pinoox 3.x の bake/ランタイムレイヤーです。設定と Cache はソースから PHP ファイルにコンパイルされ、高速 boot のために `include` できます。アプリごとの標準パス: **`pinker/apps/{package}/`**。

---

## フォルダ構造

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← bake 済み app.php
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← コンパイル済みテンプレート
```

プロジェクトレベル:

```
pinker/config/          ← bake 済み config（env 非依存）
pinker/state/config/    ← インストール後の上書き（例: database）
```

---

## CLI コマンド

```bash
# 1 アプリの Pinker を再ビルド
php pinoox pinker:rebuild com_acme_shop

# 短いエイリアス
php pinoox bake com_acme_shop

# ステータス: ソース vs bake 出力を比較
php pinoox pinker:status com_acme_shop

# Cache をビルド（route、api、twig、pinker など）
php pinoox cache:build com_acme_shop

# Twig のみ
php pinoox cache:build com_acme_shop --only=twig

# Pinker のみ
php pinoox cache:build com_acme_shop --only=pinker

# Cache をクリア
php pinoox cache:clear com_acme_shop
```

---

## 再ビルドが必要なタイミング

| イベント | コマンド |
|-------|---------|
| `app.php` または config を変更 | `pinker:rebuild` |
| route / api を変更 | `cache:build` |
| 本番で `.twig` を変更 | `cache:build --only=twig` |
| サーバーインストール後 | `cache:build` + `pinker:rebuild` |
| `.pinx` をビルドする前 | `cache:build`（パッケージ内に cache） |

---

## ランタイムで Cache を有効化

`apps/{package}/app.php` 内:

```php
'cache' => [
    'enabled' => false,   // デフォルト — 必要なら本番で true
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## アプリミラー — `pinker/app.php`

各アプリは bake 済みミラーを持てます。

```
apps/com_acme_shop/pinker/app.php   ← リポジトリ内のソース/参照
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← ランタイム
```

---

## `pinker()` ヘルパー

手動データ bake 用:

```php
pinker($data, ['lifetime' => 3600]);
```

通常は CLI を使用。アプリコードでは稀に必要です。

---

## 推奨デプロイワークフロー

```bash
# 1. フロントエンドをビルド
php pinoox theme:frontend build com_acme_shop

# 2. cache
php pinoox cache:build com_acme_shop

# 3. pinker（env 固有）
php pinoox pinker:rebuild com_acme_shop
```

---

## ヒント

- `pinker/state/` を手動編集しない — インストーラーが書き込みます。
- 開発環境ではランタイム cache は通常オフ。大きな変更後のみ再ビルド。
- `.pinx` は pre-built cache を同梱可能。ターゲットサーバーで一度 `cache:build --only=pinker` を実行。

---

## 関連ドキュメント

- [Config](../basic/config.md)
- [Twig テンプレート](../basic/templates.md)
- [CLI リファレンス](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← 索引に戻る](../README.md)

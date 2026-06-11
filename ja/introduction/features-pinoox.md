# Pinoox の機能

[← 索引に戻る](../README.md)

Pinoox 3.x はモジュラー PHP エコシステム向けに設計されています。1 つの共有コア上で複数の独立したアプリ、CLI によるスキャフォールディング、HTTP・Database・テーマ・認証向けの組み込みツールを提供します。

---

## HMVC アーキテクチャと独立したアプリ

`apps/{package}/` 配下の各アプリは完全な MVC 構造を持ちます。

| レイヤー | 例のパス |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View（Twig） | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow（ミドルウェア） | `Flow/AuthFlow.php` |

1 つのアプリを追加または無効化しても、他のアプリには影響しません。

---

## CLI と迅速な開発

プロジェクトルートから:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI は標準的なフォルダレイアウト、`app.php`、初期ルートファイルを生成します。

---

## ルーティングと Named Actions

URL パスと論理ハンドラーは分離されています。

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

このパターンにより、リファクタリングとテストが容易になります。

---

## Flow（ミドルウェア）

リクエストが Controller に到達する前に Flow が実行されます — 認証、認可、ロギングなど:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Flow エイリアスは `app.php` に登録します。

---

## View とテーマ

- Twig テンプレートは `theme/{themeName}/` に配置
- **`View::render()`** でレンダリング
- テーマ内の Vite による SPA サポート（Vue/React）

---

## Database と Eloquent

- `DB` Portal 経由の Query Builder と Eloquent
- 各アプリの `database/migrations/` に Migrations と Seeders
- パッケージ名に基づくテーブルプレフィックス（例: `com_acme_blog_posts`）

---

## API と JSON レスポンス

**`ApiController`** を継承し、標準エンベロープを使用します。

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## 国際化

翻訳ファイルは `lang/{locale}/*.lang.php` — 多言語アプリに適しています。

---

## 関連ドキュメント

- [Pinoox とは？](./what-is-pinoox.md)
- [Pinoox のインストール](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← 索引に戻る](../README.md)

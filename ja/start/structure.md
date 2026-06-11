# プロジェクト構造

[← 索引に戻る](../README.md)

Pinoox は HMVC アーキテクチャを採用しています。`apps/{package}/` 配下の各アプリは完全で独立した MVC モジュールです。フレームワークコアは `vendor/pinoox/pincore/` にあり、プラットフォーム自体を変更する場合のみ編集します。

---

## プロジェクトレイアウト

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← コア（Composer パッケージ）
├── apps/                    ← すべてのアプリ
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← アップロードファイルとアプリストレージ
```

---

## アプリレイアウト

```
apps/com_acme_shop/
├── app.php                  ← マニフェスト（必須）
├── boot.php                 ← プログラム的なルート/イベント（任意）
├── schedule.php             ← cron タスク（任意）
├── Controller/              ← HTTP ハンドラー
├── Model/                   ← Eloquent Model
├── Flow/                    ← ミドルウェア
├── Component/               ← ビジネスロジック
├── Portal/                  ← アプリ Facade（任意）
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← アクション名定数（任意）
├── theme/default/           ← Twig + アセット
├── lang/en/                 ← 翻訳
├── config/                  ← アプリ設定
├── database/migrations/
└── pinker/                  ← ビルドミラー
```

View は別の `View/` フォルダにはありません — テンプレートは `theme/{themeName}/` にあります。

---

## app.php — 主要フィールド

```php
<?php

return [
    'package' => 'com_acme_shop',   // = フォルダ名
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## 名前空間

PSR-4: `App\` → `apps/`

| ファイル | 名前空間 |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## 命名規則

- パッケージ: `com_{vendor}_{name}` — 例: `com_acme_shop`
- フォルダ名 = `app.php` の `package` = 名前空間セグメント
- DB テーブルプレフィックス: `{package}_`（例: `com_acme_shop_orders`）

---

## アプリとコアの境界

| 変更内容 | 場所 |
|--------|----------|
| 新しいエンドポイント | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| フレームワークのバグ | `pinoox/pincore`（上流） |
| UI | `apps/{package}/theme/` |

アプリは独立させてください — アプリ同士を結合するのではなく、`Pinoox\Portal\*` Facade を使用します。

---

## 関連ドキュメント

- [最初のアプリ](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← 索引に戻る](../README.md)

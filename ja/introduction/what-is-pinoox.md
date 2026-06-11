# Pinoox とは？

[← 索引に戻る](../README.md)

Pinoox は、HMVC アーキテクチャと **app** 概念に基づく、モダンなオープンソース PHP フレームワーク（3.x）です。モジュラー Web 開発をシンプルにします。各アプリは `apps/{package}/` 配下の独立した MVC ユニットであり、共有フレームワークコアは `vendor/pinoox/pincore/` にあります。

---

## アプリ中心のアーキテクチャ

1 つの Pinoox インストール上で、複数の独立したアプリが並行して動作します。

```
{project_root}/
├── index.php              ← Web エントリーポイント
├── pinoox                 ← CLI エントリーポイント
├── composer.json
├── vendor/pinoox/pincore/ ← フレームワークコア（コア変更時のみ編集）
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← あなたのアプリ
```

- **プロジェクト** — `index.php` と `apps/` を含むフォルダ（フォルダ名は任意）。
- **アプリ** — 独自の Controller、Model、ルート、テーマ、設定を持つ完全なモジュール。
- **コア** — 共有エンジン（ルーター、HTTP、データベース、Twig、CLI など）。

ビジネスロジックは `vendor/pinoox/pincore/` ではなく `apps/` に書きます。

---

## HTTP リクエストのライフサイクル

```
ブラウザ → index.php → 起動処理（bootstrap）
      → アクティブなアプリを解決（ドメインまたは URL プレフィックス）
      → app.php と routes/ を読み込み
      → Flows → Controller → Model（任意） → View または JSON
```

---

## アプリの命名

推奨パッケージ形式:

```
com_{vendor}_{name}
```

例: `com_acme_shop` — フォルダ名、`app.php` の `package` 値、名前空間セグメントはすべて一致させる必要があります。

---

## 向いている用途

- 各セクションを別アプリにできる、複数セクションのサイトや管理パネル
- モジュールを独立して開発・テスト・保守したいチーム
- Composer と統合 CLI（`php pinoox …`）を使う PHP 8.1+ プロジェクト

---

## 関連ドキュメント

- [Pinoox の機能](./features-pinoox.md)
- [Pinoox のインストール](../start/installing-pinoox.md)
- [最初のアプリ](../start/your-first-app.md)
- [Notes API ウォークスルー](../examples/simple-api-app.md)
- [電話帳ウォークスルー](../examples/phonebook-app.md)
- [お問い合わせフォーム ウォークスルー](../examples/contact-form-app.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)

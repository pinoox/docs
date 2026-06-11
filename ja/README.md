# Pinoox ドキュメント

Pinoox プラットフォーム（PHP 8.1+、HMVC アーキテクチャ）上でアプリを構築するための公式開発者向けドキュメントです。

各ガイドでは、実践的な例とともに **推奨される 1 つのアプローチ** を説明します。以下からセクションを選ぶか、トピック別に閲覧してください。

**言語:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](./README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### はじめに

#### [Pinoox とは？](./introduction/what-is-pinoox.md)
#### [Pinoox の機能](./introduction/features-pinoox.md)
#### [Pinoox への貢献](./introduction/contributions.md)

### スタートガイド

#### [Pinoox のインストール](./start/installing-pinoox.md)
#### [最初のアプリ](./start/your-first-app.md)
#### [プロジェクト構造](./start/structure.md)
#### [Pinoox CLI リファレンス](./start/cli-reference.md)
#### [Pinx CLI（シングルアプリプロジェクト）](./start/pinx-cli.md)
#### [app.php マニフェスト リファレンス](./start/app-manifest.md)

### 実践ウォークスルー

#### [ウォークスルー: Notes API アプリ](./examples/simple-api-app.md)
#### [ウォークスルー: 電話帳 Web アプリ](./examples/phonebook-app.md)
#### [ウォークスルー: お問い合わせフォーム アプリ](./examples/contact-form-app.md)
#### [ウォークスルー: シンプルなブログ アプリ](./examples/blog-app.md)
#### [ウォークスルー: タスクボード（Todo）](./examples/task-board-app.md)
#### [ウォークスルー: 画像ギャラリー アプリ](./examples/gallery-app.md)
#### [ウォークスルー: Vue SPA パネル](./examples/vue-spa-app.md)
#### [ウォークスルー: React SPA パネル](./examples/react-spa-app.md)
#### [ウォークスルー: Vite ハイブリッド（Twig + JS ウィジェット）](./examples/vite-hybrid-app.md)

### コア概念

#### [Router](./basic/routers.md)
#### [Controller](./basic/controllers.md)
#### [Flow（ミドルウェア）](./basic/flows.md)
#### [HTTP Request](./basic/requests.md)
#### [HTTP Response](./basic/responses.md)
#### [URL とリンク生成](./basic/url.md)
#### [ファイルパス](./basic/path.md)
#### [Validation](./basic/validation.md)
#### [View](./basic/views.md)
#### [Twig テンプレート](./basic/templates.md)
#### [Portal（Facade）](./basic/portal.md)
#### [Config](./basic/config.md)
#### [言語と翻訳](./basic/language.md)

### 高度なトピック

#### [Pinker と Cache](./advanced/pinker.md)
#### [App Services（Component + Portal）](./advanced/services.md)
#### [グローバル Helpers](./advanced/helpers.md)
#### [メール送信](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [ユーザー管理](./advanced/user-management.md)
#### [ファイル管理](./advanced/file-management.md)
#### [トークン管理](./advanced/token-management.md)
#### [アクセスと権限](./advanced/access-permissions.md)
#### [Transport（共有リソース）](./advanced/transport.md)
#### [boot.php とイベント](./advanced/boot-and-events.md)
#### [スケジューリング（cron）](./advanced/schedule.md)

### Database

#### [Database はじめに](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Pagination](./database/pagination.md)
#### [Migrations](./database/migrations.md)
#### [Patches（データ更新）](./database/patches.md)

### Eloquent ORM

#### [Eloquent ORM はじめに](./eloquent-orm/getting-started.md)
#### [Eloquent Relationships](./eloquent-orm/relationships.md)
#### [Eloquent Collections](./eloquent-orm/collections.md)
#### [Mutators と Casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Model Serialization](./eloquent-orm/serialization.md)
#### [テストデータ — Seeders](./eloquent-orm/factories.md)

### テスト

#### [Pinoox でのテストはじめに](./test/getting-started.md)
#### [Pinoox での HTTP テスト](./test/http-tests.md)
#### [Pinoox での Console テスト](./test/console-tests.md)
#### [Pinoox での Browser（HTML）テスト](./test/browser-tests.md)
#### [Pinoox での Database テスト](./test/database.md)
#### [Pinoox での Serialization テスト](./test/serialization.md)
#### [Pinoox での Mocking](./test/mocking.md)

### FAQ

#### [よくある問題](./faq/common-issues.md)
#### [サポートへのお問い合わせ](./faq/contact-support.md)

---

### ソース
**サンプルソース:** [docs/source/](../source/) — すべてのウォークスルーの完全なコード

実際のアプリ向けのステップバイステップガイド — 基本を読んだ後、手を動かしてコードを書きたいときにご利用ください。

---

### ドキュメントの読み方

1. Pinoox が初めての方は **はじめに** と **スタートガイド** から始めてください。
2. **実践ウォークスルー** に従い — JSON API とシンプルな Web サイトをステップバイステップで構築します。
3. ルート、Controller、View を構築しながら **コア概念** を読んでください。
4. 永続化を追加するときは **Database** と **Eloquent ORM** を参照してください。
5. 認証、ファイル、Pinker、共有サービスについては **高度なトピック** を参照してください。
6. 本番環境に機能をリリースする前に **テスト** を活用してください。

すべてのアプリコードは `apps/{package}/` 配下にあります。フレームワークコアは `vendor/pinoox/pincore/` です — アプリのビジネスロジックはここに置かないでください。

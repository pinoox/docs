# Pinoox への貢献

[← 索引に戻る](../README.md)

Pinoox はオープンソースプロジェクトです。バグ報告から Pull Request まで、あなたの貢献がフレームワークとドキュメントの改善に役立ちます。

---

## 貢献の方法

| 種類 | 説明 |
|------|-------------|
| バグ報告 | 再現手順付きの GitHub Issue |
| 機能リクエスト | ユースケースを説明する Issue |
| Pull Request | 適切なリポジトリでのバグ修正または機能追加 |
| ドキュメント | `docs/` 配下のファイルの改善（日本語または英語） |
| オープンソースアプリ | コミュニティ向けに Pinoox アプリを公開 |

---

## バグの報告

Issue を開く際は、以下を含めてください。

1. **タイトル** — 問題の短い要約
2. **再現手順** — ステップバイステップ
3. **期待される動作** と **実際の動作**
4. **環境** — PHP バージョン、Pinoox/pincore バージョン、オペレーティングシステム
5. **サンプルコード** — 可能な場合

[Pinoox GitHub Issues](https://github.com/pinoox/pinoox/issues)

---

## Pull Request

### リポジトリ

- **pinoox/pinoox** — サンプルプロジェクト、システムアプリ、ランチャー
- **pinoox/pincore** — フレームワークコア（`vendor/pinoox/pincore/`）

コアの変更は、プロジェクト内のローカル `vendor/` コピーだけでなく pincore に送ってください。

### ブランチ戦略（3.x）

- **バグ修正** → 現在の安定ブランチ（例: `3.x`）
- **小さく互換性のある機能** → 同じ安定ブランチ
- **破壊的または大規模な変更** → `master` / 次期バージョンブランチ

### コード規約

- コードスタイル: [PSR-12](https://www.php-fig.org/psr/psr-12/)
- オートロード: [PSR-4](https://www.php-fig.org/psr/psr-4/)
- PHP 8.2+
- 明確で命令形のコミットメッセージ（例: `Fix route validation for missing actions`）

---

## セキュリティ

セキュリティ上の脆弱性は **非公開** で報告してください。

`security@pinoox.com`

---

## お問い合わせ

- サポート: `support@pinoox.com`
- [GitHub リポジトリ](https://github.com/pinoox/pinoox)

---

## 関連ドキュメント

- [Pinoox とは？](./what-is-pinoox.md)
- [Pinoox の機能](./features-pinoox.md)

---

[← 索引に戻る](../README.md)

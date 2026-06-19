# サポートへのお問い合わせ

[← 索引に戻る](../README.md)

[よくある問題](./common-issues.md) を確認してもブロッカーが残る場合は、以下の公式チャネルをご利用ください。サポートに連絡する前に、Pinoox バージョン、PHP バージョン、エラーメッセージ、再現手順を準備してください。

---

## 一般サポート

**メール:** [support@pinoox.com](mailto:support@pinoox.com)

適している内容:

- インストールとデプロイの質問
- 予期しないフレームワークの動作
- HMVC とアプリアーキテクチャのガイダンス

メールに含める内容:

1. Pinoox バージョン（`composer.json` → `version` または git tag）
2. PHP バージョン（`php -v`）
3. OS と Web サーバー（Apache/nginx、MAMP、cPanel など）
4. 完全なエラーテキストまたはスクリーンショット
5. 最小限の再現手順

---

## GitHub Issues

確認済みバグ、機能リクエスト、公開技術討論:

**リポジトリ:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

新規 Issue を開く前に:

- 重複 Issue を検索
- 最新 stable/beta リリースでテスト
- `pincore` 関連の場合、`pinoox/pincore` パッケージも確認

推奨 Issue テンプレート:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.2.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## セキュリティ報告

**メール:** [security@pinoox.com](mailto:security@pinoox.com)

**セキュリティ脆弱性のみ** — SQL インジェクション、認証バイパス、RCE、シークレット露出。

- パッチ準備完了まで公開（GitHub Issue）しない
- 可能なら最小 PoC と影響説明を含める

---

## コード貢献

PR とフレームワーク開発:

- [貢献](../introduction/contributions.md)
- Fork → branch → test（`php pinoox test`）→ Pull Request

---

## セルフヘルプリソース

| トピック | ドキュメント |
|-------|-----|
| インストール | [installing-pinoox.md](../start/installing-pinoox.md) |
| 最初のアプリ | [your-first-app.md](../start/your-first-app.md) |
| よくある問題 | [common-issues.md](./common-issues.md) |
| テスト | [getting-started.md](../test/getting-started.md) |

**Web サイト:** [pinoox.com](https://www.pinoox.com/)

---

## 関連ドキュメント

- [よくある問題](./common-issues.md)
- [Pinoox とは？](../introduction/what-is-pinoox.md)
- [貢献](../introduction/contributions.md)
- [Pinoox のインストール](../start/installing-pinoox.md)

---

[← 索引に戻る](../README.md)

# Transport（共有リソース）

[← 索引に戻る](../README.md)

HMVC アーキテクチャでは、アプリは `app.php` の **`transport`** ブロックを通じてユーザー、認証、ファイル、権限を互いに共有できます。transport がない場合、各アプリはすべてのリソースを自分のパッケージ内で **local** に保持します。

| 用語 | 意味 |
|------|---------|
| **`platform`** | 論理共有スコープ — 共有 DB 行は `app = platform` |
| **`pincore/`** | 物理フレームワークフォルダのみ — transport スコープ値として **使用しない** |

---

## 仕組み

Transport には 2 レイヤーがあります。

1. **Scenario** — 複数の詳細キーに展開される単語プリセット
2. **Granular key** — 1 つの共有リソース用の複数語名

```php
// app.php
'transport' => [
    'full' => 'platform',           // scenario プリセット
    'file_storage' => 'local',      // granular 上書き
],
```

**解決順序:** 明示的 granular key → 一致する scenario。

Granular key は常に scenario 展開より優先されます。キーが未設定で scenario もカバーしない場合、アプリはそのリソースを **local**（現在のパッケージ）に保持します。

---

## スコープ値

各 scenario または granular key に 1 つのスコープを割り当てます。

| スコープ | 意味 |
|-------|---------|
| `local` | 現在のアプリパッケージ（省略時デフォルト） |
| `platform` | 共有プラットフォームスコープ（`app = platform`、`pinx_*` テーブル） |
| `host` | このアプリを開いたアプリ（プレビュー / `App::meeting()`） |
| `{package}` | 明示的アプリ、例: `com_pinoox_manager` |

**`auth_config`** と **`auth_cookie`** では、`platform` と `{package}` は **認証設定を提供するアプリ**（インストール時は通常 `com_pinoox_manager`）に解決されます。

---

## Scenario リファレンス

単語プリセット。`app.php` で `'transport' => ['{scenario}' => '{scope}']` として使用。

| Scenario | 説明 | 含まれる granular key |
|----------|-------------|------------------------|
| `full` | すべての共有リソース | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | ログインシステム: アカウント、認証、セッショントークン | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | ファイルアップロードとメタデータ | `file_storage` |
| `access` | ロールと権限 | `access_table` |

---

## Granular key リファレンス

複数語のリソース名。1 つのリソースを共有または上書きする際に使用。

| Granular key | 制御対象 | 使用元 |
|--------------|----------|---------|
| `user_table` | `UserModel` の `app` 列 / グローバルスコープ | ユーザーアカウント |
| `auth_config` | 認証モード、JWT シークレット、lifetime（`auth` ブロックのソース） | `AuthConfig`、ログインフロー |
| `auth_cookie` | クライアントキー / cookie 名（`auth.key`） | Cookie と SPA トークンストレージ |
| `session_token` | `TokenModel` の `app` 列 / DB セッション行 | セッション永続化 |
| `file_storage` | `FileModel` の `app` 列 / アップロードパス | アップロードとファイルメタデータ |
| `access_table` | ロールと権限 Model の `app` スコープ | `RoleModel`、`PermissionModel`、`can()` |

---

## よくある設定

**プラットフォーム向け認証プロバイダー（例: manager）:**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**コンシューマーアプリ — すべて共有、local auth ブロックなし:**

```php
'transport' => ['full' => 'platform'],
```

**ログインのみ共有:**

```php
'transport' => ['user' => 'platform'],
```

**スタンドアロンアプリ** — `transport` を省略、またはすべて local に固定:

```php
'transport' => ['user' => 'local'],
```

**scenario 内の 1 リソースを上書き:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## コード API

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // granular key の解決パッケージ
Transport::authSource();                       // 認証設定を所有するアプリ、または null
Transport::sharesAuthWith($guest, $host);      // アプリ間認証チェック
Transport::resolved();                         // すべての granular key → スコープ
Transport::activeScenarios();                  // 例: ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Database

プラットフォームスコープのテーブルは接続 **`platform`** とプレフィックス **`pinx_`** を使用します。

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## 関連ドキュメント

- [app.php マニフェスト](../start/app-manifest.md)
- [ユーザー管理](./user-management.md)
- [アクセスと権限](./access-permissions.md)
- [ファイル管理](./file-management.md)

---

[← 索引に戻る](../README.md)

# app.php マニフェスト リファレンス

[← 索引に戻る](../README.md)

`app.php` はアプリのマニフェストです。デフォルトは `vendor/pinoox/pincore/Component/Package/data/source.php` にあり、必要なものだけ上書きしてください。

---

## ID と有効化

| キー | 目的 |
|-----|---------|
| `package` | フォルダ名 = 名前空間（`com_acme_shop`） |
| `name` | 表示名 |
| `enable` | アプリの有効 / 無効 |
| `description`, `developer`, `icon` | メタデータ |
| `version-name`, `version-code` | アプリバージョン |
| `sys-app`, `hidden`, `dock` | システムアプリ / 非表示 / Manager ドック |
| `minpin` | 最小プラットフォームバージョン |

---

## Router と boot

| キー | 目的 |
|-----|---------|
| `router.routes` | `routes/*.php` ファイル |
| `boot` | `boot.php` を実行（デフォルト true） |
| `boot-global` | すべての HTTP リクエストで boot |
| `extends` | ホストアプリ boot 時に boot |
| `loader` | 追加ファイル（`func.php`） |
| `depends` | 必須アプリ |

[boot.php とイベント](../advanced/boot-and-events.md) を参照してください。

---

## Flow とセキュリティ

| キー | 目的 |
|-----|---------|
| `flow` | グローバル Flow（BootFlow） |
| `alias` | 名前 → Flow クラス |
| `auth` | mode、lifetime、JWT/cookie |
| `access` | RBAC: `groups`、`super_roles` |
| `transport` | プラットフォームと user/file/access を共有 |

[Flows](../basic/flows.md)、[ユーザー管理](../advanced/user-management.md)、[アクセス](../advanced/access-permissions.md) を参照してください。

---

## UI とテーマ

| キー | 目的 |
|-----|---------|
| `theme` | アクティブなテーマフォルダ |
| `theme-context`, `theme-contexts`, `theme-extends` | マルチコンテキスト / 継承 |
| `frontend` | `stack`、`profile`、`entry`、`manifest` |
| `lang` | デフォルトロケール |
| `open` | Manager 起動動作 |

---

## Database と storage

| キー | 目的 |
|-----|---------|
| `database` | DB 接続の上書き |
| `table.prefix` | テーブルプレフィックス |
| `transport.user` / `file_storage` / `access` | プリセットまたは詳細キー |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## ランタイム

| キー | 目的 |
|-----|---------|
| `runtime.mode`, `runtime.debug` | モードの上書き |
| `cache` | routes/api/boot/twig を Bake |
| `log`, `redis`, `date` | アプリごとの上書き |
| `container` | DI バインディング |

---

## Pinker / Pinx

| キー | 目的 |
|-----|---------|
| `pinx` | type、minpin、sign |
| `build` | パッケージ用 exclude/include |

---

## 統合例

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## 関連ドキュメント

- [プロジェクト構造](./structure.md)
- [Config](../basic/config.md)

---

[← 索引に戻る](../README.md)

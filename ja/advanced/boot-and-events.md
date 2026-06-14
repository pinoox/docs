# boot.php とイベント

[← 索引に戻る](../README.md)

`routes/` に加え、**`boot.php`** でルート、API エンドポイント、Flow、Schedule、リスナーを登録できます — **プラグイン**、マイクロモジュール、ホストアプリ（例: manager）へのフックに有用です。

---

## 3 つのアプリモード

| モード | 設定 | 動作 |
|------|--------|----------|
| **Route のみ** | `router.routes` のみ | アプリ URL がアクティブなとき実行 |
| **Boot global** | `boot-global => true` | すべての HTTP リクエストで boot |
| **Boot + Route** | `boot.php` + routes | デフォルトスキャフォールディング |

ホストアプリ上のプラグイン:

```php
'extends' => ['com_host_app'],
```

プラグインはホストが boot するときのみ boot（global より軽量）。

---

## boot 用の `app.php` キー

`apps/{package}/app.php` のこれらのキーは、**いつ** `boot.php` を実行するか、キャッシュするかを制御します。boot パイプラインの設定であり、`boot.php` の代替ではありません。

### boot ファイル（`boot`）

| 値 | デフォルト | 動作 |
|----|------------|------|
| `true` | はい | この app の boot 時に `boot.php` を実行 |
| `false` | | boot なし — ルートのみ |
| `'path/custom.php'` | | app ルートからの別ファイル |

ファイルは **callable を返す**必要があります。`true` でファイルが無くてもエラーになりません。

### グローバル plugin（`boot-global`）

| 値 | デフォルト | 動作 |
|----|------------|------|
| `false` | はい | この app がアクティブなときのみ |
| `true` | | **すべての HTTP リクエスト**で boot |

### ホスト plugin（`extends`）

| 値 | デフォルト | 動作 |
|----|------------|------|
| `[]` | はい | 通常の app |
| `['com_host_app']` | | ホストがアクティブになる **前に** boot |

### 追加登録（`startup`）

`app.php` 内の optional callable。`boot.php` の **後** に同じ API で実行。

### boot キャッシュ（`cache`）

opt-in: `cache.enabled` を `true` に。デプロイ後: `php pinoox cache:build {package}`。

### 早見表

| 目的 | 設定 |
|------|------|
| 通常 app | `'boot' => true` |
| ルートのみ | `'boot' => false` |
| 全站 plugin | `'boot-global' => true` |
| ホスト plugin | `'extends' => ['com_host_app']` |

---

## 基本的な boot.php

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## AppRegister — よく使うメソッド

| メソッド | 目的 |
|--------|---------|
| `web(callable)` | ビルダー経由でルート登録 |
| `route([...])` | 単一 web ルート |
| `api([manifest])` | 完全な API マニフェスト |
| `apiRoute([...])` | 単一 API エンドポイント |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow エイリアス |
| `schedule(callable)` | スケジュールタスク |
| `listen('event', listener)` | イベントリスナー |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | 別アプリ boot 時のフック |

---

## Event Portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Controller からメールを分離する例は [メール](./mail.md) を参照。

**Flow** = Controller の前（ミドルウェア）。**Event** = アクションの後（副作用）。

---

## ヘルパー

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot cache

`app.php` の `cache.stores` 下の `'boot' => true` で Pinker 経由 boot を bake — [Pinker](./pinker.md) を参照。

---

## 関連ドキュメント

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Router](../basic/routers.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)

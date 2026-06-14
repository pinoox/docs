# boot.php とイベント

[← 索引に戻る](../README.md)

`routes/` に加え、**`boot.php`** でルート、API エンドポイント、Flow、Schedule、リスナーを登録できます — **プラグイン**、マイクロモジュール、ホストアプリ（例: manager）へのフックに有用です。

各 app は `apps/{package}/boot.php` を置けます。ファイルは `AppRegister` を受け取る closure を返し、リクエスト処理の**前**に実行されます。

---

## ライフサイクル

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### パイプライン段階

| 段階 | 目的 |
|------|------|
| `boot.global` | 各リクエストで `boot-global => true` の app を boot |
| `app.boot` | アクティブな route app を boot（`extends` の extender 含む） |

### boot イベント

| 名前 | タイミング |
|------|------------|
| `app.booting` / `app.booting.{package}` | commit 前 |
| `app.booted` / `app.booted.{package}` | integrate 後 |
| `app.routes` / `app.routes.{package}` | Web ルート適用時 |
| `app.api` / `app.api.{package}` | API registry 構築時 |

`boot.php` から listen:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### コア request イベント

各 HTTP リクエストでフレームワークが自動 dispatch（`AppCoreEventSubscriber`）:

| 名前 | タイミング | package 変種 | 名前付きチャネル |
|------|------------|--------------|------------------|
| `app.route.matched` | ルート match 後 | `app.route.matched.{package}` | `app.route.{routeName}` または `app.api.{routeName}` |
| `app.controller` | controller 実行前 | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | レスポンス送信前 | `app.response.{package}` | — |
| `app.exception` | 未処理 exception 時 | `app.exception.{package}` | — |
| `app.terminate` | レスポンス送信後 | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

簡単な hook は **watch**（`onRoute`, `onApi`, …）、完全制御はコアイベントへの **listen**。

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

## Theme — コンテキスト、継承、boot フック

`apps/{package}/theme/{name}/` に配置。**`app.php`** で active theme、**`boot.php`** で runtime フック。

### `app.php` キー

| キー | 用途 |
|------|------|
| `theme` | アクティブ theme フォルダ |
| `theme-context` / `theme-contexts` | 複数 theme |
| `theme-extends` | 継承 |
| `path-theme` | カスタムパス |
| `frontend` | Vite profile, entry, manifest |

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Routes: `flows: ['auth', 'theme.panel']`. `theme/{name}/`: `theme.php`, Twig, `functions.php`, `frontend.config.php`, `src/` / `dist/`.

[Views](../basic/views.md)、[Twig](../basic/templates.md)、[app.php](../start/app-manifest.md) を参照。

### `boot.php` から

**`onTheme`** または **listen** / **watch**:

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

Controller: `View::changeTheme('panel')`、`ThemeContext::activate('panel')`、`within_theme(...)`。

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

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

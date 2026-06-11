# URL とリンク生成

[← 索引に戻る](../README.md)

Pinoox 3.x では内部 URL の構築に **`url()`** を使用します。このヘルパーは **`Url::link()`** を使用し、ドメイン、インストールパス（サブフォルダ）、現在のアプリセグメントを認識します。

> **`Url::get()`** や **`Url::app()`** は使用しないでください。代わりに **`url()`**、**`Url::link()`**、**`Url::forApp()`** を使用してください。

---

## PHP — `url()` ヘルパー

```php
// アクティブアプリ内の相対リンク
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// 引数なしのアクセサ
$accessor = url();
echo $accessor->app;               // アプリベース URL
echo $accessor->site;              // origin + プロジェクトパス
echo $accessor->api;               // API プレフィックス

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // url('products') と同じ
echo Url::forApp('com_acme_shop'); // 特定アプリのベース URL
echo Url::current();               // 現在のページ URL
echo Url::origin();                // https://example.com/pinoox
```

アプリベース外のリンクには `^` または `~` プレフィックス:

```php
echo url('^about');                // プロジェクトルートから
echo Url::link('^config/app.php');
```

---

## Twig — `url()` アクセサ

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| アクセサメソッド | 目的 |
|-----------------|---------|
| `url().site` | origin + プロジェクトパス |
| `url().app` | origin + アプリセグメント |
| `url().api` | API プレフィックス（デフォルト `api/v1/`） |
| `url().resource('resources/logo.png')` | `apps/{package}/` 配下の静的ファイル |
| `url('profile')` | アプリ内のルートリンク |

---

## ルート名 — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## テーマアセット — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // アクティブテーマ内ファイルの URL
```

---

## Controller でのメニュー例

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## リクエスト情報

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## ヒント

- リンクをハードコードしない。常に `url()` または `Url::link()` を使用
- `apps/{package}/resources/` のファイルは `url().resource()` または `asset()`。テーマファイルは **`assets()`**
- ベース URL は設定で手動設定しない。HTTP リクエストから検出される

---

## 関連ドキュメント

- [ファイルパス](./path.md)
- [View](./views.md)
- [Twig テンプレート](./templates.md)
- [Router](./routers.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)

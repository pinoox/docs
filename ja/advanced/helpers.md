# グローバル Helpers

[← 索引に戻る](../README.md)

Pinoox 3.x は `pincore/functions/` からグローバルヘルパーを読み込みます。日常のアプリ開発ではこれらのヘルパー（と Portal）で十分です — コア Component を直接インスタンス化しないでください。

---

## 主要ヘルパー

| ヘルパー | 目的 | 例 |
|--------|---------|---------|
| `render()` | 文字列として HTML | `$html = render('email', $data);` |
| `response()` | HTTP レスポンス | `return response()->json($data);` |
| `redirect()` | リダイレクト | `return redirect(url('login'));` |
| `url()` | アプリ/サイト URL | `url('products')` |
| `path()` | ディスク上のファイルパス | `path('storage/logs/app.log')` |
| `assets()` | テーマファイル URL | `assets('dist/app.css')` |
| `config()` | 設定の読み書き | `config('app.name')` |
| `t()` | 翻訳（return） | `t('welcome.title')` |
| `lang()` | 翻訳（echo） | `lang('welcome.title')` |
| `app()` | アクティブアプリ | `app()->get('package')` |
| `auth()` | ログインユーザー | `auth()` → `Auth::user()` |
| `user()` | ユーザーフィールド | `user('email')` |
| `isLogin()` | ログイン状態 | `if (isLogin()) { … }` |
| `session()` | セッション | `session('token')` |
| `runtime()` | アクティブ HTTP カーネル | `runtime()->getRequest()` |
| `_env()` | 環境変数 | `_env('APP_DEBUG', false)` |
| `alias()` | Flow/クラスエイリアス | `alias('auth')` |

Controller の HTML には **`View::render()`** を使用（システムアプリと同じ）。`view()` ヘルパーも存在しますが Controller では Portal を優先してください。

---

## Request — 注入または `runtime()`

pincore にグローバル `request()` ヘルパーはありません。Controller と Component では型ヒント注入を使用します。

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

Flow などシグネチャで注入できない場所:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`、`user()`、Flow

```php
// 現在のユーザー（Auth::user()）
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) は user($key) と同じ
$email = auth('email');

// Flow エイリアスでルートを保護
// app.php → 'alias' => ['auth' => AuthFlow::class]
// routes → ->flows(['auth']) または flows 付き group
```

---

## View と Response

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## Config

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## Lang

```php
$label = t('product.title');
// Twig 内: {{ t('product.title') }}
```

---

## URL と Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## カスタムアプリヘルパー

`app.php` 内:

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return '$' . number_format($amount, 2);
}
```

---

## Twig ヘルパー（テンプレート内）

PHP ヘルパーに加え、Twig でも以下が利用できます。

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## ヒント

- Controller の HTML には `View::render()`。日常タスクには `url()`、`t()`、`config()` などのヘルパー
- ヘルパーは Pinoox bootstrap 後にのみ動作 — `index.php` / `pinoox` 外の生 PHP スクリプトでは読み込まない
- 複雑なロジックはカスタムヘルパーより `Component/` + Portal を優先

---

## 関連ドキュメント

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [言語](../basic/language.md)
- [Services](./services.md)

---

[← 索引に戻る](../README.md)

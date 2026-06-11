# Flow（ミドルウェア）

[← 索引に戻る](../README.md)

Flow は Pinoox のミドルウェアレイヤーです。Controller アクションの前に実行されます。ブートストラップ、認証、認可などの横断的関心事に使用します。

---

## アプリ全体の Flow — `before()` メソッド

boot とセットアップ（セッション、グローバル View データなど）には **`before(Request $request)`** を使用します。

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Flow\Flow;
use Pinoox\Component\Http\Request;
use Pinoox\Portal\View;

class BootFlow extends Flow
{
    protected function before(Request $request): void
    {
        View::set('siteName', config('app.name'));
    }
}
```

パス: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth Flow — `AuthFlow` を継承

ログインガードには **`Pinoox\Flow\AuthFlow`** を継承し、**`exit()`** を実装します。

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Router\Route;
use Pinoox\Flow\AuthFlow;
use Pinoox\Portal\Auth;

class ShopAuthFlow extends AuthFlow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }

    protected function exit(Request $request, Route $route)
    {
        return redirect(url('login'));
    }
}
```

ユーザーがゲストの場合、`AuthFlow` は `exit()` を呼び出します。API では JSON エラーを返せます。

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## `handle()` によるカスタム Flow

必要に応じて **`handle(Request $request, Closure $next)`** を直接オーバーライドします。

```php
protected function handle(Request $request, \Closure $next)
{
    if (!$this->userCanAccess($request)) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    return $next($request);
}
```

---

## app.php にエイリアスを登録

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // アプリ内のすべてのルートで実行
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — アプリ全体の Flow（常に実行）
- **`alias`** — ルートで使う短い名前

---

## ネストされたエイリアス（Manager パターン）

Flow エイリアスはグループ化のためにネストできます。

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

API マニフェストではキー **`manager.auth`** を使用します。

```php
// routes/api/private.php
[
    'method' => 'GET',
    'uri' => '/user/get',
    'action' => [UserController::class, 'get'],
    'name' => 'user.get',
    'flow' => ['manager.auth'],
],
```

---

## ルートに Flow を適用

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

複数の Flow:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## ルートグループへの Flow

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## チェーンの停止

Flow が HTTP レスポンス（リダイレクト、エラー JSON など）を返すと、Controller アクションは実行されません。

---

## ガイドライン

- Flow はアプリの `Flow/` フォルダに置く — pincore ではない
- エイリアスは常に `app.php` に登録する
- アプリ全体の boot: `flow` マニフェストの `before()`
- ログインガード: `AuthFlow` + `exit()`

---

## 関連ドキュメント

- [Router](./routers.md)
- [Controller](./controllers.md)
- [Request](./requests.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)

# ユーザー管理

[← 索引に戻る](../README.md)

Pinoox 3.x の認証は **pincore** に集約されています。アプリは `app.php` で `auth` と `transport.user` を設定し、`Auth` と `User` Portal を使用するだけです — アプリ内でログインロジックを重複させないでください。

---

## Portal とヘルパー

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## app.php 設定

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // プラットフォームと UserModel を共有
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // cookie 名 / SPA キー
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // または .env の PINOOX_JWT_SECRET
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| transport 値 | 意味 |
|-----------------|---------|
| `platform` | 共有ユーザー（`pinx_user` / `pincore_user`） |
| `local` | このアプリ専用のユーザーテーブル |
| `{package}` | 別アプリの UserModel を使用 |

---

## BootFlow 内の `Auth::boot()`

```php
<?php
namespace App\com_acme_portal\Flow;

use Pinoox\Component\Flow\Flow;
use Pinoox\Component\Http\Request;
use Pinoox\Portal\Auth;

class BootFlow extends Flow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }
}
```

`Auth::boot()` はアクティブアプリの `auth` 設定をガードに適用します。これがないと `attempt()` と `check()` が正しく動作しない場合があります。

---

## ログイン

新しい API Controller は `ApiController`（pincore）を継承し `ok()` / `fail()` を使用します。

```php
<?php

namespace App\com_acme_portal\Controller;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;
use Pinoox\Portal\Auth;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        Auth::boot();

        if (!Auth::guest()) {
            return $this->fail('ACCESS_DENIED', 'user.already_logged_in', status: 401);
        }

        $input = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $result = Auth::attemptResult([
            'username' => $input['username'],
            'password' => $input['password'],
        ], remember: (bool) ($input['remember'] ?? false));

        if (!$result->success) {
            return $this->fail('ACCESS_DENIED', $result->message ?? 'user.invalid_credentials');
        }

        return $this->ok(['token' => $result->token], 'user.logged_in_successfully');
    }
}
```

システムアプリ `com_pinoox_manager` は同じロジックですが `$this->error()` と `$this->message()`（レガシー基底クラス）を呼び出します — どちらも標準 JSON エンベロープにマップされます。

使用可能な認証情報フィールド: `username`、`email`、または `login`。

---

## Flow でルートを保護

### API — `routes/api/*.php` 内のマニフェスト

```php
return [
    [
        'method' => 'GET',
        'uri' => '/orders',
        'action' => [OrderController::class, 'index'],
        'name' => 'orders.index',
        'flow' => ['auth'],
    ],
];
```

グループ化エイリアス（manager など）:

```php
// app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],

// routes/api/private.php
'flow' => ['manager.auth'],
```

### Web — flows 付き `group`

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` は `Auth::guest()` が true のときアクションを停止します。API では `exit()` をオーバーライドできます。

```php
class ManagerAuthFlow extends AuthFlow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }

    protected function exit(Request $request, Route $route)
    {
        return ApiResponse::error('ACCESS_DENIED', 'Access denied!', status: 401);
    }
}
```

---

## よく使う API メソッド

| メソッド | 目的 |
|--------|---------|
| `Auth::check()` / `guest()` | ログイン状態 |
| `Auth::user()` / `id()` | 現在のユーザー |
| `Auth::profile()` | API 用プロフィール DTO |
| `Auth::updateProfile($id, $data)` | 名前/email を更新 |
| `Auth::changePassword($id, $old, $new)` | パスワード変更 |
| `Auth::create($data)` / `remove($id)` | ユーザー CRUD |
| `Auth::revokeSessions($userId)` | すべてのトークンを失効 |

---

## イベント

| イベント | 名前 |
|-------|------|
| `UserAuthenticated` | `user.authenticated` |
| `UserLoggedOut` | `user.logged_out` |
| `UserLoginFailed` | `user.login_failed` |

```php
use Pinoox\Portal\Event;
use Pinoox\Component\User\Event\UserAuthenticated;

Event::listen(UserAuthenticated::$eventName, function (UserAuthenticated $event) {
    // $event->user
});
```

---

## アーキテクチャ

```
app.php (auth + transport.user)
        ↓
Portal.Auth / Portal.User
        ↓
Manager → Guard → AuthSession (cookie/session/jwt)
        ↓
UserModel (pincore_user) + TokenModel (pincore_token)
```

---

## HMVC のヒント

1. 認証ロジックは pincore にあり — アプリで重複させない
2. BootFlow でグローバルに `Auth::boot()` を呼ぶ
3. 各 Controller で `if (!isLogin())` ではなく Flow エイリアス（`->flows()` または API マニフェストの `'flow'`）でルートを保護
4. コンシューマーアプリでは `'transport' => ['user' => 'platform']` で十分なことが多い

---

## 関連ドキュメント

- [トークン管理](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← 索引に戻る](../README.md)

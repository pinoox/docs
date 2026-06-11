# トークン管理

[← 索引に戻る](../README.md)

Pinoox 3.x ではセッションと JWT は **`TokenModel`**（`pincore_token`）と内部 pincore ガードで管理されます。アプリは `app.php` の `auth` ブロックでモードを選択します。API と SPA では通常 **`jwt`** を推奨します。

---

## app.php の JWT 設定

```php
'auth' => [
    'mode' => 'jwt',
    'key' => 'manager_pinoox',
    'lifetime' => 30,
    'lifetime_unit' => 'day',
    'remember_lifetime' => 365,
    'remember_unit' => 'day',
    'jwt_secret' => null,
],
```

プロジェクト `.env` のシークレット:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` は `TokenModel` 行のスコープを設定します（例: アプリ間共有の `platform`）。

---

## ログイン後のトークン

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // またはログイン後 Auth::token()
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // 現在の JWT または認証文字列
}
```

クライアント（Vue/React）はヘッダーでトークンを送信:

```http
Authorization: Bearer {token}
```

または `auth.key` で定義されたキーを cookie/localStorage 経由（SPA による）。

---

## TokenModel

```php
namespace Pinoox\Model;

class TokenModel extends Model
{
    protected $table = Table::TOKEN;
    protected $fillable = [
        'token_key', 'token_name', 'token_data',
        'user_id', 'remote_url', 'app',
        'ip', 'user_agent', 'expiration_date',
    ];
    protected $casts = ['token_data' => 'json'];
}
```

`app` 列は transport スコープでフィルタ — トークン行はアプリごとに共有または分離できます。

---

## revokeSessions — すべてのセッションを失効

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// user_id のすべての TokenModel 行が削除される
```

使用例:

- すべてのデバイスからサインアウト
- 強制パスワード変更後
- ユーザーをブロック（`Auth::setStatus($id, UserModel::SUSPEND)` + revoke）

```php
public function logoutAllDevices(Request $request)
{
    Auth::boot();
    $userId = Auth::id();

    Auth::revokeSessions($userId);
    Auth::logout();

    return $this->ok(null, 'user.sessions_revoked');
}
```

---

## persistClientJwt（SPA）

```php
Auth::persistClientJwt($jwt);
```

トークン更新後にクライアントに JWT を永続化する際に使用します。

---

## auth モード

| mode | 動作 |
|------|----------|
| `jwt` | ヘッダー/cookie のトークン。API と SPA に適する |
| `session` | PHP サーバーセッション |
| `cookie` | cookie 内の暗号化認証情報 |

---

## セキュリティのヒント

- 本番環境では必ず `PINOOX_JWT_SECRET` を設定
- 短い lifetime + 長い remember で UX を改善
- `changePassword` 後に `revokeSessions` を呼ぶ
- Transport `session_token => platform` は 1 アプリでのログアウトが共有トークンにも影響

---

## 関連ドキュメント

- [ユーザー管理](./user-management.md)
- [Transport](./transport.md)
- [Response](../basic/responses.md)

---

[← 索引に戻る](../README.md)

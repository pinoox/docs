# 令牌管理（Token Management）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，会话（Session）和 JWT 由 **`TokenModel`**（`pincore_token`）以及 pincore 内部的 guard 管理。应用通过 `app.php` 中的 `auth` 配置块选择模式；对于 API 和 SPA，通常推荐使用 **`jwt`**。

---

## app.php 中的 JWT 配置

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

密钥放在项目的 `.env` 中：

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` 设置 `TokenModel` 行的作用域（例如 `platform` 表示跨应用共享）。

---

## 登录后的令牌

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // 或登录后调用 Auth::token()
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // 当前 JWT 或凭据字符串
}
```

客户端（Vue/React）通过请求头发送令牌：

```http
Authorization: Bearer {token}
```

或者通过 `auth.key` 中定义的键存放在 cookie/localStorage 中（取决于 SPA 的实现）。

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

`app` 列会按 transport 作用域过滤 —— 令牌记录可以在应用之间共享，也可以按应用隔离。

---

## revokeSessions —— 撤销所有会话

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// 该 user_id 的所有 TokenModel 记录都会被删除
```

使用场景：

- 在所有设备上退出登录
- 强制修改密码之后
- 封禁用户（`Auth::setStatus($id, UserModel::SUSPEND)` + 撤销）

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

用于在刷新令牌后，把 JWT 持久化到客户端。

---

## auth 模式

| 模式 | 行为 |
|------|----------|
| `jwt` | 令牌位于请求头/cookie；适合 API 和 SPA |
| `session` | PHP 服务端会话 |
| `cookie` | cookie 中存放加密凭据 |

---

## 安全提示

- 生产环境务必设置 `PINOOX_JWT_SECRET`。
- 较短的 lifetime + 较长的 remember 可以改善用户体验。
- 调用 `changePassword` 后请调用 `revokeSessions`。
- transport 设置 `session_token => platform` 时，在一个应用中退出登录也会影响共享令牌。

---

## 相关文档

- [用户管理（User management）](./user-management.md)
- [Transport](./transport.md)
- [响应（Responses）](../basic/responses.md)

---

[← 返回索引](../README.md)

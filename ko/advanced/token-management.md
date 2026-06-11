# Token Management

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 session과 JWT는 **`TokenModel`**(`pincore_token`)과 internal pincore guard로 관리됩니다. 앱은 `app.php`의 `auth` block으로 mode를 선택; API와 SPA에는 보통 **`jwt`** 권장.

---

## app.php JWT configuration

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

프로젝트 `.env`의 secret:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token`이 `TokenModel` row scope 설정 (예: app 간 공유는 `platform`).

---

## Login 후 token

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // or Auth::token() after login
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // current JWT or credential string
}
```

Client(Vue/React)는 header로 token 전송:

```http
Authorization: Bearer {token}
```

또는 `auth.key`에 정의된 key로 cookie/localStorage(SPA에 따라).

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

`app` column은 transport scope로 filter — token row 공유 또는 앱별 격리.

---

## revokeSessions — 모든 session revoke

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// all TokenModel rows for user_id are removed
```

사용 사례:

- 모든 device에서 sign out
- 강제 password 변경 후
- 사용자 차단 (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

## persistClientJwt (SPA)

```php
Auth::persistClientJwt($jwt);
```

token refresh 후 client에 JWT persist할 때 사용.

---

## auth mode

| mode | Behavior |
|------|----------|
| `jwt` | header/cookie의 token; API와 SPA에 적합 |
| `session` | PHP server session |
| `cookie` | cookie의 encrypted credential |

---

## Security tips

- production에서 항상 `PINOOX_JWT_SECRET` 설정.
- 짧은 lifetime + 긴 remember로 UX 개선.
- `changePassword` 후 `revokeSessions` 호출.
- Transport `session_token => platform`이면 한 앱 logout이 shared token에도 영향.

---

## 관련 문서

- [User management](./user-management.md)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)

---

[← 색인으로 돌아가기](../README.md)

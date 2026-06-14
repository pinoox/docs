# Token Management

[← Back to index](../README.md)

In Pinoox 3.x, sessions and JWTs are managed by **`TokenModel`** (`pincore_token`) and the internal pincore guard. The app selects the mode via the `auth` block in `app.php`; for APIs and SPAs, **`jwt`** is usually recommended.

---

## JWT configuration in app.php

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

Secret in the project `.env`:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` sets the scope for `TokenModel` rows (e.g. `platform` for sharing across apps).

---

## Token after login

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

The client (Vue/React) sends the token in a header:

```http
Authorization: Bearer {token}
```

Or via the key defined in `auth.key` in cookie/localStorage (depending on the SPA).

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

The `app` column is filtered by transport scope — token rows can be shared or isolated per app.

---

## revokeSessions — revoke all sessions

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// all TokenModel rows for user_id are removed
```

Use cases:

- Sign out from all devices
- After a forced password change
- Block a user (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

Used to persist the JWT on the client after a token refresh.

---

## auth modes

| mode | Behavior |
|------|----------|
| `jwt` | Token in header/cookie; suited for API and SPA |
| `session` | PHP server session |
| `cookie` | Encrypted credential in cookie |

---

## Security tips

- Always set `PINOOX_JWT_SECRET` in production.
- Short lifetime + long remember improves UX.
- Call `revokeSessions` after `changePassword`.
- Transport `session_token => platform` means logout in one app affects shared tokens too.

---

## CLI (terminal)

Inspect and manage session rows in `TokenModel` for the active transport scope:

| Command | Purpose |
|---------|---------|
| `token:list {package}` | List tokens (`--json`) |
| `token:show {token}` | Details for id or `token_key` |
| `token:create` | Create token (`--user`, `--name`, `--lifetime`, `--unit`) |
| `token:update` / `token:delete` | Change metadata or remove one token |
| `token:revoke-user {user}` | Same effect as `Auth::revokeSessions($userId)` |
| `token:purge` | Delete expired tokens |

Alias: `tokens` → `token:list`.

```bash
php pinoox token:list platform
php pinoox token:create com_my_shop --user=1 --lifetime=7 --unit=day
php pinoox token:revoke-user 1 --force
```

CLI token creation sets `ip=127.0.0.1` and `user_agent=pinoox-cli` so commands work outside HTTP requests.

See [CLI reference](../start/cli-reference.md).

---

## Related docs

- [User management](./user-management.md)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)

---

[← Back to index](../README.md)

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
    return $this->ok([
        'token' => $result->token,
        'user' => Auth::clientUser($result->user),
    ], 'Logged in successfully');
}
```

`Auth::clientUser()` is the shared SPA user envelope — see [User management](./user-management.md).

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // current JWT or credential string
}
```

The SPA should send **both**:

```http
Authorization: Bearer {token}
Cookie: {auth.key}={jwt}   # HttpOnly; browser sends this when credentials: 'include'
```

| Store | Readable by JS? | Role |
|-------|-----------------|------|
| HttpOnly cookie (`auth.key`) | No | Server session for same-origin API |
| `localStorage[auth.key]` | Yes | `Authorization` header + offline check |

`@pinooxhq/auth` syncs them: login stores the returned `token` in localStorage; `me()` always uses `credentials: 'include'` so a cookie-only session still hydrates when storage was cleared. On `ALREADY_AUTHENTICATED`, it logs out the cookie session and retries login once.

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
- Prefer HttpOnly cookies for the server session; do not rely on JS reading `auth.key` from `document.cookie`.
- Keep AuthController thin (`token` + `Auth::clientUser()`); leave client recovery to `@pinooxhq/auth`.

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

- [User management](./user-management.md) (`Auth::clientUser`, SPA hydrate)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)
- Package: [`@pinooxhq/auth`](https://github.com/pinoox/auth)

---

[← Back to index](../README.md)

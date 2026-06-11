# Token-Verwaltung

[← Zurück zur Übersicht](../README.md)

In Pinoox 3.x werden Sessions und JWTs vom **`TokenModel`** (`pincore_token`) und dem internen pincore-Guard verwaltet. Die App wählt den Modus über den `auth`-Block in `app.php`; für APIs und SPAs wird in der Regel **`jwt`** empfohlen.

---

## JWT-Konfiguration in app.php

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

Secret in der Projekt-`.env`:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` legt den Scope für `TokenModel`-Zeilen fest (z. B. `platform`, um sie zwischen Apps zu teilen).

---

## Token nach dem Login

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // oder Auth::token() nach dem Login
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // aktuelles JWT oder Credential-String
}
```

Der Client (Vue/React) sendet das Token in einem Header:

```http
Authorization: Bearer {token}
```

Oder über den in `auth.key` definierten Schlüssel im Cookie/localStorage (je nach SPA).

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

Die Spalte `app` wird nach dem Transport-Scope gefiltert — Token-Zeilen können pro App geteilt oder isoliert sein.

---

## revokeSessions — alle Sessions widerrufen

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// alle TokenModel-Zeilen für user_id werden entfernt
```

Anwendungsfälle:

- Abmeldung von allen Geräten
- Nach einer erzwungenen Passwortänderung
- Einen Benutzer sperren (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

Wird verwendet, um das JWT nach einem Token-Refresh auf dem Client zu persistieren.

---

## auth-Modi

| mode | Verhalten |
|------|----------|
| `jwt` | Token im Header/Cookie; geeignet für API und SPA |
| `session` | PHP-Server-Session |
| `cookie` | Verschlüsseltes Credential im Cookie |

---

## Sicherheitstipps

- Setzen Sie in Produktion immer `PINOOX_JWT_SECRET`.
- Kurze Lifetime + langes Remember verbessert die UX.
- Rufen Sie `revokeSessions` nach `changePassword` auf.
- Transport `session_token => platform` bedeutet: Logout in einer App betrifft auch geteilte Tokens.

---

## Verwandte Dokumente

- [Benutzerverwaltung (User management)](./user-management.md)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)

---

[← Zurück zur Übersicht](../README.md)

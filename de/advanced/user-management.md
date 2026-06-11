# Benutzerverwaltung (User Management)

[← Zurück zur Übersicht](../README.md)

Die Authentifizierung ist in Pinoox 3.x zentral in **pincore** angesiedelt. Apps konfigurieren lediglich `auth` und `transport.user` in `app.php` und verwenden die Portals `Auth` und `User` — duplizieren Sie keine Login-Logik in der App.

---

## Portals und Helpers

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## Konfiguration in app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // UserModel mit der Plattform teilen
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // Cookie-Name / SPA-Schlüssel
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // oder PINOOX_JWT_SECRET in .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| transport-Wert | Bedeutung |
|-----------------|---------|
| `platform` | Gemeinsame Benutzer (`pinx_user` / `pincore_user`) |
| `local` | Benutzertabelle nur für diese App |
| `{package}` | UserModel einer anderen App verwenden |

---

## `Auth::boot()` im BootFlow

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

`Auth::boot()` wendet die `auth`-Einstellungen der aktiven App auf den Guard an. Ohne diesen Aufruf funktionieren `attempt()` und `check()` möglicherweise nicht korrekt.

---

## Login

Neue API-Controller erweitern `ApiController` (pincore) und verwenden `ok()` / `fail()`:

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

Die System-App `com_pinoox_manager` verwendet dieselbe Logik, ruft aber `$this->error()` und `$this->message()` auf (Legacy-Basisklasse) — beide werden auf das Standard-JSON-Envelope abgebildet.

Erlaubte Credential-Felder: `username`, `email` oder `login`.

---

## Routen mit Flow schützen

### API — Manifest in `routes/api/*.php`

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

Für einen gruppierten Alias (wie beim Manager):

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

### Web — `group` mit Flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` stoppt die Action, wenn `Auth::guest()` true ist. Für APIs können Sie `exit()` überschreiben:

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

## Häufig verwendete API-Methoden

| Methode | Zweck |
|--------|---------|
| `Auth::check()` / `guest()` | Login-Status |
| `Auth::user()` / `id()` | Aktueller Benutzer |
| `Auth::profile()` | Profil-DTO für die API |
| `Auth::updateProfile($id, $data)` | Name/E-Mail aktualisieren |
| `Auth::changePassword($id, $old, $new)` | Passwort ändern |
| `Auth::create($data)` / `remove($id)` | Benutzer-CRUD |
| `Auth::revokeSessions($userId)` | Alle Tokens widerrufen |

---

## Events

| Event | Name |
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

## Architektur

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

## HMVC-Tipps

1. Auth-Logik lebt in pincore — duplizieren Sie sie nicht in Apps.
2. Rufen Sie `Auth::boot()` global im BootFlow auf.
3. Schützen Sie Routen mit Flow-Aliassen (`->flows()` oder `'flow'` im API-Manifest), nicht mit `if (!isLogin())` in jedem Controller.
4. Für Consumer-Apps reicht oft `'transport' => ['user' => 'platform']`.

---

## Verwandte Dokumente

- [Token-Verwaltung](./token-management.md)
- [Flows](../basic/flows.md)
- [Transport](./transport.md)

---

[← Zurück zur Übersicht](../README.md)

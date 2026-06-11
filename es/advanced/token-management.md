# Gestión de tokens

[← Volver al índice](../README.md)

En Pinoox 3.x, las sesiones y los JWT los gestiona **`TokenModel`** (`pincore_token`) y el guard interno de pincore. La app elige el modo mediante el bloque `auth` en `app.php`; para APIs y SPAs, **`jwt`** suele ser lo recomendado.

---

## Configuración JWT en app.php

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

Secreto en el `.env` del proyecto:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` define el ámbito de las filas `TokenModel` (p. ej. `platform` para compartir entre apps).

---

## Token tras el login

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // o Auth::token() tras el login
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // JWT actual o cadena de credencial
}
```

El cliente (Vue/React) envía el token en una cabecera:

```http
Authorization: Bearer {token}
```

O mediante la clave definida en `auth.key` en cookie/localStorage (según el SPA).

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

La columna `app` se filtra por ámbito transport — las filas de token pueden compartirse o aislarse por app.

---

## revokeSessions — revocar todas las sesiones

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// se eliminan todas las filas TokenModel para user_id
```

Casos de uso:

- Cerrar sesión en todos los dispositivos
- Tras un cambio de contraseña forzado
- Bloquear un usuario (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

Se usa para persistir el JWT en el cliente tras una renovación de token.

---

## Modos auth

| mode | Comportamiento |
|------|----------|
| `jwt` | Token en cabecera/cookie; adecuado para API y SPA |
| `session` | Sesión PHP en servidor |
| `cookie` | Credencial cifrada en cookie |

---

## Consejos de seguridad

- Establece siempre `PINOOX_JWT_SECRET` en producción.
- Vida corta + remember largo mejora la UX.
- Llama a `revokeSessions` tras `changePassword`.
- Transport `session_token => platform` implica que el logout en una app afecta también a tokens compartidos.

---

## Documentación relacionada

- [Gestión de usuarios](./user-management.md)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)

---

[← Volver al índice](../README.md)

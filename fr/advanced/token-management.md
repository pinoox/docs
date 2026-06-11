# Gestion des tokens

[← Retour à l'index](../README.md)

Dans Pinoox 3.x, les sessions et les JWT sont gérés par **`TokenModel`** (`pincore_token`) et le guard interne de pincore. L'application choisit le mode via le bloc `auth` dans `app.php` ; pour les API et les SPA, **`jwt`** est généralement recommandé.

---

## Configuration JWT dans app.php

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

Le secret dans le `.env` du projet :

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` définit la portée des lignes de `TokenModel` (par ex. `platform` pour un partage entre applications).

---

## Token après connexion

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // ou Auth::token() après la connexion
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // JWT courant ou chaîne d'identification
}
```

Le client (Vue/React) envoie le token dans un en-tête :

```http
Authorization: Bearer {token}
```

Ou via la clé définie dans `auth.key` en cookie/localStorage (selon la SPA).

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

La colonne `app` est filtrée par la portée de transport — les lignes de tokens peuvent être partagées ou isolées par application.

---

## revokeSessions — révoquer toutes les sessions

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// toutes les lignes TokenModel du user_id sont supprimées
```

Cas d'usage :

- Déconnexion de tous les appareils
- Après un changement de mot de passe forcé
- Bloquer un utilisateur (`Auth::setStatus($id, UserModel::SUSPEND)` + révocation)

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

Utilisé pour persister le JWT côté client après un rafraîchissement de token.

---

## Modes d'authentification

| mode | Comportement |
|------|----------|
| `jwt` | Token dans l'en-tête/cookie ; adapté aux API et SPA |
| `session` | Session PHP côté serveur |
| `cookie` | Identifiant chiffré dans un cookie |

---

## Conseils de sécurité

- Définissez toujours `PINOOX_JWT_SECRET` en production.
- Une durée de vie courte + un « remember » long améliore l'expérience utilisateur.
- Appelez `revokeSessions` après `changePassword`.
- Le transport `session_token => platform` signifie qu'une déconnexion dans une application affecte aussi les tokens partagés.

---

## Documentation associée

- [Gestion des utilisateurs](./user-management.md)
- [Transport](./transport.md)
- [Réponses](../basic/responses.md)

---

[← Retour à l'index](../README.md)

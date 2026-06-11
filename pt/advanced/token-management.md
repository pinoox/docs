# Gerenciamento de Tokens

[← Voltar ao índice](../README.md)

No Pinoox 3.x, sessões e JWTs são gerenciados pelo **`TokenModel`** (`pincore_token`) e pelo guard interno do pincore. O app seleciona o modo através do bloco `auth` no `app.php`; para APIs e SPAs, geralmente recomenda-se **`jwt`**.

---

## Configuração do JWT no app.php

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

Secret no `.env` do projeto:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` define o escopo das linhas do `TokenModel` (por exemplo, `platform` para compartilhar entre apps).

---

## Token após o login

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // ou Auth::token() após o login
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // JWT atual ou string de credencial
}
```

O cliente (Vue/React) envia o token em um cabeçalho:

```http
Authorization: Bearer {token}
```

Ou através da chave definida em `auth.key` no cookie/localStorage (dependendo da SPA).

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

A coluna `app` é filtrada pelo escopo do transport — as linhas de token podem ser compartilhadas ou isoladas por app.

---

## revokeSessions — revogar todas as sessões

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// todas as linhas do TokenModel para o user_id são removidas
```

Casos de uso:

- Sair de todos os dispositivos
- Após uma troca de senha forçada
- Bloquear um usuário (`Auth::setStatus($id, UserModel::SUSPEND)` + revogar)

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

Usado para persistir o JWT no cliente após um refresh do token.

---

## Modos de auth

| mode | Comportamento |
|------|----------|
| `jwt` | Token no header/cookie; adequado para API e SPA |
| `session` | Sessão PHP no servidor |
| `cookie` | Credencial criptografada em cookie |

---

## Dicas de segurança

- Sempre defina `PINOOX_JWT_SECRET` em produção.
- Lifetime curto + remember longo melhora a UX.
- Chame `revokeSessions` após `changePassword`.
- Transport `session_token => platform` significa que o logout em um app também afeta os tokens compartilhados.

---

## Documentação relacionada

- [Gerenciamento de usuários](./user-management.md)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)

---

[← Voltar ao índice](../README.md)

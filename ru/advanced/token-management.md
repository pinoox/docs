# Управление токенами (Token Management)

[← Назад к оглавлению](../README.md)

В Pinoox 3.x сессии и JWT управляются через **`TokenModel`** (`pincore_token`) и внутренний guard pincore. Приложение выбирает режим через блок `auth` в `app.php`; для API и SPA обычно рекомендуется **`jwt`**.

---

## Конфигурация JWT в app.php

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

Секрет в `.env` проекта:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` задаёт область видимости (scope) для строк `TokenModel` (например, `platform` для общего использования между приложениями).

---

## Токен после входа

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // или Auth::token() после входа
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // текущий JWT или строка учётных данных
}
```

Клиент (Vue/React) отправляет токен в заголовке:

```http
Authorization: Bearer {token}
```

Или через ключ, заданный в `auth.key`, в cookie/localStorage (в зависимости от SPA).

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

Колонка `app` фильтруется по области transport — строки токенов могут быть общими или изолированными для каждого приложения.

---

## revokeSessions — отзыв всех сессий

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// все строки TokenModel для user_id удаляются
```

Сценарии использования:

- Выход со всех устройств
- После принудительной смены пароля
- Блокировка пользователя (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

Используется для сохранения JWT на клиенте после обновления токена.

---

## Режимы auth

| Режим | Поведение |
|------|----------|
| `jwt` | Токен в заголовке/cookie; подходит для API и SPA |
| `session` | Серверная PHP-сессия |
| `cookie` | Зашифрованные учётные данные в cookie |

---

## Советы по безопасности

- Всегда задавайте `PINOOX_JWT_SECRET` в production.
- Короткий lifetime + длинный remember улучшают UX.
- Вызывайте `revokeSessions` после `changePassword`.
- Transport `session_token => platform` означает, что выход в одном приложении затрагивает и общие токены.

---

## Связанные документы

- [Управление пользователями](./user-management.md)
- [Transport](./transport.md)
- [Ответы (Responses)](../basic/responses.md)

---

[← Назад к оглавлению](../README.md)

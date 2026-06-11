# Token yönetimi

[← Dizine dön](../README.md)

Pinoox 3.x'te session'lar ve JWT'ler **`TokenModel`** (`pincore_token`) ve dahili pincore guard tarafından yönetilir. Uygulama modu `app.php` içindeki `auth` bloğuyla seçilir; API ve SPA'lar için genellikle **`jwt`** önerilir.

---

## app.php'de JWT yapılandırması

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

Proje `.env` dosyasındaki secret:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token`, `TokenModel` satırları için kapsamı belirler (ör. uygulamalar arası paylaşım için `platform`).

---

## Giriş sonrası token

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

İstemci (Vue/React) token'ı bir başlıkta gönderir:

```http
Authorization: Bearer {token}
```

Veya `auth.key` içinde tanımlanan anahtarla cookie/localStorage üzerinden (SPA'ya bağlı).

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

`app` sütunu transport kapsamına göre filtrelenir — token satırları paylaşılabilir veya uygulama başına izole edilebilir.

---

## revokeSessions — tüm session'ları iptal etme

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// all TokenModel rows for user_id are removed
```

Kullanım senaryoları:

- Tüm cihazlardan çıkış
- Zorunlu şifre değişikliğinden sonra
- Kullanıcıyı engelleme (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

Token yenilemeden sonra JWT'yi istemcide kalıcı hale getirmek için kullanılır.

---

## auth modları

| mod | Davranış |
|------|----------|
| `jwt` | Başlık/cookie'de token; API ve SPA için uygun |
| `session` | PHP sunucu session'ı |
| `cookie` | Cookie'de şifreli kimlik bilgisi |

---

## Güvenlik ipuçları

- Üretimde her zaman `PINOOX_JWT_SECRET` ayarlayın.
- Kısa süre + uzun remember UX'i iyileştirir.
- `changePassword` sonrası `revokeSessions` çağırın.
- Transport `session_token => platform`, bir uygulamadaki çıkışın paylaşılan token'ları da etkilemesi anlamına gelir.

---

## İlgili dokümantasyon

- [Kullanıcı yönetimi](./user-management.md)
- [Transport](./transport.md)
- [Response](../basic/responses.md)

---

[← Dizine dön](../README.md)

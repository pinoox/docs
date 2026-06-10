# مدیریت توکن

در پینوکس 3.x نشست‌ها و JWT از **`TokenModel`** (`pincore_token`) و Guard داخلی pincore مدیریت می‌شوند. اپ با بلوک `auth` در `app.php` حالت را انتخاب می‌کند؛ برای API و SPA معمولاً **`jwt`** پیشنهاد می‌شود.

---

## تنظیم JWT در app.php

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

Secret در `.env` پروژه:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` scope ردیف‌های `TokenModel` را تعیین می‌کند (مثل `platform` برای اشتراک بین اپ‌ها).

---

## دریافت توکن بعد از login

```php
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;
use Pinoox\Portal\Auth;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        Auth::boot();

        $input = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $result = Auth::attemptResult([
            'username' => $input['username'],
            'password' => $input['password'],
        ], remember: true);

        if (!$result->success) {
            return $this->fail('ACCESS_DENIED', $result->message ?? 'user.invalid_credentials');
        }

        return $this->ok(['token' => $result->token], 'user.logged_in_successfully');
    }
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // JWT یا credential string جاری
}
```

کلاینت (Vue/React) توکن را در header می‌فرستد:

```http
Authorization: Bearer {token}
```

یا با کلید تعریف‌شده در `auth.key` در cookie/localStorage (بسته به SPA).

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

ستون `app` با transport scope فیلتر می‌شود — ردیف‌های توکن بین اپ‌های مشترک یا جدا قابل تنظیم است.

---

## revokeSessions — ابطال همه نشست‌ها

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// همه TokenModel های user_id حذف می‌شوند
```

کاربردها:

- خروج از همه دستگاه‌ها
- بعد از تغییر رمز اجباری
- مسدود کردن کاربر (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

```php
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;
use Pinoox\Portal\Auth;

class SessionController extends ApiController
{
    public function logoutAllDevices(Request $request)
    {
        Auth::boot();
        $userId = Auth::id();

        Auth::revokeSessions($userId);
        Auth::logout();

        return $this->ok(null, 'user.sessions_revoked');
    }
}
```

---

## persistClientJwt (SPA)

```php
Auth::persistClientJwt($jwt);
```

برای ذخیره JWT سمت کلاینت بعد از refresh token استفاده می‌شود.

---

## حالت‌های auth

| mode | رفتار |
|------|--------|
| `jwt` | توکن در header/cookie؛ مناسب API و SPA |
| `session` | نشست PHP سرور |
| `cookie` | credential رمزنگاری‌شده در cookie |

---

## نکات امنیتی

- `PINOOX_JWT_SECRET` را در production حتماً set کنید.
- lifetime کوتاه + remember طولانی برای UX بهتر.
- بعد از `changePassword`، `revokeSessions` فراموش نشود.
- Transport `session_token => platform` یعنی logout در یک اپ، توکن مشترک را هم تحت تأثیر قرار می‌دهد.

---

## مستندات مرتبط

- [مدیریت کاربران](./user-management.md)
- [Transport](../../pinoox%20docs/pinoox-transport.md)
- [API Response](../../pinoox%20docs/pinoox-api-response.md)

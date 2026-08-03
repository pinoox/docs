# مدیریت توکن

[← بازگشت به فهرست](../README.md)

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

`Auth::clientUser()` envelope مشترک کاربر برای SPA است — [مدیریت کاربران](./user-management.md).

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // JWT یا credential string جاری
}
```

اس‌پی‌ای باید **هر دو** را بفرستد:

```http
Authorization: Bearer {token}
Cookie: {auth.key}={jwt}   # HttpOnly؛ مرورگر با credentials: 'include' می‌فرستد
```

| محل ذخیره | خوانا برای JS؟ | نقش |
|-----------|----------------|-----|
| کوکی HttpOnly (`auth.key`) | خیر | نشست سرور برای API هم‌‌مبدأ |
| `localStorage[auth.key]` | بله | هدر `Authorization` + تشخیص آفلاین |

`@pinooxhq/auth` این دو را همگام می‌کند: login توکن برگشتی را در localStorage می‌گذارد؛ `me()` همیشه با `credentials: 'include'` می‌رود تا نشست فقط‌کوکی هم hydrate شود. روی `ALREADY_AUTHENTICATED` یک‌بار logout کوکی و retry لاگین انجام می‌دهد.

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
- برای نشست سرور ترجیحاً کوکی HttpOnly؛ به خواندن `auth.key` از `document.cookie` تکیه نکنید.
- AuthController را نازک نگه دارید (`token` + `Auth::clientUser()`)؛ بازیابی کلاینت با `@pinooxhq/auth`.

---

## CLI (ترمینال)

مدیریت ردیف‌های `TokenModel` برای scope فعال:

| دستور | کاربرد |
|--------|--------|
| `token:list {package}` | لیست توکن‌ها |
| `token:show {token}` | جزئیات با id یا `token_key` |
| `token:create` | ساخت (`--user`, `--lifetime`, `--unit`) |
| `token:update` / `token:delete` | ویرایش یا حذف |
| `token:revoke-user {user}` | معادل `Auth::revokeSessions` |
| `token:purge` | حذف توکن‌های منقضی |

Alias: `tokens` → `token:list`.

```bash
php pinoox token:list platform
php pinoox token:create com_my_shop --user=1 --lifetime=7 --unit=day
php pinoox token:revoke-user 1 --force
```

ساخت از CLI با `ip=127.0.0.1` و `user_agent=pinoox-cli` انجام می‌شود تا خارج از HTTP هم کار کند.

مرجع: [CLI](../start/cli-reference.md).

---

## مستندات مرتبط

- [مدیریت کاربران](./user-management.md) (`Auth::clientUser`, hydrate اس‌پی‌ای)
- [ترنسپورت — Transport](./transport.md)
- [پاسخ — Responses](../basic/responses.md)
- پکیج: [`@pinooxhq/auth`](https://github.com/pinoox/auth)

---

[← بازگشت به فهرست](../README.md)

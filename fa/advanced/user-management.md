# مدیریت کاربران

[← بازگشت به فهرست](../README.md)

احراز هویت در پینوکس 3.x در **pincore** متمرکز است. اپ‌ها فقط `auth` و `transport.user` را در `app.php` تنظیم می‌کنند و از Portalهای `Auth` و `User` استفاده می‌کنند — منطق لاگین را در اپ تکرار نکنید.

---

## Portalها و Helperها

```php
use Pinoox\Portal\Auth;
use Pinoox\Portal\User;

auth();           // Auth::user()
user('email');    // User::get('email')
isLogin();        // Auth::check()
```

---

## تنظیم app.php

```php
return [
    'package' => 'com_acme_portal',
    'transport' => [
        'user' => 'platform',   // اشتراک UserModel با پلتفرم
    ],
    'auth' => [
        'mode' => 'jwt',              // cookie | session | jwt
        'key' => 'manager_pinoox',    // نام cookie / کلید SPA
        'lifetime' => 30,
        'lifetime_unit' => 'day',
        'remember_lifetime' => 365,
        'remember_unit' => 'day',
        'jwt_secret' => null,         // یا PINOOX_JWT_SECRET در .env
    ],
    'flow' => [
        BootFlow::class,
    ],
    'alias' => [
        'auth' => AuthFlow::class,
    ],
];
```

| مقدار transport | معنی |
|-----------------|------|
| `platform` | کاربران مشترک (`pinx_user` / `pincore_user`) |
| `local` | جدول کاربر فقط برای این اپ |
| `{package}` | استفاده از UserModel اپ دیگر |

---

## Auth::boot() در BootFlow

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

`Auth::boot()` تنظیمات `auth` اپ فعال را روی Guard اعمال می‌کند. بدون آن، `attempt()` و `check()` ممکن است درست کار نکنند.

---

## ورود (Login)

کنترلر API از `ApiController` (pincore) و متدهای `ok()` / `fail()` استفاده می‌کند:

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
            return $this->fail('ALREADY_AUTHENTICATED', 'Already logged in', status: 401);
        }

        $input = $this->validated($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $result = Auth::attemptResult([
            'username' => $input['username'],
            'password' => $input['password'],
        ], remember: (bool) ($input['remember'] ?? false));

        if (!$result->success) {
            return $this->fail(
                'INVALID_CREDENTIALS',
                $result->message ?? 'Invalid username or password',
                status: 401,
            );
        }

        return $this->ok([
            'token' => $result->token,
            'user' => Auth::clientUser($result->user),
        ], 'Logged in successfully');
    }

    public function get()
    {
        Auth::boot();

        if (!Auth::check()) {
            return $this->fail('UNAUTHORIZED', 'You must login', status: 401);
        }

        return $this->ok(Auth::clientUser());
    }

    public function logout()
    {
        Auth::boot();
        Auth::logout();

        return $this->ok(null, 'Logged out');
    }
}
```

payload کاربر را در هر اپ دستی نسازید — از `Auth::clientUser()` استفاده کنید.

اپ سیستمی `com_pinoox_manager` همان جریان را دارد اما ممکن است از `$this->error()` / `$this->message()` (legacy) استفاده کند. وقتی نشست سرور هنوز فعال است، کد خطا را `ALREADY_AUTHENTICATED` بگذارید تا `@pinooxhq/auth` بتواند بازیابی کند (پاک‌کردن کوکی + یک‌بار retry لاگین).

فیلدهای مجاز credentials: `username`, `email`, یا `login`.

---

## `Auth::clientUser()` — envelope کاربر برای SPA

شکل ثابت برای پاسخ login / `me` که `@pinooxhq/auth` و تم‌ها مصرف می‌کنند:

```php
Auth::clientUser(?UserModel $user = null): ?array
```

| فیلد | توضیح |
|------|--------|
| `id` / `user_id` | همان شناسه عددی |
| `name` | نام کامل؛ در غیر این صورت username |
| `username`, `email`, `fname`, `lname`, `mobile` | فیلدهای پروفایل |
| `group_key`, `status` | دسترسی / وضعیت حساب |
| `abilities` | از `Access::abilitiesFor($user)` |
| `avatar` / `avatar_url` | بندانگشتی + URL کامل |

```php
// بعد از login
'user' => Auth::clientUser($result->user)

// نشست جاری (me)
Auth::clientUser()          // از Auth::user()
Auth::clientUser($model)    // UserModel مشخص
```

برای پنل / صفحه قفل از **`Auth::profile()`** استفاده کنید. برای قرارداد auth اس‌پی‌ای (`token` + `user` / `me`) از **`Auth::clientUser()`**.

---

## کلاینت SPA — `@pinooxhq/auth`

اپ‌های مستقل `auth` (و در صورت نیاز `auth.client`) را در `app.php` تنظیم می‌کنند و در تم یک‌بار auth می‌سازند:

```js
import { createAuth, createHttp } from '@pinooxhq/auth'

export const auth = createAuth({
  strategy: 'local',
  mode: 'jwt',
  key: 'acme_pinoox',           // باید با auth.key در app.php یکی باشد
  loginUrl: '/acme/login',
  endpoints: {
    login: 'auth/login',
    logout: 'auth/logout',
    me: 'auth/get',
  },
})
```

### کوکی JWT با HttpOnly در برابر localStorage

در حالت `jwt` سرور JWT را در کوکی **HttpOnly** با نام `auth.key` می‌گذارد؛ جاوااسکریپت آن را نمی‌خواند. `@pinooxhq/auth` این‌طور رفتار می‌کند:

1. وقتی login مقدار `token` برگرداند، کپی را در `localStorage` نگه می‌دارد (برای `Authorization: Bearer …`).
2. همیشه `me()` را با `credentials: 'include'` صدا می‌زند — حتی اگر storage خالی باشد — تا کوکی HttpOnly معتبر نشست را hydrate کند.
3. اگر `login` با `ALREADY_AUTHENTICATED` برگردد (کوکی زنده، storage خالی)، با `logout` نشست سرور را پاک می‌کند و **یک‌بار** با همان credentials دوباره login می‌زند.

کلاینت HTTP باید کوکی را روی درخواست‌های same-origin بفرستد:

```js
axios.create({ withCredentials: true })
```

هک «resume session» را در کنترلر یا `main.js` اپ ننویسید — AuthController را نازک نگه دارید و همگام‌سازی کوکی ↔ storage را به `@pinooxhq/auth` بسپارید.

---

## محافظت route با Flow

### API — manifest در `routes/api/*.php`

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

برای alias گروهی (مثل manager):

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

### Web — `group` با flows

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

`Pinoox\Flow\AuthFlow` وقتی `Auth::guest()` باشد، اجرای اکشن را متوقف می‌کند. برای API می‌توانید `exit()` را override کنید:

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

## APIهای پرکاربرد

| متد | کاربرد |
|-----|--------|
| `Auth::check()` / `guest()` | وضعیت ورود |
| `Auth::user()` / `id()` | مدل / شناسه کاربر جاری |
| `Auth::clientUser($user = null)` | envelope اس‌پی‌ای (`id`, `name`, `abilities`, avatar…) |
| `Auth::profile()` | DTO پروفایل برای پنل / قفل صفحه |
| `Auth::token()` | JWT جاری (بعد از login / setToken) |
| `Auth::updateProfile($id, $data)` | ویرایش نام/ایمیل |
| `Auth::changePassword($id, $old, $new)` | تغییر رمز |
| `Auth::create($data)` / `remove($id)` | CRUD کاربر |
| `Auth::revokeSessions($userId)` | ابطال همه توکن‌ها |

---

## رویدادها

| رویداد | نام |
|--------|-----|
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

## معماری

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

## نکات HMVC

1. منطق Auth در pincore است — payload کاربر و هک resume را در اپ duplicate نکنید.
2. `Auth::boot()` را در BootFlow سراسری صدا بزنید.
3. محافظت route با Flow alias (`->flows()` یا `'flow'` در manifest API)، نه `if (!isLogin())` در هر کنترلر.
4. برای اپ‌های مصرف‌کننده فقط `'transport' => ['user' => 'platform']` کافی است.
5. برای SPA: `token` + `Auth::clientUser()` برگردانید؛ همگام‌سازی storage/کوکی با `@pinooxhq/auth`.

---

## CLI (ترمینال)

مدیریت کاربر از ریشه پروژه. دستورات scope مربوط به `transport.user` را رعایت می‌کنند.

| دستور | کاربرد |
|--------|--------|
| `user:list {package}` | لیست کاربران؛ `--status=active`, `--json` |
| `user:show {user}` | نمایش یک کاربر (id، username، email) |
| `user:create` | ساخت کاربر |
| `user:update {user}` | ویرایش (`--set fname=Ali`, `--meta theme=dark`) |
| `user:delete {user}` | حذف کاربر |
| `user:password {user}` | تنظیم رمز |
| `user:status {user}` | تغییر وضعیت |
| `user:role {user}` | اتصال/جداسازی نقش |
| `user:login` | صدور توکن session/JWT (پایین را ببینید) |
| `user:logout` | پایان session فعلی |

Alias: `users` → `user:list`.

```bash
php pinoox user:list com_my_shop --json
php pinoox user:create com_my_shop --username=demo --email=demo@example.test
php pinoox user:password 1 --password=secret
php pinoox user:role 1 --attach=editor
php pinoox user:login com_my_shop --id=1
php pinoox user:login --id=1 --force
php pinoox user:logout --force
```

فهرست کامل: [مرجع CLI](../start/cli-reference.md).

---

## Auto-login محلی / توسعه (`.env`)

دو کلید اختیاری برای توسعهٔ محلی. از auth معمولی cookie / JWT / session **جدا** هستند.

| کلید | چه کسی می‌نویسد | کاربرد |
|------|------------------|--------|
| `PINOOX_LOGIN` | **خودت** (دستی) | لاگین اعلانی: کاربر را با فیلد مشخص برای همان اپ force می‌کند |
| `PINOOX_LOGIN_TOKEN` | CLI با `--force` | ذخیرهٔ توکنِ `user:login` برای auth سمت سرور |

### `PINOOX_LOGIN` (دستی)

فرمت: `package:field:value`

فیلدهای مجاز: `id`، `user_id`، `personal_id`، `username`، `email`، `login`، `mobile`.

```env
PINOOX_LOGIN=com_pinoox_manager:id:1
PINOOX_LOGIN=com_pinoox_account:username:yoosef
PINOOX_LOGIN=com_pinoox_shop:mobile:09122220000
```

قواعد:

- چند خط مجاز است (هر اپ یک خط). اپ فعال خط هم‌نام خودش را می‌گیرد.
- مقدار خالی / `null` رد می‌شود (مثلاً `…:mobile:` هیچ‌کس را لاگین نمی‌کند).
- اگر چند ردیف یکسان باشند، اولین ردیف بر اساس `user_id` انتخاب می‌شود.
- CLI این کلید را **نمی‌نویسد**؛ فقط دستی در `.env`.

### `PINOOX_LOGIN_TOKEN` (توکن CLI)

`user:login` همیشه توکن را چاپ می‌کند (برای Apply در مرورگر / Inspector). با `--force` همان توکن را در `.env` می‌نویسد:

```bash
php pinoox user:login com_pinoox_manager --id=1 --force
# → PINOOX_LOGIN_TOKEN=<token>

php pinoox user:logout --force
# → PINOOX_LOGIN_TOKEN حذف می‌شود
```

بدون `--force` فایل `.env` عوض نمی‌شود. دستورات بالا هرگز `PINOOX_LOGIN` را تغییر نمی‌دهند.

در Pinx / Inspector، «Login as user» معمولاً با `--force` همان توکن را برای auto-login محلی هم ذخیره می‌کند.

نمونهٔ کامنت‌ها در `.env.example` ریشهٔ پروژه.

---

## مستندات مرتبط

- [مدیریت توکن](./token-management.md)
- [دسترسی و مجوزها](./access-permissions.md)
- [فلو — Flow](../basic/flows.md)
- [ترنسپورت — Transport](./transport.md)
- پکیج: [`@pinooxhq/auth`](https://github.com/pinoox/auth)

---

[← بازگشت به فهرست](../README.md)

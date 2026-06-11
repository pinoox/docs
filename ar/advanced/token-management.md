# إدارة الرموز (Token Management)

[← العودة إلى الفهرس](../README.md)

في Pinoox 3.x، تُدار الجلسات (Sessions) ورموز JWT بواسطة **`TokenModel`** (الجدول `pincore_token`) وحارس pincore الداخلي (Guard). يحدّد التطبيق النمط عبر كتلة `auth` في `app.php`؛ وعادةً ما يُوصى بـ **`jwt`** لواجهات API وتطبيقات SPA.

---

## إعدادات JWT في app.php

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

المفتاح السري في ملف `.env` الخاص بالمشروع:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

يحدّد `transport.session_token` نطاق صفوف `TokenModel` (مثلاً `platform` للمشاركة بين التطبيقات).

---

## الرمز (Token) بعد تسجيل الدخول

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // أو Auth::token() بعد تسجيل الدخول
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // رمز JWT الحالي أو سلسلة بيانات الاعتماد
}
```

يرسل العميل (Vue/React) الرمز في ترويسة (Header):

```http
Authorization: Bearer {token}
```

أو عبر المفتاح المعرَّف في `auth.key` داخل الكوكيز/localStorage (حسب تطبيق SPA).

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

يُرشَّح العمود `app` حسب نطاق النقل (Transport scope) — يمكن مشاركة صفوف الرموز أو عزلها لكل تطبيق.

---

## revokeSessions — إلغاء جميع الجلسات

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// تُحذَف جميع صفوف TokenModel الخاصة بـ user_id
```

حالات الاستخدام:

- تسجيل الخروج من جميع الأجهزة
- بعد تغيير كلمة المرور القسري
- حظر مستخدم (`Auth::setStatus($id, UserModel::SUSPEND)` + الإلغاء)

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

تُستخدم لحفظ رمز JWT على جهة العميل بعد تجديد الرمز (Token refresh).

---

## أنماط المصادقة (auth modes)

| النمط | السلوك |
|------|----------|
| `jwt` | الرمز في الترويسة/الكوكيز؛ مناسب لـ API و SPA |
| `session` | جلسة PHP على الخادم |
| `cookie` | بيانات اعتماد مشفّرة في الكوكيز |

---

## نصائح أمنية

- عيّن دائماً `PINOOX_JWT_SECRET` في بيئة الإنتاج.
- مدة صلاحية قصيرة (lifetime) + تذكّر طويل (remember) يحسّنان تجربة المستخدم.
- استدعِ `revokeSessions` بعد `changePassword`.
- مع النقل `session_token => platform`، فإن تسجيل الخروج من تطبيق واحد يؤثر أيضاً على الرموز المشتركة.

---

## وثائق ذات صلة

- [إدارة المستخدمين](./user-management.md)
- [النقل (Transport)](./transport.md)
- [الاستجابات (Responses)](../basic/responses.md)

---

[← العودة إلى الفهرس](../README.md)

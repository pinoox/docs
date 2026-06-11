# टोकन प्रबंधन (Token Management)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x में sessions और JWTs को **`TokenModel`** (`pincore_token`) और internal pincore guard द्वारा प्रबंधित किया जाता है। ऐप `app.php` के `auth` ब्लॉक के माध्यम से mode चुनता है; APIs और SPAs के लिए आमतौर पर **`jwt`** की सिफारिश की जाती है।

---

## app.php में JWT कॉन्फ़िगरेशन

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

प्रोजेक्ट के `.env` में secret:

```env
PINOOX_JWT_SECRET=your-long-random-secret
```

`transport.session_token` `TokenModel` पंक्तियों के लिए scope सेट करता है (जैसे ऐप्स के बीच साझा करने के लिए `platform`)।

---

## लॉगिन के बाद Token

```php
use Pinoox\Portal\Auth;

Auth::boot();

$result = Auth::attemptResult([
    'username' => $input['username'],
    'password' => $input['password'],
], remember: true);

if ($result->success) {
    $jwt = $result->token;   // या लॉगिन के बाद Auth::token()
    return $this->ok(['token' => $jwt], 'user.logged_in_successfully');
}
```

---

## Auth::token()

```php
Auth::boot();

if (Auth::check()) {
    $token = Auth::token();   // वर्तमान JWT या credential string
}
```

Client (Vue/React) टोकन को header में भेजता है:

```http
Authorization: Bearer {token}
```

या `auth.key` में परिभाषित key के माध्यम से cookie/localStorage में (SPA पर निर्भर)।

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

`app` कॉलम transport scope द्वारा फ़िल्टर होता है — token पंक्तियाँ साझा या प्रत्येक ऐप के लिए अलग हो सकती हैं।

---

## revokeSessions — सभी sessions रद्द करना

```php
use Pinoox\Portal\Auth;

$count = Auth::revokeSessions($userId);
// user_id की सभी TokenModel पंक्तियाँ हटा दी जाती हैं
```

उपयोग के मामले:

- सभी डिवाइसों से साइन आउट
- ज़बरदस्ती पासवर्ड बदलने के बाद
- किसी उपयोगकर्ता को ब्लॉक करना (`Auth::setStatus($id, UserModel::SUSPEND)` + revoke)

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

Token refresh के बाद client पर JWT को बनाए रखने के लिए उपयोग होता है।

---

## auth modes

| mode | व्यवहार |
|------|----------|
| `jwt` | Header/cookie में टोकन; API और SPA के लिए उपयुक्त |
| `session` | PHP सर्वर session |
| `cookie` | Cookie में एन्क्रिप्टेड credential |

---

## सुरक्षा सुझाव

- Production में हमेशा `PINOOX_JWT_SECRET` सेट करें।
- छोटा lifetime + लंबा remember UX को बेहतर बनाता है।
- `changePassword` के बाद `revokeSessions` को कॉल करें।
- Transport `session_token => platform` का मतलब है कि एक ऐप में logout साझा tokens को भी प्रभावित करता है।

---

## संबंधित दस्तावेज़

- [User management](./user-management.md)
- [Transport](./transport.md)
- [Responses](../basic/responses.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

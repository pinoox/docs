# عميل HTTP (HTTP Client)

[← العودة إلى الفهرس](../README.md)

يرسل Pinoox 3.x طلبات HTTP الصادرة عبر **`Pinoox\Component\Http\Http`** (مبني داخلياً على Symfony HttpClient). هذه مكتبة مساعدة فقط — فـ Pinoox نفسه منصة HMVC قائمة على التطبيقات ولها نظام توجيه (Routing) وواجهات API خاصة بها. تستخدم تطبيقات النظام مثل `com_pinoox_manager` هذا النمط عند الاتصال بـ pinoox.com.

---

## الاستيراد (Import)

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## الدوال المختصرة

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

الطرق المدعومة: `GET`، `POST`، `PUT`، `PATCH`، `DELETE`، `HEAD`، `OPTIONS`.

---

## خيارات الطلب (Request options)

يقبل Symfony HttpClient الخيارات القياسية:

```php
$response = Http::post('https://api.example.com/oauth/token', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ],
    'json' => [
        'grant_type' => 'client_credentials',
    ],
    'timeout' => 15,
]);
```

| المفتاح | الغرض |
|-----|---------|
| `json` | جسم الطلب بصيغة JSON |
| `body` | جسم الطلب الخام |
| `headers` | ترويسات HTTP |
| `query` | معاملات الاستعلام (Query string) |
| `timeout` | مهلة الانتظار بالثواني |
| `auth_basic` | `[user, pass]` |

---

## قراءة الاستجابة (Response)

```php
private function decodeResponse(?ResponseInterface $response): array
{
    if (!$response) {
        return [];
    }

    return json_decode($response->getContent(), true) ?? [];
}
```

```php
$response = Http::get('https://api.example.com/v1/items');
$data = $this->decodeResponse($response);

if ($response && $response->getStatusCode() === 200) {
    return $this->ok($data);
}

return $this->fail('UPSTREAM_ERROR', 'api.unavailable', status: 502);
```

---

## مثال من الواقع (manager)

```php
use Pinoox\Component\Http\Http;
use Pinoox\Portal\Url;

$response = Http::post('https://www.pinoox.com/api/manager/v1/account/getData', [
    'json' => [
        'remote_url' => Url::origin(),
        'token_key' => config('connect.token_key'),
    ],
]);
```

---

## معالجة الأخطاء

تُرجع `Http::request()` القيمة `null` عند حدوث `TransportExceptionInterface`. تحقّق دائماً من الاستجابة:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## نصائح

- لواجهات API الداخلية ضمن التطبيق نفسه، استخدم الموجّه (Router) والمتحكمات (Controllers)؛ عميل HTTP مخصّص للطلبات **الخارجية** فقط.
- احفظ الروابط ومفاتيح API في `config/` أو `.env` الخاص بالتطبيق، ولا تكتبها بشكل ثابت داخل المتحكمات.
- لا تُستخدم Guzzle مباشرةً في pincore؛ فـ Symfony HttpClient هو البديل الموصى به.

---

## وثائق ذات صلة

- [المتحكمات (Controllers)](../basic/controllers.md)
- [الاستجابات (Responses)](../basic/responses.md)
- [الإعدادات (Config)](../basic/config.md)

---

[← العودة إلى الفهرس](../README.md)

# HTTP Client

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x बाहरी (outbound) HTTP requests को **`Pinoox\Component\Http\Http`** (अंदर Symfony HttpClient) के माध्यम से भेजता है। यह एक helper लाइब्रेरी है — Pinoox स्वयं अपनी routing और APIs के साथ एक app-केंद्रित HMVC प्लेटफ़ॉर्म है। `com_pinoox_manager` जैसे system ऐप्स pinoox.com से संवाद करते समय इसी पैटर्न का उपयोग करते हैं।

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## संक्षिप्त मेथड्स

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

समर्थित मेथड्स: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`।

---

## Request विकल्प

Symfony HttpClient मानक विकल्प स्वीकार करता है:

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

| Key | उद्देश्य |
|-----|---------|
| `json` | JSON body |
| `body` | Raw body |
| `headers` | HTTP headers |
| `query` | Query string |
| `timeout` | सेकंड में timeout |
| `auth_basic` | `[user, pass]` |

---

## Response पढ़ना

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

## वास्तविक उदाहरण (manager)

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

## त्रुटि प्रबंधन (Error handling)

`TransportExceptionInterface` पर `Http::request()` `null` लौटाता है। हमेशा response की जाँच करें:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## सुझाव

- एक ही ऐप में internal APIs के लिए router और controllers का उपयोग करें; Http Client केवल **बाहरी (external)** requests के लिए है।
- URLs और API keys को ऐप के `config/` या `.env` में रखें, controllers में hard-code न करें।
- Pincore में Guzzle का सीधे उपयोग नहीं होता; Symfony HttpClient अनुशंसित विकल्प है।

---

## संबंधित दस्तावेज़

- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Config](../basic/config.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

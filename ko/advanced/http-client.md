# HTTP Client

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 outbound HTTP request를 **`Pinoox\Component\Http\Http`**(내부 Symfony HttpClient)로 전송합니다. helper library이며 Pinoox 자체는 자체 routing과 API를 가진 app-centric HMVC platform입니다. `com_pinoox_manager` 같은 system app이 pinoox.com과 통신할 때 이 패턴을 사용합니다.

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Short method

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

지원 method: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Request option

Symfony HttpClient 표준 option:

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

| Key | Purpose |
|-----|---------|
| `json` | JSON body |
| `body` | Raw body |
| `headers` | HTTP headers |
| `query` | Query string |
| `timeout` | Timeout in seconds |
| `auth_basic` | `[user, pass]` |

---

## Response 읽기

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

## 실제 예제 (manager)

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

## Error handling

`Http::request()`는 `TransportExceptionInterface`에서 `null` 반환. 항상 response 확인:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Tips

- 같은 앱 내부 API는 router와 Controller 사용; Http Client는 **외부** request 전용.
- URL과 API key는 Controller 하드코딩이 아니라 앱 `config/` 또는 `.env`에.
- pincore에서 Guzzle 직접 사용 안 함; Symfony HttpClient 권장.

---

## 관련 문서

- [Controller](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Config](../basic/config.md)

---

[← 색인으로 돌아가기](../README.md)

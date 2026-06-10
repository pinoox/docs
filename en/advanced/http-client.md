# HTTP Client

[← Back to index](../../readme.md)

Pinoox 3.x sends outbound HTTP requests through **`Pinoox\Component\Http\Http`** (Symfony HttpClient inside). That is a helper library — Pinoox itself is an app-centric HMVC platform with its own routing and APIs. System apps such as `com_pinoox_manager` use this pattern when talking to pinoox.com.

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Short methods

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Supported methods: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Request options

Symfony HttpClient accepts standard options:

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

## Reading the response

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

## Real-world example (manager)

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

`Http::request()` returns `null` on `TransportExceptionInterface`. Always check the response:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Tips

- For internal APIs in the same app, use the router and controllers; Http Client is for **external** requests only.
- Keep URLs and API keys in app `config/` or `.env`, not hard-coded in controllers.
- Guzzle is not used directly in pincore; Symfony HttpClient is the recommended replacement.

---

## Related docs

- [Controllers](../basic/controllers.md)
- [API response](../../pinoox%20docs/pinoox-api-response.md)
- [Config](../basic/config.md)

---

[← Back to index](../../readme.md)

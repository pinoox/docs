# HTTP-Client

[← Zurück zur Übersicht](../README.md)

Pinoox 3.x sendet ausgehende HTTP-Requests über **`Pinoox\Component\Http\Http`** (intern Symfony HttpClient). Das ist eine Hilfsbibliothek — Pinoox selbst ist eine App-zentrierte HMVC-Plattform mit eigenem Routing und eigenen APIs. System-Apps wie `com_pinoox_manager` nutzen dieses Muster bei der Kommunikation mit pinoox.com.

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Kurzmethoden

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Unterstützte Methoden: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Request-Optionen

Symfony HttpClient akzeptiert die Standardoptionen:

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

| Schlüssel | Zweck |
|-----|---------|
| `json` | JSON-Body |
| `body` | Roher Body |
| `headers` | HTTP-Header |
| `query` | Query-String |
| `timeout` | Timeout in Sekunden |
| `auth_basic` | `[user, pass]` |

---

## Response auslesen

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

## Praxisbeispiel (Manager)

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

## Fehlerbehandlung

`Http::request()` gibt bei einer `TransportExceptionInterface` `null` zurück. Prüfen Sie die Response immer:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Tipps

- Für interne APIs derselben App verwenden Sie Router und Controller; der HTTP-Client ist nur für **externe** Requests gedacht.
- Halten Sie URLs und API-Schlüssel in der App-`config/` oder `.env`, nicht hartkodiert in Controllern.
- Guzzle wird in pincore nicht direkt verwendet; Symfony HttpClient ist der empfohlene Ersatz.

---

## Verwandte Dokumente

- [Controller](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Config](../basic/config.md)

---

[← Zurück zur Übersicht](../README.md)

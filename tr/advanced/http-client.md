# HTTP Client

[← Dizine dön](../README.md)

Pinoox 3.x giden HTTP isteklerini **`Pinoox\Component\Http\Http`** (içinde Symfony HttpClient) üzerinden gönderir. Bu bir helper kütüphanesidir — Pinoox'un kendisi kendi routing ve API'leriyle uygulama merkezli bir HMVC platformudur. `com_pinoox_manager` gibi sistem uygulamaları pinoox.com ile konuşurken bu deseni kullanır.

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Kısa metotlar

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Desteklenen metotlar: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## İstek seçenekleri

Symfony HttpClient standart seçenekleri kabul eder:

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

| Anahtar | Amaç |
|-----|---------|
| `json` | JSON gövdesi |
| `body` | Ham gövde |
| `headers` | HTTP başlıkları |
| `query` | Sorgu dizesi |
| `timeout` | Saniye cinsinden zaman aşımı |
| `auth_basic` | `[user, pass]` |

---

## Yanıtı okuma

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

## Gerçek dünya örneği (manager)

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

## Hata işleme

`Http::request()`, `TransportExceptionInterface` durumunda `null` döndürür. Yanıtı her zaman kontrol edin:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## İpuçları

- Aynı uygulamadaki dahili API'ler için router ve controller'ları kullanın; Http Client yalnızca **harici** istekler içindir.
- URL'leri ve API anahtarlarını controller'larda sabit kodlamayın; uygulama `config/` veya `.env` içinde tutun.
- pincore'da Guzzle doğrudan kullanılmaz; Symfony HttpClient önerilen alternatiftir.

---

## İlgili dokümantasyon

- [Controller](../basic/controllers.md)
- [Response](../basic/responses.md)
- [Config](../basic/config.md)

---

[← Dizine dön](../README.md)

# HTTP-клиент (HTTP Client)

[← Назад к оглавлению](../README.md)

Pinoox 3.x отправляет исходящие HTTP-запросы через **`Pinoox\Component\Http\Http`** (внутри — Symfony HttpClient). Это вспомогательная библиотека — сам Pinoox является приложение-ориентированной HMVC-платформой со своей маршрутизацией и API. Системные приложения, такие как `com_pinoox_manager`, используют этот паттерн при обращении к pinoox.com.

---

## Импорт

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Короткие методы

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Поддерживаемые методы: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Опции запроса (Request)

Symfony HttpClient принимает стандартные опции:

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

| Ключ | Назначение |
|-----|---------|
| `json` | JSON-тело запроса |
| `body` | «Сырое» тело запроса |
| `headers` | HTTP-заголовки |
| `query` | Строка запроса (query string) |
| `timeout` | Тайм-аут в секундах |
| `auth_basic` | `[user, pass]` |

---

## Чтение ответа

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

## Пример из реальной жизни (manager)

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

## Обработка ошибок

`Http::request()` возвращает `null` при `TransportExceptionInterface`. Всегда проверяйте ответ:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Советы

- Для внутренних API в том же приложении используйте роутер и контроллеры; HTTP-клиент предназначен только для **внешних** запросов.
- Храните URL-адреса и ключи API в `config/` приложения или в `.env`, а не «зашитыми» в контроллерах.
- Guzzle не используется в pincore напрямую; Symfony HttpClient — рекомендуемая замена.

---

## Связанные документы

- [Контроллеры](../basic/controllers.md)
- [Ответы (Responses)](../basic/responses.md)
- [Конфигурация (Config)](../basic/config.md)

---

[← Назад к оглавлению](../README.md)

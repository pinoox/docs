# Cliente HTTP

[← Volver al índice](../README.md)

Pinoox 3.x envía peticiones HTTP salientes mediante **`Pinoox\Component\Http\Http`** (Symfony HttpClient por dentro). Es una biblioteca auxiliar — Pinoox en sí es una plataforma HMVC centrada en apps con su propio enrutamiento y APIs. Apps del sistema como `com_pinoox_manager` usan este patrón al comunicarse con pinoox.com.

---

## Importación

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Métodos cortos

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Métodos soportados: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Opciones de petición

Symfony HttpClient acepta opciones estándar:

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

| Clave | Propósito |
|-----|---------|
| `json` | Cuerpo JSON |
| `body` | Cuerpo en bruto |
| `headers` | Cabeceras HTTP |
| `query` | Cadena de consulta |
| `timeout` | Tiempo de espera en segundos |
| `auth_basic` | `[user, pass]` |

---

## Leer la respuesta

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

## Ejemplo real (manager)

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

## Manejo de errores

`Http::request()` devuelve `null` en `TransportExceptionInterface`. Comprueba siempre la respuesta:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Consejos

- Para APIs internas en la misma app, usa el router y controllers; el Cliente HTTP es solo para peticiones **externas**.
- Mantén URLs y claves API en `config/` de la app o `.env`, no hardcodeadas en controllers.
- Guzzle no se usa directamente en pincore; Symfony HttpClient es el reemplazo recomendado.

---

## Documentación relacionada

- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Config](../basic/config.md)

---

[← Volver al índice](../README.md)

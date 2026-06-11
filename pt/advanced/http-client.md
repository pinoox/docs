# HTTP Client

[← Voltar ao índice](../README.md)

O Pinoox 3.x envia requisições HTTP de saída através de **`Pinoox\Component\Http\Http`** (Symfony HttpClient por dentro). Trata-se de uma biblioteca auxiliar — o próprio Pinoox é uma plataforma HMVC centrada em apps, com roteamento e APIs próprios. Apps do sistema, como o `com_pinoox_manager`, usam esse padrão ao se comunicar com pinoox.com.

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Métodos curtos

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Métodos suportados: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Opções da requisição

O Symfony HttpClient aceita as opções padrão:

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

| Chave | Finalidade |
|-----|---------|
| `json` | Corpo JSON |
| `body` | Corpo bruto (raw) |
| `headers` | Cabeçalhos HTTP |
| `query` | Query string |
| `timeout` | Timeout em segundos |
| `auth_basic` | `[user, pass]` |

---

## Lendo a resposta

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

## Exemplo do mundo real (manager)

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

## Tratamento de erros

`Http::request()` retorna `null` em caso de `TransportExceptionInterface`. Sempre verifique a resposta:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Dicas

- Para APIs internas no mesmo app, use o router e os controllers; o Http Client é apenas para requisições **externas**.
- Mantenha URLs e chaves de API no `config/` do app ou no `.env`, e não fixas (hard-coded) nos controllers.
- O Guzzle não é usado diretamente no pincore; o Symfony HttpClient é o substituto recomendado.

---

## Documentação relacionada

- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Config](../basic/config.md)

---

[← Voltar ao índice](../README.md)

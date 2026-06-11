# Client HTTP

[← Retour à l'index](../README.md)

Pinoox 3.x envoie les requêtes HTTP sortantes via **`Pinoox\Component\Http\Http`** (Symfony HttpClient à l'intérieur). C'est une bibliothèque utilitaire — Pinoox lui-même est une plateforme HMVC centrée sur les applications, avec son propre routage et ses propres API. Les applications système comme `com_pinoox_manager` utilisent ce modèle pour communiquer avec pinoox.com.

---

## Import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## Méthodes courtes

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

Méthodes prises en charge : `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, `OPTIONS`.

---

## Options de requête

Symfony HttpClient accepte les options standard :

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

| Clé | Rôle |
|-----|---------|
| `json` | Corps JSON |
| `body` | Corps brut |
| `headers` | En-têtes HTTP |
| `query` | Chaîne de requête (query string) |
| `timeout` | Délai d'expiration en secondes |
| `auth_basic` | `[user, pass]` |

---

## Lire la réponse

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

## Exemple réel (manager)

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

## Gestion des erreurs

`Http::request()` renvoie `null` en cas de `TransportExceptionInterface`. Vérifiez toujours la réponse :

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## Conseils

- Pour les API internes de la même application, utilisez le routeur et les contrôleurs ; le client HTTP est destiné uniquement aux requêtes **externes**.
- Conservez les URL et les clés d'API dans le `config/` de l'application ou dans `.env`, pas en dur dans les contrôleurs.
- Guzzle n'est pas utilisé directement dans pincore ; Symfony HttpClient est le remplaçant recommandé.

---

## Documentation associée

- [Contrôleurs](../basic/controllers.md)
- [Réponses](../basic/responses.md)
- [Config](../basic/config.md)

---

[← Retour à l'index](../README.md)

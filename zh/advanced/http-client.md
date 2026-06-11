# HTTP 客户端（HTTP Client）

[← 返回索引](../README.md)

Pinoox 3.x 通过 **`Pinoox\Component\Http\Http`**（内部为 Symfony HttpClient）发送对外的 HTTP 请求。它只是一个辅助库 —— Pinoox 本身是一个以应用为中心、拥有自有路由和 API 的 HMVC 平台。像 `com_pinoox_manager` 这样的系统应用在与 pinoox.com 通信时就使用这种模式。

---

## 导入

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## 快捷方法

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

支持的方法：`GET`、`POST`、`PUT`、`PATCH`、`DELETE`、`HEAD`、`OPTIONS`。

---

## 请求选项

Symfony HttpClient 接受标准选项：

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

| 键 | 用途 |
|-----|---------|
| `json` | JSON 请求体 |
| `body` | 原始请求体 |
| `headers` | HTTP 头 |
| `query` | 查询字符串 |
| `timeout` | 超时（秒） |
| `auth_basic` | `[user, pass]` |

---

## 读取响应

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

## 真实示例（manager）

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

## 错误处理

发生 `TransportExceptionInterface` 时，`Http::request()` 返回 `null`。务必检查响应：

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## 提示

- 同一应用内的内部 API 请使用路由和控制器；HTTP Client 只用于 **外部** 请求。
- 将 URL 和 API 密钥放在应用的 `config/` 或 `.env` 中，不要硬编码在控制器里。
- pincore 不直接使用 Guzzle；推荐的替代方案是 Symfony HttpClient。

---

## 相关文档

- [控制器（Controllers）](../basic/controllers.md)
- [响应（Responses）](../basic/responses.md)
- [配置（Config）](../basic/config.md)

---

[← 返回索引](../README.md)

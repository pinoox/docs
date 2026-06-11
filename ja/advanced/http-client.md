# HTTP Client

[← 索引に戻る](../README.md)

Pinoox 3.x の外向き HTTP リクエストは **`Pinoox\Component\Http\Http`**（内部は Symfony HttpClient）経由です。これはヘルパーライブラリです — Pinoox 自体は独自のルーティングと API を持つアプリ中心の HMVC プラットフォームです。`com_pinoox_manager` などのシステムアプリは pinoox.com と通信するときにこのパターンを使用します。

---

## import

```php
use Pinoox\Component\Http\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## 短いメソッド

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
```

サポートメソッド: `GET`、`POST`、`PUT`、`PATCH`、`DELETE`、`HEAD`、`OPTIONS`。

---

## リクエストオプション

Symfony HttpClient は標準オプションを受け付けます。

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

| キー | 目的 |
|-----|---------|
| `json` | JSON ボディ |
| `body` | 生ボディ |
| `headers` | HTTP ヘッダー |
| `query` | クエリ文字列 |
| `timeout` | タイムアウト（秒） |
| `auth_basic` | `[user, pass]` |

---

## レスポンスの読み取り

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

## 実例（manager）

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

## エラーハンドリング

`Http::request()` は `TransportExceptionInterface` 時に `null` を返します。常にレスポンスを確認してください。

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## ヒント

- 同一アプリ内の内部 API には Router と Controller を使用。Http Client は **外部** リクエスト専用です。
- URL と API キーは Controller にハードコードせず、アプリ `config/` または `.env` に置く
- pincore では Guzzle を直接使用しません。Symfony HttpClient が推奨代替です。

---

## 関連ドキュメント

- [Controller](../basic/controllers.md)
- [Response](../basic/responses.md)
- [Config](../basic/config.md)

---

[← 索引に戻る](../README.md)

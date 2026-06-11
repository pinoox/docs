# Config

[← 索引に戻る](../README.md)

Pinoox 3.x の設定は `config/`（コアとアプリ）配下の PHP ファイルに保存されます。標準的な方法: 読み取りは **`config('key')`**、書き込みは **`config('name')->set(...)->save()`**。

---

## 読み取り

```php
// シンプルなキー
$siteName = config('app.name');

// ネストされたキー（ドット記法）
$merchant = config('payment.merchant_id');

// デフォルト値
$timeout = config('api.timeout', 30);

// チェーン用 Config オブジェクト
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## 書き込みと保存

**変更後は必ず `save()` を呼び出す:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## ネストデータ — `setLinear` / `getLinear`

```php
// 読み取り
$themeName = config('theme.panel.name');

// 書き込み
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Config ファイルの場所

| 場所 | 内容 |
|----------|----------|
| `pincore/config/*.config.php` | コア設定（DB、ドメインなど） |
| `apps/{package}/config/*.config.php` | アプリ設定 |
| `pinker/config/` | Bake 版（本番） |
| `pinker/state/config/` | インストール後の上書き（例: DB） |

開発環境では、機密値は `.env` 経由の `env()` / `_env()` から読み取られます。

---

## 例: 決済ゲートウェイ設定

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'stripe',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller または Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## 例: 動的メニュー

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'Reports', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Portal — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

実際には `config()` が同じ Portal をラップします — 1 つのスタイルで十分です。

---

## ヒント

- シークレット（API キー、DB パスワード）を git にコミットしない。`.env` または `pinker/state` を使用
- ファイル名: `{name}.config.php` → `config('{name}.key')`
- 本番デプロイ後、`php pinoox pinker:rebuild` で config を Bake

---

## 関連ドキュメント

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [ファイルパス](./path.md)
- [app.php マニフェスト](../start/app-manifest.md)

---

[← 索引に戻る](../README.md)

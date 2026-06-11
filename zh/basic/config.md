# 配置（Config）

[← 返回索引](../README.md)

Pinoox 3.x 的设置存储在 `config/` 下的 PHP 文件中（核心和应用各有一份）。标准方式：用 **`config('key')`** 读取，用 **`config('name')->set(...)->save()`** 写入。

---

## 读取

```php
// 简单键
$siteName = config('app.name');

// 嵌套键（点号语法）
$merchant = config('payment.merchant_id');

// 默认值
$timeout = config('api.timeout', 30);

// 用于链式调用的 Config 对象
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## 写入与保存

**修改后务必调用 `save()`：**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## 嵌套数据 — `setLinear` / `getLinear`

```php
// 读取
$themeName = config('theme.panel.name');

// 写入
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## 配置文件位置

| 位置 | 内容 |
|----------|----------|
| `pincore/config/*.config.php` | 核心设置（数据库、域名等） |
| `apps/{package}/config/*.config.php` | 应用设置 |
| `pinker/config/` | 烘焙后的版本（生产环境） |
| `pinker/state/config/` | 安装后的覆盖项（例如数据库） |

在开发环境中，敏感值通过 `env()` / `_env()` 从 `.env` 读取。

---

## 示例：支付网关设置

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
// Controller 或 Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## 示例：动态菜单

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

实际上 `config()` 包装的就是同一个 Portal — 使用其中一种风格即可。

---

## 小贴士

- 不要把机密信息（API 密钥、数据库密码）提交到 git；请使用 `.env` 或 `pinker/state`。
- 文件名：`{name}.config.php` → `config('{name}.key')`。
- 生产环境部署后，运行 `php pinoox pinker:rebuild` 烘焙配置。

---

## 相关文档

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [文件路径（Path）](./path.md)
- [app.php 清单](../start/app-manifest.md)

---

[← 返回索引](../README.md)

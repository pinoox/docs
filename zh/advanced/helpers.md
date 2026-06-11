# 全局辅助函数（Global Helpers）

[← 返回索引](../README.md)

Pinoox 3.x 从 `pincore/functions/` 加载全局辅助函数。对于日常应用开发，这些辅助函数（加上 Portal）已经足够 —— 不要直接实例化核心组件（Component）。

---

## 主要辅助函数

| 辅助函数 | 用途 | 示例 |
|--------|---------|---------|
| `render()` | 以字符串形式返回 HTML | `$html = render('email', $data);` |
| `response()` | HTTP 响应 | `return response()->json($data);` |
| `redirect()` | 重定向 | `return redirect(url('login'));` |
| `url()` | 应用/站点 URL | `url('products')` |
| `path()` | 磁盘上的文件路径 | `path('storage/logs/app.log')` |
| `assets()` | 主题文件 URL | `assets('dist/app.css')` |
| `config()` | 读/写配置 | `config('app.name')` |
| `t()` | 翻译（返回值） | `t('welcome.title')` |
| `lang()` | 翻译（直接输出） | `lang('welcome.title')` |
| `app()` | 当前活动应用 | `app()->get('package')` |
| `auth()` | 已登录用户 | `auth()` → `Auth::user()` |
| `user()` | 用户字段 | `user('email')` |
| `isLogin()` | 登录状态 | `if (isLogin()) { … }` |
| `session()` | 会话（Session） | `session('token')` |
| `runtime()` | 当前活动的 HTTP 内核 | `runtime()->getRequest()` |
| `_env()` | 环境变量 | `_env('APP_DEBUG', false)` |
| `alias()` | Flow/类别名 | `alias('auth')` |

在控制器（Controller）中输出 HTML 请使用 **`View::render()`**（与系统应用一致）。虽然存在 `view()` 辅助函数，但在控制器中应优先使用 Portal。

---

## Request —— 依赖注入或 `runtime()`

pincore 中没有全局的 `request()` 辅助函数。在控制器和组件中使用类型提示注入：

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

在 Flow 或其他无法通过方法签名注入的地方：

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth —— `auth()`、`user()`、Flow

```php
// 当前用户（Auth::user()）
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) 与 user($key) 等价
$email = auth('email');

// 使用 Flow 别名保护路由
// app.php → 'alias' => ['auth' => AuthFlow::class]
// 路由 → ->flows(['auth']) 或在分组中使用 flows
```

---

## View 与 Response

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## 配置（Config）

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## 语言（Lang）

```php
$label = t('product.title');
// 在 Twig 中：{{ t('product.title') }}
```

---

## URL 与 Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## 自定义应用辅助函数

在 `app.php` 中：

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return '$' . number_format($amount, 2);
}
```

---

## Twig 辅助函数（模板中）

除了 PHP 辅助函数外，在 Twig 中还可使用以下函数：

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## 提示

- 在控制器中输出 HTML 时使用 `View::render()`；日常任务使用 `url()`、`t()`、`config()` 等辅助函数
- 辅助函数只有在 Pinoox 引导（bootstrap）之后才可用 —— 不要在 `index.php` / `pinoox` 之外的原生 PHP 脚本中加载它们
- 对于复杂逻辑，优先使用 `Component/` + Portal，而不是自定义辅助函数

---

## 相关文档

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [语言（Language）](../basic/language.md)
- [服务（Services）](./services.md)

---

[← 返回索引](../README.md)

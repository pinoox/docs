# URL 与链接构建

[← 返回索引](../README.md)

在 Pinoox 3.x 中，使用 **`url()`** 构建内部 URL。该辅助函数基于 **`Url::link()`**，能感知域名、安装路径（子文件夹）和当前应用的 URL 段。

> 不要使用 **`Url::get()`** 或 **`Url::app()`**。请改用 **`url()`**、**`Url::link()`** 和 **`Url::forApp()`**。

---

## PHP — `url()` 辅助函数

```php
// 当前激活应用内的相对链接
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// 不带参数时返回访问器
$accessor = url();
echo $accessor->app;               // 应用基础 URL
echo $accessor->site;              // 源（origin）+ 项目路径
echo $accessor->api;               // API 前缀

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // 与 url('products') 相同
echo Url::forApp('com_acme_shop'); // 指定应用的基础 URL
echo Url::current();               // 当前页面 URL
echo Url::origin();                // https://example.com/pinoox
```

在应用基础路径之外的链接，使用 `^` 或 `~` 前缀：

```php
echo url('^about');                // 从项目根开始
echo Url::link('^config/app.php');
```

---

## Twig — `url()` 访问器

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| 访问器方法 | 用途 |
|-----------------|---------|
| `url().site` | 源（origin）+ 项目路径 |
| `url().app` | 源（origin）+ 应用 URL 段 |
| `url().api` | API 前缀（默认 `api/v1/`） |
| `url().resource('resources/logo.png')` | `apps/{package}/` 下的静态文件 |
| `url('profile')` | 应用内的路由链接 |

---

## 路由名 — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## 主题资源 — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // 当前激活主题中文件的 URL
```

---

## 控制器中的菜单示例

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## 请求信息

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## 小贴士

- 不要硬编码链接；始终使用 `url()` 或 `Url::link()`
- `apps/{package}/resources/` 中的文件使用 `url().resource()` 或 `asset()`；主题文件使用 **`assets()`**
- 基础 URL 不需要在配置中手动设置；它会从 HTTP 请求中自动检测

---

## 相关文档

- [文件路径（Path）](./path.md)
- [视图（Views）](./views.md)
- [Twig 模板](./templates.md)
- [路由（Router）](./routers.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

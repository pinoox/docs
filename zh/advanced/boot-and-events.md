# boot.php 与事件（Events）

[← 返回索引](../README.md)

除了 `routes/` 之外，你还可以在 **`boot.php`** 中注册路由、API 端点、Flow、计划任务和监听器 —— 这对 **插件**、微模块或挂接到宿主应用（如 manager）的钩子很有用。

每个应用可提供 `apps/{package}/boot.php`。文件返回接收 `AppRegister` 的 closure，在请求处理**之前**执行。

---

## 生命周期

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### 管道阶段

| 阶段 | 作用 |
|------|------|
| `boot.global` | 每个请求 boot 带 `boot-global => true` 的应用 |
| `app.boot` | boot 当前活跃应用（+ `extends` 的 extender） |

### Boot 事件

| 名称 | 时机 |
|------|------|
| `app.booting` / `app.booting.{package}` | commit 之前 |
| `app.booted` / `app.booted.{package}` | integrate 之后 |
| `app.routes` / `app.routes.{package}` | 应用 Web 路由时 |
| `app.api` / `app.api.{package}` | 构建 API registry 时 |

在 `boot.php` 中监听：

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### 核心请求事件

框架在每个 HTTP 请求上自动 dispatch（`AppCoreEventSubscriber`）：

| 名称 | 时机 | package 变体 | 命名通道 |
|------|------|--------------|----------|
| `app.route.matched` | 路由匹配后 | `app.route.matched.{package}` | `app.route.{routeName}` 或 `app.api.{routeName}` |
| `app.controller` | controller 执行前 | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | 发送响应前 | `app.response.{package}` | — |
| `app.exception` | 未捕获异常 | `app.exception.{package}` | — |
| `app.terminate` | 响应发送后 | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

简单 hook 用 **watch**（`onRoute`、`onApi` 等）；完全控制用核心事件的 **listen**。

---

## 三种应用模式

| 模式 | 配置 | 行为 |
|------|--------|----------|
| **仅路由（Route only）** | 仅 `router.routes` | 当应用 URL 激活时运行 |
| **全局启动（Boot global）** | `boot-global => true` | 每个 HTTP 请求都会启动 |
| **Boot + Route** | `boot.php` + 路由 | 默认脚手架 |

挂接到宿主应用的插件：

```php
'extends' => ['com_host_app'],
```

你的插件只在宿主启动时才会启动（比全局模式更轻量）。

---

## boot 用的 `app.php` 键

`apps/{package}/app.php` 中的这些键控制 **是否** 运行 `boot.php`、**何时** 运行，以及是否缓存 boot 结果。它们配置 boot 管道，**不能** 替代 `boot.php` 本身。

### boot 文件（`boot`）

| 值 | 默认 | 效果 |
|----|------|------|
| `true` | 是 | 在该 app boot 时执行 `boot.php` |
| `false` | | 不执行 boot，仅路由 |
| `'path/custom.php'` | | 使用 app 根目录下的其他文件 |

文件必须 **返回 callable**：`fn (AppRegister $register) => …`。若 `true` 但文件不存在，boot 会静默跳过。

### 全局插件（`boot-global`）

| 值 | 默认 | 效果 |
|----|------|------|
| `false` | 是 | 仅在该 app 激活时 boot |
| `true` | | **每个 HTTP 请求** 都 boot |

### 宿主插件（`extends`）

| 值 | 默认 | 效果 |
|----|------|------|
| `[]` | 是 | 普通 app |
| `['com_host_app']` | | 宿主激活时 **先于** 宿主 boot |

### 额外注册（`startup`）

`app.php` 中的可选 callable，在 `boot.php` **之后** 执行，API 相同。

### boot 缓存（`cache`）

需主动开启：`cache.enabled` 为 `true`。部署后：`php pinoox cache:build {package}`。

### 快速选择

| 需求 | 设置 |
|------|------|
| 普通 app | `'boot' => true` |
| 仅路由 | `'boot' => false` |
| 全站插件 | `'boot-global' => true` |
| 宿主插件 | `'extends' => ['com_host_app']` |

---

## 基本的 boot.php

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## AppRegister —— 常用方法

| 方法 | 用途 |
|--------|---------|
| `web(callable)` | 通过构建器注册路由 |
| `route([...])` | 单条 Web 路由 |
| `api([manifest])` | 完整的 API 清单 |
| `apiRoute([...])` | 单个 API 端点 |
| `action('name', handler)` | 命名 Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow 别名 |
| `schedule(callable)` | 计划任务 |
| `listen('event', listener)` | 事件监听器 |
| `subscribe(SubscriberClass::class)` | Symfony 订阅者（subscriber） |
| `when('com_host', fn)` | 在另一个应用启动时挂接 |

---

## Theme — 上下文、继承与 boot 钩子

目录位于 `apps/{package}/theme/{name}/`。在 **`app.php`** 配置 active theme；在 **`boot.php`** 做 runtime 钩子。

### `app.php` 键

| 键 | 用途 |
|----|------|
| `theme` | 当前 theme 文件夹 |
| `theme-context` / `theme-contexts` | 多 theme |
| `theme-extends` | 继承 |
| `path-theme` | 自定义路径 |
| `frontend` | Vite profile、entry、manifest |

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Routes：`flows: ['auth', 'theme.panel']`。`theme/{name}/`：`theme.php`、Twig、`functions.php`、`frontend.config.php`、`src/` / `dist/`。

见 [Views](../basic/views.md)、[Twig](../basic/templates.md)、[app.php](../start/app-manifest.md)。

### 在 `boot.php` 中

**`onTheme`** 或 **listen** / **watch**：

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

Controller：`View::changeTheme('panel')`、`ThemeContext::activate('panel')`、`within_theme(...)`。

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

---

## Event Portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

关于如何让邮件逻辑与控制器解耦，参见 [邮件](./mail.md)。

**Flow** = 控制器之前（中间件）。**Event** = 动作之后（副作用）。

---

## 辅助函数

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot 缓存

在 `app.php` 的 `cache.stores` 下设置 `'boot' => true`，可通过 Pinker 烘焙 boot —— 参见 [Pinker](./pinker.md)。

---

## 相关文档

- [计划任务（Schedule）](./schedule.md)
- [Flow](../basic/flows.md)
- [路由（Routers）](../basic/routers.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

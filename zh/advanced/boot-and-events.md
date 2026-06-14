# boot.php 与事件（Events）

[← 返回索引](../README.md)

除了 `routes/` 之外，你还可以在 **`boot.php`** 中注册路由、API 端点、Flow、计划任务和监听器 —— 这对 **插件**、微模块或挂接到宿主应用（如 manager）的钩子很有用。

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

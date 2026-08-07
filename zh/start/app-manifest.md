# app.php 清单参考

[← 返回索引](../README.md)

`app.php` 是你的应用清单。默认值位于 `vendor/pinoox/pincore/Component/Package/data/source.php` — 只需覆盖你需要修改的部分。

---

## 标识与激活

| 键 | 用途 |
|-----|---------|
| `package` | 文件夹名称 = 命名空间（`com_acme_shop`） |
| `name` | 显示名称 |
| `enable` | 启用 / 禁用应用 |
| `description`, `developer`, `icon` | 元数据 |
| `version-name`, `version-code` | 应用版本 |
| `sys-app`, `hidden`, `dock` | 系统应用 / 隐藏 / 管理器 Dock |
| `minpin` | 最低平台版本 |

---

## 路由与启动

| 键 | 用途 |
|-----|---------|
| `router.routes` | `routes/*.php` 文件 |
| `boot` | 运行 `boot.php`（默认为 true） |
| `boot-global` | 在每个 HTTP 请求时启动 |
| `extends` | 在宿主应用启动时一并启动 |
| `loader` | 额外文件（`func.php`） |
| `depends` | 依赖的应用 |

参见 [boot.php 与事件](../advanced/boot-and-events.md)。

---

## Flow 与安全

| 键 | 用途 |
|-----|---------|
| `flow` | 全局 Flow（BootFlow） |
| `alias` | 名称 → Flow 类 |
| `auth` | mode、lifetime、JWT/cookie |
| `access` | RBAC：`groups`、`super_roles` |
| `transport` | 与平台共享用户/文件/权限 |

参见 [Flow](../basic/flows.md)、[用户管理](../advanced/user-management.md)、[访问权限](../advanced/access-permissions.md)。

---

## UI 与主题

| 键 | 用途 |
|-----|---------|
| `theme` | 当前激活的主题文件夹 |
| `theme-context`, `theme-contexts`, `theme-extends` | 多上下文 / 继承 |
| `frontend` | `stack`、`profile`、`entry`、`manifest` |
| `lang` | 默认语言环境 |
| `open` | 管理器中的打开行为 |

---

## 数据库与存储

| 键 | 用途 |
|-----|---------|
| `database` | 覆盖数据库连接 |
| `table.prefix` | 表前缀 |
| `transport.user` / `file_storage` / `access` | 预设值或细粒度键 |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## 运行时

| 键 | 用途 |
|-----|---------|
| `runtime.mode`, `runtime.debug` | 覆盖运行模式 |
| `cache` | 烘焙 routes/api/boot/twig |
| `log`, `redis`, `date` | 按应用覆盖 |
| `container` | DI 绑定 |

---

## Pinker / Pinx

| 键 | 用途 |
|-----|---------|
| `pinx` | type、minpin、sign |
| `build` | 打包时的排除/包含规则 |

---

## 综合示例

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## 相关文档

- [项目结构](./structure.md)
- [配置（Config）](../basic/config.md)

---

[← 返回索引](../README.md)

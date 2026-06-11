# Transport（共享资源）

[← 返回索引](../README.md)

在 HMVC 架构中，应用可以通过 `app.php` 中的 **`transport`** 配置块与其他应用共享用户、认证、文件和权限。如果不配置 transport，每个应用的所有资源都保持在自身包内（**local**）。

| 术语 | 含义 |
|------|---------|
| **`platform`** | 逻辑共享作用域 —— 共享的数据库行使用 `app = platform` |
| **`pincore/`** | 仅指框架的物理文件夹 —— **绝不是** transport 作用域的取值 |

---

## 工作原理

Transport 分为两层：

1. **场景（Scenario）** —— 单个单词的预设，会展开为多个细粒度键。
2. **细粒度键（Granular key）** —— 多个单词组成的名称，对应一项具体的共享资源。

```php
// app.php
'transport' => [
    'full' => 'platform',           // 场景预设
    'file_storage' => 'local',      // 细粒度覆盖
],
```

**解析顺序：** 显式细粒度键 → 匹配的场景。

细粒度键始终优先于场景展开。如果某个键未设置且没有场景覆盖它，该资源保持 **local**（当前包）。

---

## 作用域取值

每个场景或细粒度键都会被赋予一个作用域：

| 作用域 | 含义 |
|-------|---------|
| `local` | 当前应用包（省略时的默认值） |
| `platform` | 共享平台作用域（`app = platform`、`pinx_*` 表） |
| `host` | 打开本应用的宿主应用（预览 / `App::meeting()`） |
| `{package}` | 指定应用，例如 `com_pinoox_manager` |

对于 **`auth_config`** 和 **`auth_cookie`**，`platform` 和 `{package}` 会解析为 **提供认证设置** 的应用（通常是已安装的 `com_pinoox_manager`）。

---

## 场景参考

单个单词的预设。在 `app.php` 中以 `'transport' => ['{scenario}' => '{scope}']` 的形式使用。

| 场景 | 说明 | 包含的细粒度键 |
|----------|-------------|------------------------|
| `full` | 所有共享资源 | `user_table`、`auth_config`、`auth_cookie`、`session_token`、`file_storage`、`access_table` |
| `user` | 登录系统：账户、认证、会话令牌 | `user_table`、`auth_config`、`auth_cookie`、`session_token` |
| `storage` | 文件上传与元数据 | `file_storage` |
| `access` | 角色与权限 | `access_table` |

---

## 细粒度键参考

多个单词组成的资源名称。用于共享或覆盖单项资源。

| 细粒度键 | 控制范围 | 使用方 |
|--------------|----------|---------|
| `user_table` | `UserModel` 的 `app` 列 / 全局作用域 | 用户账户 |
| `auth_config` | 认证模式、JWT 密钥、有效期（`auth` 配置块的来源） | `AuthConfig`、登录流程 |
| `auth_cookie` | 客户端键 / cookie 名称（`auth.key`） | Cookie 与 SPA 令牌存储 |
| `session_token` | `TokenModel` 的 `app` 列 / 数据库会话记录 | 会话持久化 |
| `file_storage` | `FileModel` 的 `app` 列 / 上传路径 | 上传与文件元数据 |
| `access_table` | 角色与权限模型的 `app` 作用域 | `RoleModel`、`PermissionModel`、`can()` |

---

## 常见配置

**平台的认证提供方（例如 manager）：**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**消费方应用 —— 全部共享，无本地 auth 配置块：**

```php
'transport' => ['full' => 'platform'],
```

**仅共享登录：**

```php
'transport' => ['user' => 'platform'],
```

**独立应用** —— 省略 `transport`，或将所有资源固定为本地：

```php
'transport' => ['user' => 'local'],
```

**在场景内覆盖单项资源：**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## 代码 API

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // 解析某个细粒度键对应的包
Transport::authSource();                       // 拥有认证设置的应用，或 null
Transport::sharesAuthWith($guest, $host);      // 跨应用认证检查
Transport::resolved();                         // 所有细粒度键 → 作用域
Transport::activeScenarios();                  // 例如 ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## 数据库

平台作用域的表使用 **`platform`** 连接和 **`pinx_`** 前缀。

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## 相关文档

- [app.php 清单](../start/app-manifest.md)
- [用户管理（User management）](./user-management.md)
- [访问与权限（Access & permissions）](./access-permissions.md)
- [文件管理（File management）](./file-management.md)

---

[← 返回索引](../README.md)

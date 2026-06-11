# 数据库入门

[← 返回索引](../README.md)

Pinoox 3.x 通过 **Illuminate Database**（Eloquent + Query Builder）和 **`Pinoox\Portal\Database\DB`** Portal 提供数据库层。每个应用在 `app.php` 中定义自己的连接；平台凭据存放在项目的 `.env` 中。

---

## DB Portal

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // 当前活动应用的连接
DB::app('com_my_shop')->table('orders')->get();      // 指定应用的连接
DB::core()->table('user')->get();                     // pincore 表
DB::tableName('orders');                             // 带前缀的物理表名
```

---

## 平台默认连接

```php
// app.php
'database' => null,
```

模型和查询使用项目默认连接（`.env` 中的 `DB_CONNECTION`）。

---

## 命名的平台连接

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox 会基于平台配置块克隆一个名为 `app_{package}_default` 的连接。

---

## 表前缀

### 共享数据库上的应用（无专用数据库）

默认：使用从包名派生的短前缀。

```php
'database' => null,
// com_pinoox_manager + 表 notifications → manager_notifications
```

### 显式前缀

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// 或
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### 专用数据库 —— 无前缀

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### 核心表

始终使用 **`pincore_`** 前缀：`pincore_user`、`pincore_token`、`pincore_file`。

---

## 完全专用数据库

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

多连接：

```php
'database' => [
    'default' => 'primary',
    'connections' => [
        'primary' => ['driver' => 'sqlite', 'database' => ':memory:'],
        'analytics' => ['use' => 'mysql', 'prefix' => 'an_'],
    ],
],
```

```php
DB::app('com_my_shop', 'analytics')->table('events')->get();
```

---

## 应用的 .env 键

| 键 | 映射到 |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | 专用凭据 |

**不要** 在应用的 `.env` 中使用 `DB_CONNECTION` —— 它会被忽略。

---

## database 文件夹结构

```text
apps/{package}/
├── patches/                 ← 一次性数据补丁
└── database/
    migrations/
    seed/
```

---

## 解析表名

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## 提示

- 业务逻辑放在 Model/Component 中；控制器保持精简。
- 迁移（Migration）和种子（Seed）只存放在应用的 `database/` 文件夹中 —— 不要放进 pincore。
- Pinker 可以覆盖 `database.use` 和 `database.prefix`。

---

## 相关文档

- [查询构建器（Query Builder）](./query-builder.md)
- [迁移（Migrations）](./migrations.md)
- [Eloquent —— 入门](../eloquent-orm/getting-started.md)
- [应用数据库配置（app.php）](../start/app-manifest.md)

---

[← 返回索引](../README.md)

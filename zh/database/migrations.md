# 迁移（Migrations）

[← 返回索引](../README.md)

迁移用于对数据库 **结构（schema）** 变更进行版本管理。在 Pinoox 3.x 中，应用的迁移文件位于 `apps/{package}/database/migrations/`，核心文件位于 `system/database/migrations/`。

---

## 创建迁移

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

输出：

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## 文件结构

```php
<?php
namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('posts', 'com_acme_blog'));
    }
};
```

`$this->table('posts', $package)` 会应用正确的应用前缀。

---

## 运行迁移

```bash
# 应用迁移
php pinoox migrate com_acme_blog

# 核心迁移
php pinoox migrate pincore

# 平台迁移（pinx_* 表）
php pinoox migrate platform
```

---

## 状态与回滚

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## 核心迁移（示例）

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

核心表：前缀为 **`pincore_`**（平台作用域为 `pinx_`）。

---

## 命名空间

| 位置 | 命名空间 |
|----------|-----------|
| 应用 | `App\{package}\database\migrations` |
| 核心 | `Pinoox\Database\migrations` |

---

## 旧路径

Pinoox 仍会读取旧的 `apps/{package}/migrations/` 文件夹，但 **新** 文件会创建在 `database/migrations/` 中。

---

## Migration、Seed 与 Patch 对比

| 类型 | 用途 | 命令 |
|------|---------|---------|
| Migration | 结构（CREATE/ALTER） | `php pinoox migrate {package}` |
| Seeder | 初始数据 | `php pinoox seeder:run {package}` |
| Patch | 一次性数据变更 | `php pinoox patch:run {package}` |

完整补丁指南：[补丁（数据更新）](./patches.md)。

---

## 最佳实践

- 每个迁移只做一个逻辑变更（一张表或一次 ALTER）。
- 始终编写 `down()`。
- 不要修改已经运行过的迁移 —— 创建一个新迁移。
- 指向核心表的外键使用 `$this->table(Table::FILE, 'platform')`。

---

## 相关文档

- [数据库入门](./getting-started.md)
- [Seeder / 工厂（factories）](../eloquent-orm/factories.md)
- [应用数据库配置（app.php）](../start/app-manifest.md)

---

[← 返回索引](../README.md)

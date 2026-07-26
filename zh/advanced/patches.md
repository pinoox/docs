# 补丁（数据更新）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，**补丁（patch）** 是一次性的**运维变更**：修复数据、迁移记录、同步配置，或执行升级后逻辑。它不是**迁移（migration）**（结构变更），也不是**填充器（seeder）**（可重复的种子数据）。

---

## 何时使用补丁

| 工具 | 用途 |
|------|---------|
| **Migration** | CREATE/ALTER 表和列 |
| **Seeder** | 初始或示例数据（手动运行） |
| **Patch** | 运行一次并在 `history` 中记录 |

补丁示例：

- 修复 bug 后的无效行
- 为旧记录回填默认值
- 重命名数据库中的配置值
- 新版本发布后的升级后逻辑

---

## 文件位置

```text
vendor/pinoox/pincore/patches/     ← 平台（CLI：platform）
apps/{package}/patches/            ← 你的应用
```

> 旧路径 `database/patches/` **不再使用**。补丁与 `app.php` 同级，不在 `database/` 下。

---

## 创建补丁

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

CLI 会写入带时间戳的文件，例如：

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

存根结构（匿名类）：

```php
<?php
namespace App\com_acme_shop\patches;

use Pinoox\Component\Database\Patch\PatchBase;
use Pinoox\Portal\Database\DB;

return new class extends PatchBase
{
    public function description(): string
    {
        return 'Set empty contact status to active';
    }

    public function shouldRun(): bool
    {
        return DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->exists();
    }

    public function canRollback(): bool
    {
        return false;
    }

    public function up(): void
    {
        DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->update(['status' => 'active']);
    }
}
```

平台命名空间：`Pinoox\Patches`。

---

## PatchBase 方法

| 方法 | 作用 |
|--------|------|
| `up()` | 主逻辑（通过 `run()` 调用） |
| `down()` | 当 `canRollback()` 为 true 时回滚 |
| `shouldRun()` | 若为 false，补丁记为 **skipped** |
| `canRollback()` | 是否允许回滚 |
| `description()` | 历史中的人类可读文本 |
| `metadata()` | 存入历史的额外 JSON |

---

## CLI 命令

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**注意：** `patch:run` 会先运行**平台迁移**，再运行所选包的补丁。

别名：`php pinoox patch` = `patch:run`。

---

## history 表

迁移与补丁共用 **`history`** 表：

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

成功的补丁不会自动重新运行。

---

## 安装器

系统应用 `com_pinoox_installer` 在设置过程中通过 `SetupService` 运行迁移和补丁。

---

## 最佳实践

- 不要编辑已运行过的补丁 — 应创建新补丁。
- 结构变更用迁移，不要用补丁。
- 实现 `shouldRun()`，使幂等检查可跳过不必要的工作。
- 仅在 `down()` 安全时启用回滚。

---

## 相关文档

- [迁移](./migrations.md)
- [填充器 / 工厂](../eloquent-orm/factories.md)
- [CLI 参考](../start/cli-reference.md)

---

[← 返回索引](../README.md)

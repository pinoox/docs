# 测试数据 — 填充器（Seeders）

[← 返回索引](../README.md)

Pinoox 3.x 的 CLI 中**不包含** Laravel 风格的 **Model Factory**。初始数据和开发数据的推荐方式是使用 **`SeederBase`** 在 `apps/{package}/database/seeders/` 中编写 **填充器（Seeder）**。

---

## 创建填充器

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## 结构

```php
<?php
namespace App\com_acme_blog\database\seeders;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;
use Pinoox\Portal\Hash;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::insert([
            [
                'user_id' => 1,
                'title' => 'First post',
                'body' => 'Sample content',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Second post',
                'body' => '...',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
```

---

## 调用其他填充器

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
    ]);

    // 用户创建后的依赖数据
    PostModel::factory(); // ❌ 不可用 — 使用 insert 或手动 create
}
```

---

## 使用 Model 的 create

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "Post #{$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## 运行填充器

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## 推荐顺序

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## 生产环境中的填充器

- 仅填充**必要**数据（角色、默认设置）。
- 用 `APP_ENV` 保护假数据/开发数据：

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // 示例数据
}
```

---

## 填充器与补丁

| Seeder | Patch |
|--------|-------|
| 初始 / 示例数据 | 对已有数据的一次性修复 |
| `seeder:run` — 可谨慎重复运行 | `patch:run` — 只记录一次 |

---

## 提示

- 编写幂等填充器（用 `firstOrCreate` 而非盲目 `insert`）。
- 不要在填充器中提交真实凭据。
- 单元测试使用 Pest 夹具或 `:memory:` sqlite。

---

## 相关文档

- [迁移](../database/migrations.md)
- [Eloquent 入门](./getting-started.md)
- [应用数据库配置（app.php）](../start/app-manifest.md)

---

[← 返回索引](../README.md)

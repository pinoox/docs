# Pinker 与缓存（Cache）

[← 返回索引](../README.md)

**Pinker** 是 Pinoox 3.x 中的烘焙（bake）/运行时层：配置与缓存从源文件编译为可直接 `include` 的 PHP 文件，从而加快启动速度。每个应用的标准路径为：**`pinker/apps/{package}/`**。

---

## 目录结构

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← 烘焙后的 app.php
        └── cache/
            ├── manifest.php     ← 校验和 + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← 编译后的模板
```

项目级别：

```
pinker/config/          ← 烘焙后的配置（与环境无关）
pinker/state/config/    ← 安装后的覆盖配置（例如 database）
```

---

## CLI 命令

```bash
# 为单个应用重建 Pinker
php pinoox pinker:rebuild com_acme_shop

# 简短别名
php pinoox bake com_acme_shop

# 状态：比较源文件与烘焙后的输出
php pinoox pinker:status com_acme_shop

# 构建缓存（route、api、twig、pinker 等）
php pinoox cache:build com_acme_shop

# 仅 Twig
php pinoox cache:build com_acme_shop --only=twig

# 仅 Pinker
php pinoox cache:build com_acme_shop --only=pinker

# 清除缓存
php pinoox cache:clear com_acme_shop
```

---

## 何时需要重建

| 事件 | 命令 |
|-------|---------|
| 修改 `app.php` 或配置 | `pinker:rebuild` |
| 修改 route / api | `cache:build` |
| 在生产环境修改 `.twig` | `cache:build --only=twig` |
| 服务器安装之后 | `cache:build` + `pinker:rebuild` |
| 构建 `.pinx` 之前 | `cache:build`（缓存打包进安装包） |

---

## 在运行时启用缓存

在 `apps/{package}/app.php` 中：

```php
'cache' => [
    'enabled' => false,   // 默认值 —— 生产环境如有需要可设为 true
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## 应用镜像 —— `pinker/app.php`

每个应用都可以有一个烘焙后的镜像：

```
apps/com_acme_shop/pinker/app.php   ← 仓库中的源文件/参考
         ↓ 烘焙（bake）
pinker/apps/com_acme_shop/app.php   ← 运行时
```

---

## `pinker()` 辅助函数

用于手动烘焙数据：

```php
pinker($data, ['lifetime' => 3600]);
```

通常使用 CLI 即可；应用代码中很少需要它。

---

## 推荐的部署流程

```bash
# 1. 构建前端
php pinoox theme:frontend build com_acme_shop

# 2. 缓存
php pinoox cache:build com_acme_shop

# 3. pinker（与环境相关）
php pinoox pinker:rebuild com_acme_shop
```

---

## 提示

- 不要手动编辑 `pinker/state/` —— 该目录由安装程序写入。
- 开发环境中通常关闭运行时缓存；只有在大量改动后才需要重建。
- `.pinx` 可以附带预构建的缓存；在目标服务器上只需运行一次 `cache:build --only=pinker`。

---

## 相关文档

- [配置（Config）](../basic/config.md)
- [Twig 模板](../basic/templates.md)
- [CLI 参考](../start/cli-reference.md)
- [路由（Router）](../basic/routers.md)

---

[← 返回索引](../README.md)

# Pinx CLI（单应用项目）

[← 返回索引](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** 是面向**单应用** Pinoox 项目的开发者 CLI — 生成脚手架、运行、迁移、构建并发布 `.pinx` 包，全程无需接触多应用管理器。

它基于 `pinoox/pincore` 和 `pinoox/app` 模板构建。你的项目根目录**就是**应用本身：一个 `app.php`、一个包、一套工作流。

> 对于经典的多应用平台安装，请改用 [`php pinoox`](./cli-reference.md)。

---

## 快速开始

只需安装一次 Pinx，然后创建并运行新应用：

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # 会建议 com_my_shop — 在向导中确认或修改
cd my-shop
cp .env.example .env          # 如果使用数据库，请设置 DB_*
pinx setup                    # 迁移平台和应用，并运行填充器
pinx dev                      # http://127.0.0.1:8000
```

如果找不到 `pinx` 命令，请将 Composer 的全局 `bin` 目录加入 `PATH`：

- Linux / macOS：`~/.composer/vendor/bin` 或 `~/.config/composer/vendor/bin`
- Windows：`%APPDATA%\Composer\vendor\bin`

| 步骤 | 作用 |
|------|--------------|
| `composer global require` | 在你的机器上安装 `pinx` 命令 |
| `pinx new my-shop` | 基于 `pinoox/app` 生成脚手架；向导会建议一个三段式包名（例如 `com_my_shop`） |
| `.env` | 数据库和项目路径 — 从 `.env.example` 复制 |
| `pinx setup` | 一键完成：平台迁移 → 应用迁移 → 填充器 |
| `pinx dev` | PHP 开发服务器；若配置了前端栈，会同时启动 Vite |

包名遵循 `com_{vendor}_{name}` 格式 — 例如 `com_acme_shop`、`ir_yekdo_app`。已经在一个空文件夹里了？用 `pinx init` 代替 `pinx new`。

**`setup` 前的可选检查：** `pinx doctor` 会报告 PHP、目录布局、环境变量、数据库和构建就绪状态。

---

## 替代方案：`composer create-project`

无需全局安装 — 模板自带项目内的 `bin/pinx`：

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## 单应用模式有何不同

经典的 Pinoox 安装会在 `apps/` 下保存多个应用，并在运行时选择其一。**单应用**模式将其扁平化：

- 项目根目录下的 `app.php` 保存包标识和 pinx 设置
- `Controller/`、`Model/`、`routes/`、`theme/` 位于根目录 — 而不是 `apps/{package}/` 里
- `platform/` 保存本地路由和启动器配置（不会包含在 `.pinx` 构建中）
- Pinx 始终以**你的**应用为目标 — 没有包选择器，没有管理器 UI

```
my-shop/                    ← 项目根目录 = 应用根目录
├── app.php                 ← package、version、pinx.sign、frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← 开发宿主 + 部署层（仅本地）
├── bin/pinx                ← 项目本地的 CLI 入口
└── vendor/pinoox/pincore   ← 框架
```

---

## 安装方式

| 位置 | 方法 | 适用场景 |
|-------|-----|-------------|
| **全局** | `composer global require pinoox/pinx-cli` | 推荐 — 可在任何位置使用 `pinx new` 和 `pinx init` |
| **按项目** | `pinoox/app` 自带 `bin/pinx` | 执行 `composer create-project` 之后 — 无需全局安装 |

```bash
pinx -v          # CLI 版本（例如 pinx-cli 1.1.7）
pinx list        # 分组的命令概览
pinx help setup  # 查看单个命令的详情
```

---

## 日常工作流

```bash
pinx dev                    # 本地服务器（当 app.php → frontend.stack 设置时同时启动 Vite）
pinx dev --open             # 启动后打开浏览器
pinx dev --no-frontend      # 仅 PHP

pinx migrate                # 运行应用迁移（--platform 会先运行平台迁移）
pinx migrate:st             # 迁移状态
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # 列出命名 Action（--validate、--json）
pinx test                   # 运行应用测试（Pest）
```

**前端**（当 `theme/` 使用 Vue/React + Vite 时）：

```bash
pinx fe:info                # 技术栈、npm 脚本、路径
pinx fe:i                   # npm install
pinx fe:d                   # Vite 开发服务器
pinx fe:b                   # 生产构建
pinx fe:sc --stack=vue      # 生成起步文件
```

**依赖：**

```bash
pinx deps:st                # Composer + npm 状态
pinx deps:i                 # 安装全部依赖
pinx deps:up                # 更新全部依赖
```

**Pinker**（构建缓存）：

```bash
pinx pinker:st              # 缓存与源码对比
pinx pinker:rb              # 重新构建
pinx pinker:df              # 差异
```

---

## 发布到生产环境

构建 `.pinx` 包，以便安装到完整的 Pinoox 平台上（管理器 → 应用）：

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # 提升 app.php 中的版本号 + 构建
pinx release --sign         # 当 app.php → pinx.sign 配置了密钥时进行签名
```

`pinx build` 会应用合理的默认值（排除 `vendor/`、`bin/`、`.env`、`platform/` 和开发工具）。仅在需要时才在 `app.php` 中覆盖：

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor 会运行结构化诊断，并在检查失败时给出修复命令建议：

| 分组 | 检查项 |
|-------|--------|
| **Project** | `app.php`、包标识、`platform/` 布局 |
| **Runtime** | PHP 版本（≥ 8.1）、扩展、可写路径 |
| **Dependencies** | Composer vendor、可选的 Node/npm |
| **Environment** | `.env` 是否存在及关键变量 |
| **Database** | 连接（可用 `--skip-db` 跳过） |
| **Frontend** | 主题技术栈、`package.json`（可用 `--skip-frontend` 跳过） |
| **Build** | 导出就绪状态、图标、版本字段 |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # 适合 CI 的报告
pinx doctor --no-fixes      # 隐藏建议的修复命令
```

---

## 命令参考

运行 `pinx list` 查看分节的命令概览。简写别名显示在括号中。

### 项目

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `new` | — | 基于 `pinoox/app` 生成脚手架（向导或参数） |
| `init` | — | 初始化当前目录（`--force` 覆盖） |
| `setup` | — | 数据库：迁移平台 + 应用，然后填充数据 |
| `doctor` | `dr` | 健康检查 — `--json`、`--skip-db`、`--skip-frontend` |
| `info` | `inf` | 显示 `app.php` 中的元数据 |

### 开发

| 命令 | 说明 |
|---------|-------------|
| `dev` | 开发服务器；当 `frontend.stack` 为 vue/react 时启动 Vite |

### 数据库

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `migrate:run` | `migrate` | 运行应用迁移（`--platform` 先运行平台迁移） |
| `migrate:status` | `migrate:st` | 迁移状态 |
| `migrate:rollback` | `migrate:rb` | 回滚最后一个批次（`--ignore-fk`） |
| `migrate:create <name>` | `migrate:cr` | 创建迁移文件 |
| `migrate:platform` | `migrate:pl` | 仅平台迁移 |
| `seeder:run` | `seed` | 运行填充器（`-c` 指定类） |

### 补丁

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `patch:run` | `patch` | 运行待执行的补丁 |
| `patch:status` | `patch:st` | 补丁状态 |
| `patch:rollback` | `patch:rb` | 回滚最后一个补丁批次 |

### 构建与发布

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `build` | `bld` | 构建 `.pinx` 包 |
| `release` | `rel` | 版本号提升 + 构建（`--bump`、`--sign`） |

### 脚手架

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller、model、migration、patch、portal、form-request、seeder、test |

### 路由

| 命令 | 说明 |
|---------|-------------|
| `route:actions` / `routes` | 列出命名 Action（`--validate`、`--json`） |

### 依赖

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Composer + npm 状态 |
| `deps:install` | `deps:i` | 安装依赖 |
| `deps:update` | `deps:up` | 更新依赖 |

### 前端

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | 主题技术栈与 npm 脚本 |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | 生产构建 |
| `fe:dev` | `fe:d` | Vite 开发服务器 |
| `fe:scaffold` | `fe:sc` | 起步文件（`--stack=vue\|react\|twig`） |

### 计划任务

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | 列出 `schedule.php` 中的定时任务 |
| `schedule:run` | `sched:run` | 运行到期任务（`--dry-run`） |

### Pinion（可恢复上传）

转发到 `php pinoox pinion:*` — 管理临时分块上传 session。

| 命令 | 说明 |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

参见 [Pinion 协议](../advanced/pinion.md)。

### Pinker

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | 缓存与源码对比 |
| `pinker:rebuild` | `pinker:rb` | 重新构建缓存 |
| `pinker:diff` | `pinker:df` | 显示差异 |
| `pinker:clear` | `pinker:cl` | 清除缓存 |
| `pinker:overrides` | `pinker:ov` | 列出覆盖项 |

### 质量与文档

| 命令 | 说明 |
|---------|-------------|
| `test` / `pest` | 运行应用测试（`--unit`、`--feature`） |
| `api:docs` | REST API 文档 |
| `graphql:docs` | GraphQL Schema 文档 |

### 元信息

| 命令 | 别名 | 说明 |
|---------|---------|-------------|
| `list` | — | 分组的命令概览 |
| `version` | `ver` | CLI 版本 |

---

## 应用检测

Pinx 会从当前工作目录向上查找，直到找到一个有效的单应用项目：

1. 存在 `app.php`，且它返回一个包含非空 `package` 键的数组
2. `composer.json` 中要求了 `pinoox/pincore`，或者存在 `vendor/pinoox/pincore`

可以通过环境变量覆盖检测到的包：

| 变量 | 用途 |
|----------|---------|
| `PINX_PACKAGE` | 强制指定 CLI 的目标包 |
| `PINOOX_DEV_APP` | `PINX_PACKAGE` 的别名 |
| `PINX_DEV=1` | 开发模式（pinx 委托给 pincore 时自动设置） |

---

## 环境要求

- **PHP** ≥ 8.1，并安装 `pinoox/pincore` 所需的扩展
- **Composer** 2.x
- **Node.js** + npm — 仅在使用 Vite/Vue/React 前端时需要
- **数据库** — MySQL/MariaDB 或你在 `.env` 中配置的任何数据库（纯静态/Twig 应用可不配置）

---

## 相关文档

- [安装 Pinoox](./installing-pinoox.md)
- [Pinoox CLI 参考（多应用）](./cli-reference.md)
- [你的第一个应用](./your-first-app.md)
- [app.php 清单](./app-manifest.md)

---

[← 返回索引](../README.md)

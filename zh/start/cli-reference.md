# Pinoox CLI 参考

[← 返回索引](../README.md)

所有命令都在**项目根目录**下运行：

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

当命令需要包名而你未提供时，Pinoox 会显示一个交互式选择器。

> 对于**单应用**项目，请使用独立的 [Pinx CLI](./pinx-cli.md)（`pinx dev`、`pinx setup`、`pinx build` 等）。

---

## 常用别名

| 别名 | 命令 |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## 应用

| 命令 | 用途 |
|---------|---------|
| `app:create {package}` | 生成应用脚手架（`--simple`、`--stack`、`--profile`） |
| `app:list` | 列出应用 |
| `app:delete` | 删除应用 |
| `app:router set /path {package}` | URL 映射 |
| `app:domain` | 域名 → 应用映射 |
| `app:resolve` | 调试当前激活的应用 |

---

## 脚手架

| 命令 | 输出 |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest 类 |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest 文件 |
| `theme:frontend` | 前端工具链（Vue/React/Twig） |

---

## 数据库

| 命令 | 用途 |
|---------|---------|
| `migrate {package}` | 运行迁移（应用、`platform`、`pincore`） |
| `migrate:create` | 新建迁移文件 |
| `migrate:status` / `migrate:rollback` | 查看状态 / 回滚 |
| `seeder:run` | 运行填充器 |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [补丁（Patches）](../database/patches.md) |
| `query` | 原生 SQL（调试用） |

---

## 缓存与 Pinker

| 命令 | 用途 |
|---------|---------|
| `cache:build` / `cache:clear` | 运行时缓存 |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | 重置 Pinker + 配置 |

---

## 计划任务

| 命令 | 用途 |
|---------|---------|
| `schedule:list` | 列出定时任务 |
| `schedule:run` | 运行到期任务 |

参见[计划任务（Schedule）](../advanced/schedule.md)。

---

## 路由

| 命令 | 用途 |
|---------|---------|
| `route:actions {package}` | 列出命名 Action |

---

## Pinx 打包

| 命令 | 用途 |
|---------|---------|
| `pinx:build` | 构建 `.pinx` 包 |
| `pinx:install` | 安装包 |
| `pinx:info` | 元数据 |
| `wizard:list` / `wizard:install` | 安装向导 |

---

## 开发

| 命令 | 用途 |
|---------|---------|
| `test` | Pest 测试 |
| `serve` | 内置开发服务器 |
| `log:view` / `log:clear` | 日志 |
| `deps` | 跨应用管理 Composer/npm |
| `version` / `mode:show` | 版本 / 运行模式 |

---

## 包名参数

| 取值 | 含义 |
|-------|---------|
| `com_my_shop` | 指定应用 |
| `platform` | 平台的迁移/补丁/填充器 |
| `pincore` | 框架核心 |
| `all` | 所有应用（缓存/pinker） |

---

## 相关文档

- [你的第一个应用](./your-first-app.md)
- [迁移（Migrations）](../database/migrations.md)
- [补丁（Patches）](../database/patches.md)

---

[← 返回索引](../README.md)

# 项目结构

[← 返回索引](../README.md)

Pinoox 采用 HMVC 架构：`apps/{package}/` 下的每个应用都是一个完整、独立的 MVC 模块。框架核心位于 `vendor/pinoox/pincore/`，只有在修改平台本身时才需要编辑。

---

## 项目布局

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← 核心（Composer 包）
├── apps/                    ← 所有应用
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← 上传文件与应用存储
```

---

## 应用布局

```
apps/com_acme_shop/
├── app.php                  ← 清单（必需）
├── boot.php                 ← 编程式路由/事件（可选）
├── schedule.php             ← 定时任务（可选）
├── Controller/              ← HTTP 处理器
├── Model/                   ← Eloquent 模型
├── Flow/                    ← 中间件
├── Component/               ← 业务逻辑
├── Portal/                  ← 应用门面（可选）
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← Action 名称常量（可选）
├── theme/default/           ← Twig + 静态资源
├── lang/en/                 ← 翻译
├── config/                  ← 应用配置
├── database/migrations/
└── pinker/                  ← 构建镜像
```

视图并不在单独的 `View/` 文件夹中 — 模板位于 `theme/{themeName}/`。

---

## app.php — 关键字段

```php
<?php

return [
    'package' => 'com_acme_shop',   // = 文件夹名称
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## 命名空间

PSR-4：`App\` → `apps/`

| 文件 | 命名空间 |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## 命名规则

- 包名：`com_{vendor}_{name}` — 例如 `com_acme_shop`
- 文件夹名 = `app.php` 中的 `package` = 命名空间片段
- 数据库表前缀：`{package}_`（例如 `com_acme_shop_orders`）

---

## 应用与核心的边界

| 改动 | 位置 |
|--------|----------|
| 新端点 | `apps/{package}/Controller/` + `routes/` |
| 迁移（Migration） | `apps/{package}/database/migrations/` |
| 框架 Bug | `pinoox/pincore`（上游仓库） |
| UI | `apps/{package}/theme/` |

保持应用之间相互独立 — 使用 `Pinoox\Portal\*` 门面，而不要让应用彼此耦合。

---

## 相关文档

- [你的第一个应用](./your-first-app.md)
- [路由（Router）](../basic/routers.md)
- [控制器（Controllers）](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← 返回索引](../README.md)

# 什么是 Pinoox？

[← 返回索引](../README.md)

Pinoox 是一个现代化的开源 PHP 框架（3.x），基于 HMVC 架构和**应用（app）**概念构建。它让模块化 Web 开发变得简单直接：每个应用都是 `apps/{package}/` 下的一个独立 MVC 单元，而共享的框架核心位于 `vendor/pinoox/pincore/`。

---

## 以应用为中心的架构

在一个 Pinoox 安装中，多个独立应用可以并行运行：

```
{project_root}/
├── index.php              ← Web 入口
├── pinoox                 ← CLI 入口
├── composer.json
├── vendor/pinoox/pincore/ ← 框架核心（仅在修改核心时编辑）
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← 你的应用
```

- **项目（Project）** — 包含 `index.php` 和 `apps/` 的文件夹（文件夹名称无关紧要）。
- **应用（App）** — 完整模块，包含独立的控制器、模型、路由、主题和配置。
- **核心（Core）** — 共享引擎（路由、HTTP、数据库、Twig、CLI 等）。

请把业务逻辑写在 `apps/` 中，而不是 `vendor/pinoox/pincore/` 中。

---

## HTTP 请求生命周期

```
浏览器 → index.php → 初始化（bootstrap）
      → 解析当前激活的应用（域名或 URL 前缀）
      → 加载 app.php 和 routes/
      → Flows → Controller → Model（可选）→ View 或 JSON
```

---

## 应用命名

推荐的包名格式：

```
com_{vendor}_{name}
```

示例：`com_acme_shop` — 文件夹名称、`app.php` 中的 `package` 值以及命名空间片段必须完全一致。

---

## 适用场景

- 多板块网站和管理面板，每个板块都可以是一个独立应用
- 希望独立开发、测试和维护各模块的团队
- 使用 Composer 和集成 CLI（`php pinoox …`）的 PHP 8.2+ 项目

---

## 相关文档

- [Pinoox 功能特性](./features-pinoox.md)
- [安装 Pinoox](../start/installing-pinoox.md)
- [你的第一个应用](../start/your-first-app.md)
- [笔记 API 实战演练](../examples/simple-api-app.md)
- [电话簿实战演练](../examples/phonebook-app.md)
- [联系表单实战演练](../examples/contact-form-app.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

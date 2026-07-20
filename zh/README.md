# Pinoox 文档

在 Pinoox 平台（PHP 8.2+，HMVC 架构）上构建应用的官方开发者文档。

每篇指南都通过实用示例介绍**一种推荐做法**。请在下方选择一个章节，或按主题浏览。

**语言:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](./README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### 简介

#### [什么是 Pinoox？](./introduction/what-is-pinoox.md)
#### [Pinoox 功能特性](./introduction/features-pinoox.md)
#### [为 Pinoox 贡献](./introduction/contributions.md)

### 快速上手

#### [安装 Pinoox](./start/installing-pinoox.md)
#### [你的第一个应用](./start/your-first-app.md)
#### [项目结构](./start/structure.md)
#### [Pinoox CLI 参考](./start/cli-reference.md)
#### [Pinx CLI（单应用项目）](./start/pinx-cli.md)
#### [app.php 清单参考](./start/app-manifest.md)

### 实战演练

#### [实战演练：笔记 API 应用](./examples/simple-api-app.md)
#### [实战演练：电话簿 Web 应用](./examples/phonebook-app.md)
#### [实战演练：联系表单应用](./examples/contact-form-app.md)
#### [实战演练：简易博客应用](./examples/blog-app.md)
#### [实战演练：任务看板（Todo）](./examples/task-board-app.md)
#### [实战演练：图片相册应用](./examples/gallery-app.md)
#### [实战演练：Vue SPA 面板](./examples/vue-spa-app.md)
#### [实战演练：React SPA 面板](./examples/react-spa-app.md)
#### [实战演练：Vite 混合应用（Twig + JS 组件）](./examples/vite-hybrid-app.md)

### 核心概念

#### [路由（Router）](./basic/routers.md)
#### [控制器（Controllers）](./basic/controllers.md)
#### [Flow（中间件）](./basic/flows.md)
#### [HTTP 请求（Request）](./basic/requests.md)
#### [HTTP 响应（Response）](./basic/responses.md)
#### [URL 与链接构建](./basic/url.md)
#### [文件路径（Path）](./basic/path.md)
#### [验证（Validation）](./basic/validation.md)
#### [视图（Views）](./basic/views.md)
#### [Twig 模板](./basic/templates.md)
#### [Portal（门面）](./basic/portal.md)
#### [配置（Config）](./basic/config.md)
#### [语言与翻译](./basic/language.md)

### 进阶主题

#### [Pinker 与缓存（Cache）](./advanced/pinker.md)
#### [补丁（数据更新）](./advanced/patches.md)

#### [应用服务（Component + Portal）](./advanced/services.md)
#### [全局辅助函数（Global Helpers）](./advanced/helpers.md)
#### [发送邮件（Email）](./advanced/mail.md)
#### [HTTP 客户端（HTTP Client）](./advanced/http-client.md)
#### [用户管理（User Management）](./advanced/user-management.md)
#### [文件管理（File Management）](./advanced/file-management.md)
#### [Pinion 协议](./advanced/pinion.md)
#### [令牌管理（Token Management）](./advanced/token-management.md)
#### [访问与权限（Access & permissions）](./advanced/access-permissions.md)
#### [Transport（共享资源）](./advanced/transport.md)
#### [boot.php 与事件（Events）](./advanced/boot-and-events.md)
#### [计划任务（Scheduling / cron）](./advanced/schedule.md)

### 数据库

#### [数据库入门](./database/getting-started.md)
#### [查询构建器（Query Builder）](./database/query-builder.md)
#### [分页（Pagination）](./database/pagination.md)
#### [迁移（Migrations）](./database/migrations.md)

### Eloquent ORM

#### [Eloquent ORM 入门](./eloquent-orm/getting-started.md)
#### [Eloquent 关联关系](./eloquent-orm/relationships.md)
#### [Eloquent 集合](./eloquent-orm/collections.md)
#### [修改器与类型转换](./eloquent-orm/mutators-casts.md)
#### [API 资源](./eloquent-orm/api-resources.md)
#### [模型序列化](./eloquent-orm/serialization.md)
#### [测试数据 — 填充器（Seeders）](./eloquent-orm/factories.md)

### 测试

#### [Pinoox 测试入门](./test/getting-started.md)
#### [Pinoox 中的 HTTP 测试](./test/http-tests.md)
#### [Pinoox 中的控制台测试](./test/console-tests.md)
#### [Pinoox 中的浏览器（HTML）测试](./test/browser-tests.md)
#### [Pinoox 中的数据库测试](./test/database.md)
#### [Pinoox 中的序列化测试](./test/serialization.md)
#### [Pinoox 中的模拟（Mocking）](./test/mocking.md)

### 常见问题

#### [常见问题排查](./faq/common-issues.md)
#### [联系支持](./faq/contact-support.md)

---

### 源代码
**示例源码：** [docs/source/](../source/) — 每个演练的完整代码

真实应用的分步指南 — 在阅读基础内容后、需要动手写代码时使用。

---

### 如何阅读本文档

1. 如果你是 Pinoox 新手，从**简介**和**快速上手**开始。
2. 跟随**实战演练** — 逐步构建 JSON API 和简单网站。
3. 在构建路由、控制器和视图时阅读**核心概念**。
4. 添加持久化时使用**数据库**和 **Eloquent ORM**。
5. 查阅**进阶主题**了解认证、文件、Pinker 和共享服务。
6. 上线前使用**测试**。

所有应用代码位于 `apps/{package}/`。框架核心是 `vendor/pinoox/pincore/` — 不要在那里编写应用业务逻辑。

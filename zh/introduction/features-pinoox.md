# Pinoox 功能特性

[← 返回索引](../README.md)

Pinoox 3.x 专为模块化 PHP 生态系统而设计：多个独立应用共享一个核心，提供 CLI 脚手架，以及面向 HTTP、数据库、主题和认证的内置工具。

---

## HMVC 架构与独立应用

`apps/{package}/` 下的每个应用都拥有完整的 MVC 结构：

| 层级 | 示例路径 |
|-------|--------------|
| Controller（控制器） | `Controller/MainController.php` |
| Model（模型） | `Model/PostModel.php` |
| View（视图，Twig） | `theme/default/home.twig` |
| Route（路由） | `routes/web.php`, `routes/actions.php` |
| Flow（中间件） | `Flow/AuthFlow.php` |

新增或停用某个应用不会影响其他应用。

---

## CLI 与快速开发

在项目根目录下执行：

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI 会生成标准的目录布局、`app.php` 以及初始路由文件。

---

## 路由与命名 Action

URL 路径与逻辑处理器保持分离：

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

这种模式让重构和测试更加容易。

---

## Flow（中间件）

在请求到达控制器之前，Flow 会先运行 — 用于认证、授权、日志记录等：

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

请在 `app.php` 中注册 Flow 别名。

---

## 视图与主题

- Twig 模板位于 `theme/{themeName}/`
- 使用 **`View::render()`** 进行渲染
- 在主题中通过 Vite 支持 SPA（Vue/React）

---

## 数据库与 Eloquent

- 通过 `DB` Portal 使用查询构造器（Query Builder）和 Eloquent
- 迁移（Migration）和填充器（Seeder）位于每个应用的 `database/migrations/` 中
- 表前缀基于包名（例如 `com_acme_blog_posts`）

---

## API 与 JSON 响应

继承 **`ApiController`** 并使用标准响应封装：

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## 国际化

翻译文件位于 `lang/{locale}/*.lang.php` — 适用于多语言应用。

---

## 相关文档

- [什么是 Pinoox？](./what-is-pinoox.md)
- [安装 Pinoox](../start/installing-pinoox.md)
- [路由（Router）](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← 返回索引](../README.md)

# 常见问题排查

[← 返回索引](../README.md)

针对 Pinoox 安装、运行和开发过程中常见错误的实用解决方案。每个小节只推荐**一种做法**。

---

## `composer install` 失败

**症状：** 缺少扩展、PHP 版本过低或网络超时。

**解决方法：**

1. 启用 PHP 8.1+ 及扩展 `mysqli`、`zip`、`mbstring`、`json`。
2. 安装前先运行平台检查：

```bash
php launcher/check.php
```

3. 再次安装：

```bash
composer install --no-interaction
```

在共享主机上，如果 `composer` 不在 PATH 中，可在本地构建 vendor 后再上传。

---

## 权限错误（文件访问）

**症状：** 无法写入 `cache/`、`storage/`、`pinker/`。

**解决方法（Linux/macOS）：**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

Web 服务器用户（例如 `www-data` 或 `apache`）必须能够写入可写文件夹。在 Windows/MAMP 上，请将项目文件夹放在 `Program Files` 之外。

---

## `.htaccess` / 重写不生效

**症状：** 除 `index.php` 之外的所有 URL 都返回 404；API 在浏览器中不返回 JSON。

**解决方法：**

1. 启用 Apache 的 `mod_rewrite`。
2. 为 DocumentRoot 设置 `AllowOverride All`。
3. 确认项目根目录中存在 `.htaccess`。
4. 快速测试：`http://localhost/pinoox/api/v1/ping` — 如果看到 JSON，说明重写生效。

在 nginx 上，请在服务器配置中编写 `try_files` 和 `index.php` 规则，而不是使用 `.htaccess`。

---

## 数据库连接失败

**症状：** `SQLSTATE[HY000] [2002] Connection refused` 或拒绝访问。

**解决方法：**

1. 确认 MySQL/MariaDB 正在运行。
2. 检查 `config/database.config.php` 或 `.env` 中的值：

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. 提前创建数据库（`CREATE DATABASE ... utf8mb4`）。
4. 在 cPanel 上，主机可能不是 `localhost` — 请使用面板中提供的主机名。

---

## 需要重建 Pinker

**症状：** 配置或路由陈旧；对 `app.php` 的修改未生效。

**解决方法：**

```bash
php pinoox pinker:rebuild com_my_shop
# 或使用别名：
php pinoox bake com_my_shop

# 所有应用：
php pinoox pinker:rebuild all
```

修改路由、配置或部署到生产环境后，通常都需要重建。

---

## 路由未找到（端点 404）

**症状：** 代码中已定义路由，却返回 404。

**解决方法：**

1. 确认路由文件位于 `apps/{package}/routes/` 中，并已列入 `app.php` → `router.routes`。
2. 将 URL 与应用前缀（`app:router`）对应起来：

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. 运行 Pinker 重建（见上文）。
4. 使用正确的 HTTP 方法（`GET` 还是 `POST`）。

---

## 404 — 应用未被解析

**症状：** 显示默认页面或 404；加载了错误的应用。

**解决方法：**

1. 检查路径/域名映射：

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. 在 `config/domain.config.php`（或相应映射）中正确设置 host 和 path。
3. 确认应用的 `app.php` 中设置了 `'enable' => true`。
4. 应用文件夹名必须等于 `app.php` 中的 `'package'`（例如 `com_my_shop`）。

---

## 测试失败

```bash
php pinoox test com_my_shop
```

- 使用单独数据库的 `.env.testing`
- 已运行迁移：`php pinoox migrate com_my_shop`
- `fakeApp()` 之后 → `deleteFakeApp()`

详情：[测试快速入门](../test/getting-started.md)

---

## 相关文档

- [安装 Pinoox](../start/installing-pinoox.md)
- [项目结构](../start/structure.md)
- [路由（Routers）](../basic/routers.md)
- [配置（Config）](../basic/config.md)
- [Pinoox Baker（Pinker）](../advanced/pinker.md)
- [数据库快速入门](../database/getting-started.md)
- [联系支持](./contact-support.md)

---

[← 返回索引](../README.md)

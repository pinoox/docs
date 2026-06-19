# 安装 Pinoox

[← 返回索引](../README.md)

本指南介绍如何安装 Pinoox 3.x。有两种方式可以开始：

| 方式 | 适用场景 |
|-------|----------|
| **A. 使用 [Pinx CLI](./pinx-cli.md) 的单应用模式** | 只构建一个应用 — 上手最快，无管理器 UI |
| **B. 完整平台（经典模式）** | 托管多个应用，带图形化安装器和管理器 |

---

## 环境要求

| 工具 | 版本 |
|------|---------|
| PHP | 8.2 或更高（需启用 ext-mysqli、ext-zip） |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js（可选） | 18+ — 仅用于前端主题构建 |

---

## 方式 A — 使用 Pinx CLI 的单应用模式

只需安装一次 [Pinx CLI](./pinx-cli.md)，然后创建并运行新应用：

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # 会建议 com_my_shop — 在向导中确认或修改
cd my-shop
cp .env.example .env          # 如果使用数据库，请设置 DB_*
pinx setup                    # 迁移平台和应用，并运行填充器
pinx dev                      # http://127.0.0.1:8000
```

或者不进行全局安装，直接使用项目模板：

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

随时运行 `pinx doctor` 以检查 PHP、环境变量、数据库和构建就绪状态。日常工作流和命令参考请见完整的 [Pinx CLI 指南](./pinx-cli.md)。

---

## 方式 B — 完整平台（经典模式）

### 1. 获取项目

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

也可以从 [GitHub](https://github.com/pinoox/pinoox) 下载最新发行版，解压后运行 `composer install`。

---

### 2. 放入 Web 服务器

将项目文件夹放到你的站点根目录中：

| 环境 | 示例路径 |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

将站点根目录（document root）设置为**项目根目录**（包含 `index.php` 的文件夹）— 而不是 `public` 子文件夹。

---

### 3. 创建数据库

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. 运行安装器

打开浏览器访问：

```
http://localhost/pinoox
```

系统应用 `com_pinoox_installer` 会启动。图形界面的安装步骤如下：

1. 检查 PHP 环境要求
2. 接受许可协议
3. 输入数据库连接信息
4. 创建管理员账户
5. 完成安装

---

### 5. 安装完成后

主要目录布局：

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← 应用
├── vendor/pinoox/pincore/  ← 核心
└── config/             ← 项目配置
```

创建你的第一个应用：

```bash
php pinoox app:create com_acme_blog
```

---

## 快速故障排查

| 问题 | 解决方法 |
|---------|-----|
| 页面空白 | 运行 `composer install` 并检查 PHP 错误日志 |
| 子路由 404 | 启用 mod_rewrite / `.htaccess` |
| 缺少扩展的错误 | 在 php.ini 中启用 ext-mysqli 和 ext-zip |
| 安装器无法打开 | 检查站点根目录配置以及运行时文件夹的写入权限 |

---

## 相关文档

- [Pinx CLI（单应用）](./pinx-cli.md)
- [你的第一个应用](./your-first-app.md)
- [项目结构](./structure.md)
- [什么是 Pinoox？](../introduction/what-is-pinoox.md)

---

[← 返回索引](../README.md)

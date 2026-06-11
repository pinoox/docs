# 联系支持

[← 返回索引](../README.md)

如果在查阅[常见问题排查](./common-issues.md)后仍然受阻，请使用下面的官方渠道。在联系支持之前，请准备好你的 Pinoox 版本、PHP 版本、错误信息和复现步骤。

---

## 常规支持

**邮箱：** [support@pinoox.com](mailto:support@pinoox.com)

适用于：

- 安装和部署问题
- 框架的异常行为
- HMVC 与应用架构方面的指导

邮件中请包含：

1. Pinoox 版本（`composer.json` → `version` 或 git 标签）
2. PHP 版本（`php -v`）
3. 操作系统和 Web 服务器（Apache/nginx、MAMP、cPanel 等）
4. 完整的错误文本或截图
5. 最小复现步骤

---

## GitHub Issues

用于已确认的 Bug、功能请求和公开的技术讨论：

**仓库：** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

提交新 Issue 之前：

- 搜索是否已有重复的 Issue
- 在最新的稳定/Beta 版本上测试
- 如果与 `pincore` 相关，请同时检查 `pinoox/pincore` 包

建议的 Issue 模板：

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.1.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## 安全报告

**邮箱：** [security@pinoox.com](mailto:security@pinoox.com)

**仅**用于安全漏洞 — SQL 注入、认证绕过、RCE、机密泄露。

- 在补丁就绪之前，不要公开发布细节（GitHub Issue）
- 如有可能，请附上最小化的 PoC 和影响说明

---

## 贡献代码

关于 PR 和框架开发：

- [参与贡献](../introduction/contributions.md)
- Fork → 分支 → 测试（`php pinoox test`）→ Pull Request

---

## 自助资源

| 主题 | 文档 |
|-------|-----|
| 安装 | [installing-pinoox.md](../start/installing-pinoox.md) |
| 第一个应用 | [your-first-app.md](../start/your-first-app.md) |
| 常见问题排查 | [common-issues.md](./common-issues.md) |
| 测试 | [getting-started.md](../test/getting-started.md) |

**官网：** [pinoox.com](https://www.pinoox.com/)

---

## 相关文档

- [常见问题排查](./common-issues.md)
- [什么是 Pinoox？](../introduction/what-is-pinoox.md)
- [参与贡献](../introduction/contributions.md)
- [安装 Pinoox](../start/installing-pinoox.md)

---

[← 返回索引](../README.md)

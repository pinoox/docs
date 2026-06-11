# 为 Pinoox 贡献

[← 返回索引](../README.md)

Pinoox 是一个开源项目。你的贡献 — 从 Bug 报告到 Pull Request — 都会帮助改进框架及其文档。

---

## 贡献方式

| 类型 | 说明 |
|------|-------------|
| Bug 报告 | 在 GitHub Issue 中附上复现步骤 |
| 功能请求 | 在 Issue 中描述使用场景 |
| Pull Request | 向对应仓库提交 Bug 修复或新功能 |
| 文档 | 改进 `docs/` 下的文件（波斯语或英语） |
| 开源应用 | 为社区发布一个 Pinoox 应用 |

---

## 报告 Bug

提交 Issue 时，请包含：

1. **标题** — 对问题的简短概括
2. **复现步骤** — 逐步说明
3. **预期行为**与**实际行为**的对比
4. **环境信息** — PHP 版本、Pinoox/pincore 版本、操作系统
5. **示例代码** — 如有可能请提供

[Pinoox GitHub Issues](https://github.com/pinoox/pinoox/issues)

---

## Pull Request

### 仓库

- **pinoox/pinoox** — 示例项目、系统应用、启动器
- **pinoox/pincore** — 框架核心（`vendor/pinoox/pincore/`）

核心改动请提交到 pincore 仓库，而不要只修改项目中本地的 `vendor/` 副本。

### 分支策略（3.x）

- **Bug 修复** → 当前稳定分支（例如 `3.x`）
- **小型且兼容的功能** → 同一稳定分支
- **破坏性或重大变更** → `master` / 下一版本分支

### 代码规范

- 代码风格遵循 [PSR-12](https://www.php-fig.org/psr/psr-12/)
- 自动加载遵循 [PSR-4](https://www.php-fig.org/psr/psr-4/)
- PHP 8.1+
- 提交信息使用清晰的祈使句（例如 `Fix route validation for missing actions`）

---

## 安全

请**私下**报告安全漏洞：

`security@pinoox.com`

---

## 联系方式

- 支持：`support@pinoox.com`
- [GitHub 仓库](https://github.com/pinoox/pinoox)

---

## 相关文档

- [什么是 Pinoox？](./what-is-pinoox.md)
- [Pinoox 功能特性](./features-pinoox.md)

---

[← 返回索引](../README.md)

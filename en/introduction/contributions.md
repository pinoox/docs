# Contributing to Pinoox

[← Back to index](../README.md)

Pinoox is an open-source project. Your contributions — from bug reports to pull requests — help improve the framework and its documentation.

---

## Ways to contribute

| Type | Description |
|------|-------------|
| Bug report | GitHub Issue with steps to reproduce |
| Feature request | Issue describing the use case |
| Pull Request | Bug fix or feature in the appropriate repository |
| Documentation | Improve files under `docs/` (Persian or English) |
| Open-source app | Publish a Pinoox app for the community |

---

## Reporting bugs

When opening an Issue, include:

1. **Title** — a short summary of the problem
2. **Steps to reproduce** — step by step
3. **Expected behavior** vs **actual behavior**
4. **Environment** — PHP version, Pinoox/pincore version, operating system
5. **Sample code** — when possible

[Pinoox GitHub Issues](https://github.com/pinoox/pinoox/issues)

---

## Pull requests

### Repositories

- **pinoox/pinoox** — sample project, system apps, launcher
- **pinoox/pincore** — framework core (`vendor/pinoox/pincore/`)

Send core changes to pincore, not only to the local `vendor/` copy in your project.

### Branch strategy (3.x)

- **Bug fixes** → current stable branch (e.g. `3.x`)
- **Small, compatible features** → same stable branch
- **Breaking or major changes** → `master` / next-version branch

### Code standards

- [PSR-12](https://www.php-fig.org/psr/psr-12/) for code style
- [PSR-4](https://www.php-fig.org/psr/psr-4/) for autoloading
- PHP 8.1+
- Clear, imperative commit messages (e.g. `Fix route validation for missing actions`)

---

## Security

Report security vulnerabilities **privately**:

`security@pinoox.com`

---

## Contact

- Support: `support@pinoox.com`
- [GitHub repository](https://github.com/pinoox/pinoox)

---

## Related docs

- [What is Pinoox?](./what-is-pinoox.md)
- [Pinoox features](./features-pinoox.md)

---

[← Back to index](../README.md)

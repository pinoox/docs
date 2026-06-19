# Contact Support

[← Back to index](../README.md)

If you still have a blocker after reviewing [Common issues](./common-issues.md), use the official channels below. Before contacting support, prepare your Pinoox version, PHP version, error message, and reproduction steps.

---

## General support

**Email:** [support@pinoox.com](mailto:support@pinoox.com)

Suitable for:

- Installation and deployment questions
- Unexpected framework behavior
- HMVC and app architecture guidance

Include in your email:

1. Pinoox version (`composer.json` → `version` or git tag)
2. PHP version (`php -v`)
3. OS and web server (Apache/nginx, MAMP, cPanel, …)
4. Full error text or screenshot
5. Minimal reproduction steps

---

## GitHub Issues

For confirmed bugs, feature requests, and public technical discussion:

**Repository:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Before opening a new issue:

- Search for duplicate issues
- Test on the latest stable/beta release
- If related to `pincore`, also check the `pinoox/pincore` package

Suggested issue template:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.2.x
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

## Security reports

**Email:** [security@pinoox.com](mailto:security@pinoox.com)

**Only** for security vulnerabilities — SQL injection, auth bypass, RCE, secret exposure.

- Do not publish details publicly (GitHub issue) until a patch is ready
- When possible, include a minimal PoC and impact description

---

## Contributing code

For PRs and framework development:

- [Contributing](../introduction/contributions.md)
- Fork → branch → test (`php pinoox test`) → Pull Request

---

## Self-help resources

| Topic | Doc |
|-------|-----|
| Installation | [installing-pinoox.md](../start/installing-pinoox.md) |
| First app | [your-first-app.md](../start/your-first-app.md) |
| Common issues | [common-issues.md](./common-issues.md) |
| Testing | [getting-started.md](../test/getting-started.md) |

**Website:** [pinoox.com](https://www.pinoox.com/)

---

## Related docs

- [Common issues](./common-issues.md)
- [What is Pinoox?](../introduction/what-is-pinoox.md)
- [Contributing](../introduction/contributions.md)
- [Installing Pinoox](../start/installing-pinoox.md)

---

[← Back to index](../README.md)

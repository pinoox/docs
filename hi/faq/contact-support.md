# Contact Support

[← इंडेक्स पर वापस जाएँ](../README.md)

[Common issues](./common-issues.md) review करने के बाद भी blocker रहे तो नीचे official channels उपयोग करें। Support से contact करने से पहले Pinoox version, PHP version, error message, और reproduction steps तैयार रखें।

---

## General support

**Email:** [support@pinoox.com](mailto:support@pinoox.com)

Suitable for:

- Installation and deployment questions
- Unexpected framework behavior
- HMVC and app architecture guidance

Email में शामिल करें:

1. Pinoox version (`composer.json` → `version` or git tag)
2. PHP version (`php -v`)
3. OS and web server (Apache/nginx, MAMP, cPanel, …)
4. Full error text or screenshot
5. Minimal reproduction steps

---

## GitHub Issues

Confirmed bugs, feature requests, और public technical discussion के लिए:

**Repository:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

नया issue खोलने से पहले:

- Duplicate issues search करें
- Latest stable/beta release पर test करें
- `pincore` related हो तो `pinoox/pincore` package भी check करें

Suggested issue template:

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

## Security reports

**Email:** [security@pinoox.com](mailto:security@pinoox.com)

**केवल** security vulnerabilities — SQL injection, auth bypass, RCE, secret exposure.

- Patch ready होने तक details publicly (GitHub issue) publish न करें
- जहाँ संभव minimal PoC और impact description शामिल करें

---

## Contributing code

PRs और framework development के लिए:

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

## संबंधित docs

- [Common issues](./common-issues.md)
- [What is Pinoox?](../introduction/what-is-pinoox.md)
- [Contributing](../introduction/contributions.md)
- [Installing Pinoox](../start/installing-pinoox.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

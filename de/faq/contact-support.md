# Support kontaktieren

[← Zurück zum Index](../README.md)

Wenn Sie nach [Häufige Probleme](./common-issues.md) weiterhin blockiert sind, nutzen Sie die offiziellen Kanäle unten. Bereiten Sie vor dem Kontakt Ihre Pinoox-Version, PHP-Version, Fehlermeldung und Reproduktionsschritte vor.

---

## Allgemeiner Support

**E-Mail:** [support@pinoox.com](mailto:support@pinoox.com)

Geeignet für:

- Fragen zu Installation und Deployment
- Unerwartetes Framework-Verhalten
- HMVC- und App-Architektur-Beratung

In der E-Mail angeben:

1. Pinoox-Version (`composer.json` → `version` oder Git-Tag)
2. PHP-Version (`php -v`)
3. Betriebssystem und Webserver (Apache/nginx, MAMP, cPanel, …)
4. Vollständiger Fehlertext oder Screenshot
5. Minimale Reproduktionsschritte

---

## GitHub Issues

Für bestätigte Bugs, Feature-Requests und öffentliche technische Diskussion:

**Repository:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Vor dem Öffnen eines neuen Issues:

- Nach doppelten Issues suchen
- Auf der neuesten stabilen/beta-Version testen
- Bei Bezug zu `pincore` auch das Paket `pinoox/pincore` prüfen

Vorgeschlagenes Issue-Template:

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

## Sicherheitsmeldungen

**E-Mail:** [security@pinoox.com](mailto:security@pinoox.com)

**Nur** für Sicherheitslücken — SQL-Injection, Auth-Bypass, RCE, Geheimnis-Exposition.

- Details nicht öffentlich veröffentlichen (GitHub-Issue), bis ein Patch bereit ist
- Wenn möglich minimales PoC und Impact-Beschreibung beifügen

---

## Code beitragen

Für PRs und Framework-Entwicklung:

- [Mitwirken](../introduction/contributions.md)
- Fork → Branch → Test (`php pinoox test`) → Pull Request

---

## Selbsthilfe-Ressourcen

| Thema | Dokumentation |
|-------|-----|
| Installation | [installing-pinoox.md](../start/installing-pinoox.md) |
| Erste App | [your-first-app.md](../start/your-first-app.md) |
| Häufige Probleme | [common-issues.md](./common-issues.md) |
| Testen | [getting-started.md](../test/getting-started.md) |

**Website:** [pinoox.com](https://www.pinoox.com/)

---

## Verwandte Dokumentation

- [Häufige Probleme](./common-issues.md)
- [Was ist Pinoox?](../introduction/what-is-pinoox.md)
- [Mitwirken](../introduction/contributions.md)
- [Pinoox installieren](../start/installing-pinoox.md)

---

[← Zurück zum Index](../README.md)

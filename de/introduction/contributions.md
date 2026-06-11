# Zu Pinoox beitragen

[← Zurück zur Übersicht](../README.md)

Pinoox ist ein Open-Source-Projekt. Ihre Beiträge — von Bug-Reports bis zu Pull Requests — helfen, das Framework und seine Dokumentation zu verbessern.

---

## Möglichkeiten beizutragen

| Art | Beschreibung |
|------|-------------|
| Bug-Report | GitHub-Issue mit Schritten zur Reproduktion |
| Feature-Request | Issue, das den Anwendungsfall beschreibt |
| Pull Request | Bugfix oder Feature im passenden Repository |
| Dokumentation | Dateien unter `docs/` verbessern (Persisch oder Englisch) |
| Open-Source-App | Eine Pinoox-App für die Community veröffentlichen |

---

## Bugs melden

Geben Sie beim Öffnen eines Issues Folgendes an:

1. **Titel** — eine kurze Zusammenfassung des Problems
2. **Schritte zur Reproduktion** — Schritt für Schritt
3. **Erwartetes Verhalten** vs. **tatsächliches Verhalten**
4. **Umgebung** — PHP-Version, Pinoox/pincore-Version, Betriebssystem
5. **Beispielcode** — wenn möglich

[Pinoox GitHub Issues](https://github.com/pinoox/pinoox/issues)

---

## Pull Requests

### Repositories

- **pinoox/pinoox** — Beispielprojekt, System-Apps, Launcher
- **pinoox/pincore** — Framework-Kern (`vendor/pinoox/pincore/`)

Senden Sie Core-Änderungen an pincore, nicht nur an die lokale `vendor/`-Kopie in Ihrem Projekt.

### Branch-Strategie (3.x)

- **Bugfixes** → aktueller stabiler Branch (z. B. `3.x`)
- **Kleine, kompatible Features** → derselbe stabile Branch
- **Breaking Changes oder größere Änderungen** → `master` / Branch der nächsten Version

### Code-Standards

- [PSR-12](https://www.php-fig.org/psr/psr-12/) für den Code-Stil
- [PSR-4](https://www.php-fig.org/psr/psr-4/) für das Autoloading
- PHP 8.1+
- Klare Commit-Nachrichten im Imperativ (z. B. `Fix route validation for missing actions`)

---

## Sicherheit

Melden Sie Sicherheitslücken **vertraulich**:

`security@pinoox.com`

---

## Kontakt

- Support: `support@pinoox.com`
- [GitHub-Repository](https://github.com/pinoox/pinoox)

---

## Verwandte Dokumente

- [Was ist Pinoox?](./what-is-pinoox.md)
- [Pinoox-Features](./features-pinoox.md)

---

[← Zurück zur Übersicht](../README.md)

# Contribuer à Pinoox

[← Retour à l'index](../README.md)

Pinoox est un projet à code source ouvert. Vos contributions — des rapports de bugs aux pull requests — aident à améliorer le framework et sa documentation.

---

## Façons de contribuer

| Type | Description |
|------|-------------|
| Rapport de bug | Issue GitHub avec les étapes pour reproduire |
| Demande de fonctionnalité | Issue décrivant le cas d'usage |
| Pull Request | Correction de bug ou fonctionnalité dans le dépôt approprié |
| Documentation | Améliorer les fichiers sous `docs/` (persan ou anglais) |
| App à code source ouvert | Publier une app Pinoox pour la communauté |

---

## Signaler des bugs

Lors de l'ouverture d'une Issue, incluez :

1. **Titre** — un court résumé du problème
2. **Étapes pour reproduire** — pas à pas
3. **Comportement attendu** vs **comportement réel**
4. **Environnement** — version de PHP, version de Pinoox/pincore, système d'exploitation
5. **Code d'exemple** — si possible

[Issues GitHub de Pinoox](https://github.com/pinoox/pinoox/issues)

---

## Pull requests

### Dépôts

- **pinoox/pinoox** — projet d'exemple, apps système, lanceur
- **pinoox/pincore** — cœur du framework (`vendor/pinoox/pincore/`)

Envoyez les changements du cœur vers pincore, pas seulement vers la copie locale `vendor/` de votre projet.

### Stratégie de branches (3.x)

- **Corrections de bugs** → branche stable courante (ex. `3.x`)
- **Petites fonctionnalités compatibles** → même branche stable
- **Changements majeurs ou incompatibles** → `master` / branche de la prochaine version

### Normes de code

- [PSR-12](https://www.php-fig.org/psr/psr-12/) pour le style de code
- [PSR-4](https://www.php-fig.org/psr/psr-4/) pour l'autoloading
- PHP 8.1+
- Messages de commit clairs et impératifs (ex. `Fix route validation for missing actions`)

---

## Sécurité

Signalez les vulnérabilités de sécurité **en privé** :

`security@pinoox.com`

---

## Contact

- Support : `support@pinoox.com`
- [Dépôt GitHub](https://github.com/pinoox/pinoox)

---

## Documentation associée

- [Qu'est-ce que Pinoox ?](./what-is-pinoox.md)
- [Fonctionnalités de Pinoox](./features-pinoox.md)

---

[← Retour à l'index](../README.md)

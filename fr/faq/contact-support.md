# Contacter le support

[← Retour à l'index](../README.md)

Si vous avez encore un blocage après avoir consulté [Problèmes courants](./common-issues.md), utilisez les canaux officiels ci-dessous. Avant de contacter le support, préparez votre version Pinoox, version PHP, message d'erreur et étapes de reproduction.

---

## Support général

**E-mail :** [support@pinoox.com](mailto:support@pinoox.com)

Adapté pour :

- Questions d'installation et de déploiement
- Comportement inattendu du framework
- Conseils HMVC et architecture d'app

Incluez dans votre e-mail :

1. Version Pinoox (`composer.json` → `version` ou tag git)
2. Version PHP (`php -v`)
3. OS et serveur web (Apache/nginx, MAMP, cPanel, …)
4. Texte d'erreur complet ou capture d'écran
5. Étapes minimales de reproduction

---

## GitHub Issues

Pour les bugs confirmés, demandes de fonctionnalités et discussion technique publique :

**Dépôt :** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Avant d'ouvrir une nouvelle issue :

- Recherchez les doublons
- Testez sur la dernière version stable/beta
- Si lié à `pincore`, vérifiez aussi le package `pinoox/pincore`

Modèle d'issue suggéré :

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

## Signalements de sécurité

**E-mail :** [security@pinoox.com](mailto:security@pinoox.com)

**Uniquement** pour les vulnérabilités de sécurité — injection SQL, contournement d'auth, RCE, exposition de secrets.

- Ne publiez pas les détails publiquement (issue GitHub) tant qu'un correctif n'est pas prêt
- Si possible, incluez un PoC minimal et une description de l'impact

---

## Contribuer au code

Pour les PR et le développement du framework :

- [Contributions](../introduction/contributions.md)
- Fork → branche → test (`php pinoox test`) → Pull Request

---

## Ressources d'auto-assistance

| Sujet | Doc |
|-------|-----|
| Installation | [installing-pinoox.md](../start/installing-pinoox.md) |
| Première app | [your-first-app.md](../start/your-first-app.md) |
| Problèmes courants | [common-issues.md](./common-issues.md) |
| Tests | [getting-started.md](../test/getting-started.md) |

**Site web :** [pinoox.com](https://www.pinoox.com/)

---

## Documentation associée

- [Problèmes courants](./common-issues.md)
- [Qu'est-ce que Pinoox ?](../introduction/what-is-pinoox.md)
- [Contributions](../introduction/contributions.md)
- [Installer Pinoox](../start/installing-pinoox.md)

---

[← Retour à l'index](../README.md)

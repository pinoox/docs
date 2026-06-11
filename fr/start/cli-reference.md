# Référence CLI Pinoox

[← Retour à l'index](../README.md)

Exécutez chaque commande depuis la **racine du projet** :

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Lorsqu'un paquet est requis et omis, Pinoox affiche un sélecteur interactif.

> Pour les projets **mono-app**, utilisez la [CLI Pinx](./pinx-cli.md) autonome (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Alias courants

| Alias | Commande |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## Apps

| Commande | Rôle |
|---------|---------|
| `app:create {package}` | Scaffolder une app (`--simple`, `--stack`, `--profile`) |
| `app:list` | Lister les apps |
| `app:delete` | Supprimer une app |
| `app:router set /path {package}` | Mapping URL |
| `app:domain` | Carte hôte → app |
| `app:resolve` | Déboguer l'app active |

---

## Scaffolding

| Commande | Sortie |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | Classe FormRequest |
| `seeder:create` | `database/seed/` |
| `test:create` | Fichier Pest |
| `theme:frontend` | Outils frontend (Vue/React/Twig) |

---

## Base de données

| Commande | Rôle |
|---------|---------|
| `migrate {package}` | Exécuter les migrations (app, `platform`, `pincore`) |
| `migrate:create` | Nouveau fichier de migration |
| `migrate:status` / `migrate:rollback` | Statut / rollback |
| `seeder:run` | Exécuter les seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patchs](../database/patches.md) |
| `query` | SQL brut (debug) |

---

## Cache et Pinker

| Commande | Rôle |
|---------|---------|
| `cache:build` / `cache:clear` | Cache d'exécution |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Réinitialiser Pinker + config |

---

## Schedule

| Commande | Rôle |
|---------|---------|
| `schedule:list` | Lister les tâches cron |
| `schedule:run` | Exécuter les tâches dues |

Voir [Schedule](../advanced/schedule.md).

---

## Router

| Commande | Rôle |
|---------|---------|
| `route:actions {package}` | Lister les Named Actions |

---

## Empaquetage Pinx

| Commande | Rôle |
|---------|---------|
| `pinx:build` | Construire un paquet `.pinx` |
| `pinx:install` | Installer un paquet |
| `pinx:info` | Métadonnées |
| `wizard:list` / `wizard:install` | Assistant d'installation |

---

## Développement

| Commande | Rôle |
|---------|---------|
| `test` | Tests Pest |
| `serve` | Serveur de dev intégré |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm sur les apps |
| `version` / `mode:show` | Version / mode d'exécution |

---

## Argument package

| Valeur | Signification |
|-------|---------|
| `com_my_shop` | App spécifique |
| `platform` | Migrations/patchs/seeders plateforme |
| `pincore` | Cœur du framework |
| `all` | Toutes les apps (cache/pinker) |

---

## Documentation associée

- [Votre première app](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patchs](../database/patches.md)

---

[← Retour à l'index](../README.md)

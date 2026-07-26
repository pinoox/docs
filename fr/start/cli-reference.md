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
| `users` | `user:list` |

| `roles` | `role:list` |

| `permissions` | `permission:list` |

| `tokens` | `token:list` |

| `files` | `file:list` |
| `pinion` | `pinion:list` |

| `databases` | `db:list` |

| `make:permission` | `permission:create` |

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
| `seeder:create` | `database/seeders/` |
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
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patchs](../advanced/patches.md) |
| `query` | SQL brut (debug) |


### Connection management (`db:*`)



Inspect and persist platform connections (Pinker `~database`) and per-app `database` blocks.



| Command | Purpose |

|---------|---------|

| `db:list` | List platform connections or app DB settings (`--all`, `--test`, `--json`) |

| `db:show {target}` | Connection details for `platform`, a connection name, or an app package |

| `db:test {target}` | Test connectivity; ad-hoc probe with `--host`, `--database`, `--username`, … |

| `db:create {name}` | Add a platform connection (interactive or `--set key=value`) |

| `db:update {target}` | Update platform or app database settings |

| `db:prefix {package} {prefix}` | Change app table prefix (`--use` to pick platform connection) |



```bash

php pinoox db:list --test

php pinoox db:show platform

php pinoox db:show com_my_shop --json

php pinoox db:test mysql

php pinoox db:prefix com_my_shop shop_

```



> CLI writes to **Pinker**. Runtime may still override values when `.env` defines `DB_*` keys (`env-over-pinker`).



See [Database getting started](../database/getting-started.md).



---



## Users, roles & permissions



Commands respect `transport.user` / access scope (usually `platform`). Omit `{package}` to pick from the interactive list.



| Command | Purpose |

|---------|---------|

| `user:list` / `user:show` / `user:create` / `user:update` / `user:delete` | User CRUD |

| `user:password` / `user:status` / `user:role` | Password, status, role assignment |

| `role:list` / `role:create` / `role:show` / `role:update` / `role:delete` | Role CRUD |

| `role:permission` | Attach or detach permissions on a role |

| `permission:list` / `permission:create` / `permission:show` / `permission:delete` | Permission CRUD |



```bash

php pinoox user:list com_my_shop --status=active --json

php pinoox role:create com_my_shop --key=editor --name=Editor

php pinoox permission:create com_my_shop blog.posts.edit

php pinoox role:permission editor --attach=blog.posts.edit

```



See [User management](../advanced/user-management.md) and [Access & permissions](../advanced/access-permissions.md).



---



## Tokens



Manage `TokenModel` rows for the transport scope (`transport.session_token` in `app.php`).



| Command | Purpose |

|---------|---------|

| `token:list` / `token:show` | Inspect tokens (keys masked in list output) |

| `token:create` | Create token for a user (`--user`, `--lifetime`, `--unit`) |

| `token:update` / `token:delete` | Update metadata or remove one token |

| `token:revoke-user` | Revoke all tokens for a user (like `Auth::revokeSessions`) |

| `token:purge` | Delete expired tokens |



```bash

php pinoox token:list platform

php pinoox token:create com_my_shop --user=1 --lifetime=30 --unit=day

php pinoox token:revoke-user 1

```



See [Token management](../advanced/token-management.md).



---



## Files



Manage upload metadata and storage for the `FileModel` scope (`transport.file_storage`).



| Command | Purpose |

|---------|---------|

| `file:list` / `file:show` | List or inspect records (shows storage `present` / `missing`) |

| `file:update` | Update metadata, access, or links |

| `file:delete` | Remove DB row, storage, or both (`--db-only`, `--storage-only`, `--force`) |

| `file:purge` | Bulk cleanup of orphaned or old files |



```bash

php pinoox file:list com_my_shop

php pinoox file:show 12

php pinoox file:delete 12 --storage-only --force

```



See [File management](../advanced/file-management.md).

---

## Pinion (téléversements reprise)

Gérer les sessions de téléversement par morceaux en cours (stockage temporaire sous `storage/pinion`) :

| Commande | Rôle |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

Voir [protocole Pinion](../advanced/pinion.md).

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
- [Patchs](../advanced/patches.md)

---

[← Retour à l'index](../README.md)

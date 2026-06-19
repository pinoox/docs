# CLI Pinx (projets mono-app)

[← Retour à l'index](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** est la CLI développeur pour les projets Pinoox **mono-app** — scaffolder, exécuter, migrer, builder et publier des paquets `.pinx` sans toucher à un gestionnaire multi-app.

Elle repose sur `pinoox/pincore` et le template `pinoox/app`. La racine de votre projet **est** l'app : un `app.php`, un paquet, un flux de travail.

> Pour les installations plateforme multi-app classiques, utilisez plutôt [`php pinoox`](./cli-reference.md).

---

## Démarrage rapide

Installez Pinx une fois, créez une nouvelle app, lancez-la :

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggère com_my_shop — confirmez ou modifiez dans l'assistant
cd my-shop
cp .env.example .env          # définissez DB_* si vous utilisez une base de données
pinx setup                    # migrate platform + app, exécute les seeders
pinx dev                      # http://127.0.0.1:8000
```

Ajoutez le `bin` global de Composer à votre `PATH` si `pinx` est introuvable :

- Linux / macOS : `~/.composer/vendor/bin` ou `~/.config/composer/vendor/bin`
- Windows : `%APPDATA%\Composer\vendor\bin`

| Étape | Rôle |
|------|--------------|
| `composer global require` | Installe la commande `pinx` sur votre machine |
| `pinx new my-shop` | Scaffolde depuis `pinoox/app` ; l'assistant suggère un paquet en 3 parties (ex. `com_my_shop`) |
| `.env` | Base de données et chemins du projet — copiez depuis `.env.example` |
| `pinx setup` | En une fois : migrations plateforme → migrations app → seeders |
| `pinx dev` | Serveur de dev PHP ; démarre aussi Vite lorsqu'une stack frontend est configurée |

Les noms de paquet suivent `com_{vendor}_{name}` — ex. `com_acme_shop`, `ir_yekdo_app`. Déjà dans un dossier vide ? Utilisez `pinx init` au lieu de `pinx new`.

**Vérification optionnelle avant `setup` :** `pinx doctor` rapporte PHP, layout, env, DB et préparation au build.

---

## Alternative : `composer create-project`

Pas d'installation globale — le template inclut `bin/pinx` dans le projet :

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## Ce qui différencie le mono-app

Les installations Pinoox classiques gardent plusieurs apps sous `apps/` et en choisissent une à l'exécution. Le **mono-app** aplatit cela :

- `app.php` à la racine du projet contient l'identité du paquet et les paramètres pinx
- `Controller/`, `Model/`, `routes/`, `theme/` vivent à la racine — pas dans `apps/{package}/`
- `platform/` contient le routage local et la config du launcher (exclu des builds `.pinx`)
- Pinx cible toujours **votre** app — pas de sélecteur de paquet, pas d'UI manager

```
my-shop/                    ← racine projet = racine app
├── app.php                 ← paquet, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← couche dev host + deploy (local uniquement)
├── bin/pinx                ← entrée CLI locale au projet
└── vendor/pinoox/pincore   ← framework
```

---

## Options d'installation

| Où | Comment | Quand l'utiliser |
|-------|-----|-------------|
| **Global** | `composer global require pinoox/pinx-cli` | Recommandé — `pinx new` et `pinx init` depuis n'importe où |
| **Par projet** | Fourni comme `bin/pinx` dans `pinoox/app` | Après `composer create-project` — pas d'install global nécessaire |

```bash
pinx -v          # version CLI (ex. pinx-cli 1.1.7)
pinx list        # aperçu des commandes par groupe
pinx help setup  # détail pour une commande
```

---

## Flux de travail quotidien

```bash
pinx dev                    # serveur local (+ Vite quand app.php → frontend.stack est défini)
pinx dev --open             # ouvre le navigateur après le démarrage
pinx dev --no-frontend      # PHP uniquement

pinx migrate                # exécute les migrations app (--platform exécute la plateforme d'abord)
pinx migrate:st             # statut des migrations
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # liste les named actions (--validate, --json)
pinx test                   # exécute les tests app (Pest)
```

**Frontend** (lorsque `theme/` utilise Vue/React + Vite) :

```bash
pinx fe:info                # stack, scripts npm, chemins
pinx fe:i                   # npm install
pinx fe:d                   # serveur dev Vite
pinx fe:b                   # build de production
pinx fe:sc --stack=vue      # scaffolde les fichiers de départ
```

**Dépendances :**

```bash
pinx deps:st                # statut Composer + npm
pinx deps:i                 # installe tout
pinx deps:up                # met à jour tout
```

**Pinker** (cache de build) :

```bash
pinx pinker:st              # cache vs source
pinx pinker:rb              # rebuild
pinx pinker:df              # diff
```

---

## Déployer en production

Construisez un paquet `.pinx` pour installation sur une plateforme Pinoox complète (Manager → Applications) :

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # incrémente la version dans app.php + build
pinx release --sign         # signe lorsque la clé est configurée dans app.php → pinx.sign
```

`pinx build` applique des défauts sensés (exclut `vendor/`, `bin/`, `.env`, `platform/`, outillage dev). Surchargez dans `app.php` uniquement si nécessaire :

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor exécute un diagnostic structuré et suggère des commandes de correction en cas d'échec :

| Groupe | Vérifications |
|-------|--------|
| **Project** | `app.php`, identité du paquet, layout `platform/` |
| **Runtime** | Version PHP (≥ 8.2), extensions, chemins inscriptibles |
| **Dependencies** | Vendor Composer, Node/npm optionnel |
| **Environment** | Présence de `.env` et variables clés |
| **Database** | Connexion (ignorable avec `--skip-db`) |
| **Frontend** | Stack du thème, `package.json` (ignorable avec `--skip-frontend`) |
| **Build** | Préparation export, icône, champs version |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # rapport adapté CI
pinx doctor --no-fixes      # masque les commandes suggérées
```

---

## Référence des commandes

Exécutez `pinx list` pour un aperçu par section. Les alias courts apparaissent entre crochets.

### Project

| Commande | Alias | Description |
|---------|---------|-------------|
| `new` | — | Scaffolde depuis `pinoox/app` (assistant ou flags) |
| `init` | — | Initialise le répertoire courant (`--force` pour écraser) |
| `setup` | — | DB : migrate platform + app, puis seed |
| `doctor` | `dr` | Contrôle de santé — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | Affiche les métadonnées depuis `app.php` |

### Development

| Commande | Description |
|---------|-------------|
| `dev` | Serveur de dev ; Vite quand `frontend.stack` est vue/react |

### Database

| Commande | Alias | Description |
|---------|---------|-------------|
| `migrate:run` | `migrate` | Exécute les migrations app (`--platform` exécute la plateforme d'abord) |
| `migrate:status` | `migrate:st` | Statut des migrations |
| `migrate:rollback` | `migrate:rb` | Rollback du dernier lot (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Crée un fichier de migration |
| `migrate:platform` | `migrate:pl` | Migrations plateforme uniquement |
| `seeder:run` | `seed` | Exécute les seeders (`-c` class) |

### Patches

| Commande | Alias | Description |
|---------|---------|-------------|
| `patch:run` | `patch` | Exécute les patchs en attente |
| `patch:status` | `patch:st` | Statut des patchs |
| `patch:rollback` | `patch:rb` | Rollback du dernier lot de patchs |

### Build & release

| Commande | Alias | Description |
|---------|---------|-------------|
| `build` | `bld` | Construit un paquet `.pinx` |
| `release` | `rel` | Incrément de version + build (`--bump`, `--sign`) |

### Scaffolding

| Commande | Alias | Description |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Routes

| Commande | Description |
|---------|-------------|
| `route:actions` / `routes` | Liste les named actions (`--validate`, `--json`) |

### Dependencies

| Commande | Alias | Description |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Statut Composer + npm |
| `deps:install` | `deps:i` | Installe les dépendances |
| `deps:update` | `deps:up` | Met à jour les dépendances |

### Frontend

| Commande | Alias | Description |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Stack du thème et scripts npm |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Build de production |
| `fe:dev` | `fe:d` | Serveur dev Vite |
| `fe:scaffold` | `fe:sc` | Fichiers de départ (`--stack=vue\|react\|twig`) |

### Schedule

| Commande | Alias | Description |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | Liste les tâches cron depuis `schedule.php` |
| `schedule:run` | `sched:run` | Exécute les tâches dues (`--dry-run`) |

### Pinion (téléversements reprise)

Transmis à `php pinoox pinion:*` — gérer les sessions temporaires de téléversement par morceaux.

| Commande | Description |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

Voir [protocole Pinion](../advanced/pinion.md).

### Pinker

| Commande | Alias | Description |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Cache vs source |
| `pinker:rebuild` | `pinker:rb` | Rebuild du cache |
| `pinker:diff` | `pinker:df` | Affiche les différences |
| `pinker:clear` | `pinker:cl` | Vide le cache |
| `pinker:overrides` | `pinker:ov` | Liste les surcharges |

### Quality & docs

| Commande | Description |
|---------|-------------|
| `test` / `pest` | Exécute les tests app (`--unit`, `--feature`) |
| `api:docs` | Documentation API REST |
| `graphql:docs` | Documentation schéma GraphQL |

### Meta

| Commande | Alias | Description |
|---------|---------|-------------|
| `list` | — | Aperçu des commandes par groupe |
| `version` | `ver` | Version CLI |

---

## Détection de l'app

Pinx remonte depuis le répertoire de travail courant jusqu'à trouver un projet mono-app valide :

1. `app.php` existe et renvoie un tableau avec une clé `package` non vide
2. `pinoox/pincore` est requis dans `composer.json`, ou `vendor/pinoox/pincore` est présent

Surchargez le paquet détecté avec des variables d'environnement :

| Variable | Rôle |
|----------|---------|
| `PINX_PACKAGE` | Force le paquet cible de la CLI |
| `PINOOX_DEV_APP` | Alias pour `PINX_PACKAGE` |
| `PINX_DEV=1` | Mode dev (défini automatiquement par pinx lors de la délégation à pincore) |

---

## Prérequis

- **PHP** ≥ 8.2 avec les extensions requises par `pinoox/pincore`
- **Composer** 2.x
- **Node.js** + npm — uniquement avec les frontends Vite/Vue/React
- **Base de données** — MySQL/MariaDB ou ce que configure votre `.env` (optionnel pour apps statiques/Twig uniquement)

---

## Documentation associée

- [Installer Pinoox](./installing-pinoox.md)
- [Référence CLI Pinoox (multi-app)](./cli-reference.md)
- [Votre première app](./your-first-app.md)
- [Manifeste app.php](./app-manifest.md)

---

[← Retour à l'index](../README.md)

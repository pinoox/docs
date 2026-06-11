# Installer Pinoox

[← Retour à l'index](../README.md)

Ce guide couvre l'installation de Pinoox 3.x. Il existe deux façons de commencer :

| Voie | Idéale pour |
|-------|----------|
| **A. App unique avec [Pinx CLI](./pinx-cli.md)** | Construire une seule app — démarrage le plus rapide, sans interface de gestion |
| **B. Plateforme complète (classique)** | Héberger plusieurs apps avec l'installateur graphique et le gestionnaire |

---

## Prérequis

| Outil | Version |
|------|---------|
| PHP | 8.1 ou supérieur (avec ext-mysqli, ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (optionnel) | 18+ — uniquement pour les builds de thème frontend |

---

## Voie A — App unique avec Pinx CLI

Installez la [Pinx CLI](./pinx-cli.md) une seule fois, créez une nouvelle app, lancez-la :

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggère com_my_shop — confirmez ou modifiez dans l'assistant
cd my-shop
cp .env.example .env          # définissez les DB_* si vous utilisez une base de données
pinx setup                    # migre la plateforme + l'app, exécute les seeders
pinx dev                      # http://127.0.0.1:8000
```

Ou sans installation globale, via le modèle de projet :

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

Exécutez `pinx doctor` à tout moment pour vérifier PHP, l'environnement, la base de données et l'état de préparation du build. Consultez le [guide complet de la Pinx CLI](./pinx-cli.md) pour le flux de travail quotidien et la référence des commandes.

---

## Voie B — Plateforme complète (classique)

### 1. Récupérer le projet

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Vous pouvez aussi télécharger la dernière version depuis [GitHub](https://github.com/pinoox/pinoox), l'extraire, puis exécuter `composer install`.

---

### 2. Le placer dans votre serveur web

Placez le dossier du projet dans votre racine de documents (document root) :

| Environnement | Chemin d'exemple |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Définissez la racine de documents sur la **racine du projet** (le dossier qui contient `index.php`) — pas sur un sous-dossier `public`.

---

### 3. Créer la base de données

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Lancer l'installateur

Ouvrez votre navigateur :

```
http://localhost/pinoox
```

L'app système `com_pinoox_installer` s'exécute. Les étapes de l'interface graphique sont :

1. Vérification des prérequis PHP
2. Acceptation du contrat de licence
3. Saisie des identifiants de la base de données
4. Création du compte administrateur
5. Fin de l'installation

---

### 5. Après l'installation

Disposition principale :

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← apps
├── vendor/pinoox/pincore/  ← cœur
└── config/             ← configuration du projet
```

Créez votre première app :

```bash
php pinoox app:create com_acme_blog
```

---

## Dépannage rapide

| Problème | Solution |
|---------|-----|
| Page blanche | Exécutez `composer install` et consultez les journaux d'erreurs PHP |
| 404 sur les sous-routes | Activez mod_rewrite / `.htaccess` |
| Erreur d'extension manquante | Activez ext-mysqli et ext-zip dans php.ini |
| L'installateur ne s'ouvre pas | Vérifiez la racine de documents et les permissions d'écriture sur les dossiers d'exécution |

---

## Documentation associée

- [Pinx CLI (app unique)](./pinx-cli.md)
- [Votre première app](./your-first-app.md)
- [Structure du projet](./structure.md)
- [Qu'est-ce que Pinoox ?](../introduction/what-is-pinoox.md)

---

[← Retour à l'index](../README.md)

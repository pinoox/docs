# Documentation Pinoox

Documentation officielle pour les développeurs qui créent des applications sur la plateforme Pinoox (PHP 8.1+, architecture HMVC).

Chaque guide décrit **une approche recommandée** avec des exemples pratiques. Choisissez une section ci-dessous ou parcourez par thème.

**Langues ::** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](./README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### Introduction

#### [Qu'est-ce que Pinoox ?](./introduction/what-is-pinoox.md)
#### [Fonctionnalités de Pinoox](./introduction/features-pinoox.md)
#### [Contribuer à Pinoox](./introduction/contributions.md)

### Premiers pas

#### [Installer Pinoox](./start/installing-pinoox.md)
#### [Votre première app](./start/your-first-app.md)
#### [Structure du projet](./start/structure.md)
#### [Référence CLI Pinoox](./start/cli-reference.md)
#### [CLI Pinx (projets mono-app)](./start/pinx-cli.md)
#### [Référence du manifeste app.php](./start/app-manifest.md)

### Guides pratiques

#### [Guide pas à pas : app API Notes](./examples/simple-api-app.md)
#### [Tutoriel pas à pas : Application web de répertoire téléphonique](./examples/phonebook-app.md)
#### [Tutoriel pas à pas : Application de formulaire de contact](./examples/contact-form-app.md)
#### [Tutoriel pas à pas : Application de blog simple](./examples/blog-app.md)
#### [Guide pas à pas : tableau de tâches (Todo)](./examples/task-board-app.md)
#### [Tutoriel pas à pas : Application de galerie d'images](./examples/gallery-app.md)
#### [Guide pas à pas : panneau SPA Vue](./examples/vue-spa-app.md)
#### [Tutoriel pas à pas : Panneau SPA React](./examples/react-spa-app.md)
#### [Guide pas à pas : hybride Vite (Twig + widget JS)](./examples/vite-hybrid-app.md)

### Concepts fondamentaux

#### [Router](./basic/routers.md)
#### [Contrôleurs](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [Requête HTTP](./basic/requests.md)
#### [Réponse HTTP](./basic/responses.md)
#### [URL et construction de liens](./basic/url.md)
#### [Chemin de fichier](./basic/path.md)
#### [Validation](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Modèles Twig](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Langue et traduction](./basic/language.md)

### Sujets avancés

#### [Pinker et cache](./advanced/pinker.md)
#### [Services d'application (Component + Portal)](./advanced/services.md)
#### [Helpers globaux](./advanced/helpers.md)
#### [Envoi d'emails](./advanced/mail.md)
#### [Client HTTP](./advanced/http-client.md)
#### [Gestion des utilisateurs](./advanced/user-management.md)
#### [Gestion des fichiers](./advanced/file-management.md)
#### [Protocole Pinion](./advanced/pinion.md)
#### [Gestion des tokens](./advanced/token-management.md)
#### [Accès et permissions](./advanced/access-permissions.md)
#### [Transport (ressources partagées)](./advanced/transport.md)
#### [boot.php et événements](./advanced/boot-and-events.md)
#### [Planification (cron)](./advanced/schedule.md)

### Base de données

#### [Premiers pas avec la base de données](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Pagination](./database/pagination.md)
#### [Migrations](./database/migrations.md)
#### [Patchs (mises à jour de données)](./database/patches.md)

### Eloquent ORM

#### [Premiers pas avec Eloquent ORM](./eloquent-orm/getting-started.md)
#### [Relations Eloquent](./eloquent-orm/relationships.md)
#### [Collections Eloquent](./eloquent-orm/collections.md)
#### [Mutators et casts](./eloquent-orm/mutators-casts.md)
#### [Ressources API](./eloquent-orm/api-resources.md)
#### [Sérialisation de modèle](./eloquent-orm/serialization.md)
#### [Données de test — Seeders](./eloquent-orm/factories.md)

### Tests

#### [Premiers pas avec les tests dans Pinoox](./test/getting-started.md)
#### [Tests HTTP dans Pinoox](./test/http-tests.md)
#### [Tests console dans Pinoox](./test/console-tests.md)
#### [Tests navigateur (HTML) dans Pinoox](./test/browser-tests.md)
#### [Tests base de données dans Pinoox](./test/database.md)
#### [Tests de sérialisation dans Pinoox](./test/serialization.md)
#### [Mocking dans Pinoox](./test/mocking.md)

### FAQ

#### [Problèmes courants](./faq/common-issues.md)
#### [Contacter le support](./faq/contact-support.md)

---

### Code source
**Langues:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](./README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

Guides pas à pas pour créer de vraies applications — à utiliser après les bases, quand vous voulez du code concret.

---

### Comment lire cette documentation

1. Commencez par **Introduction** et **Premiers pas** si vous découvrez Pinoox.
2. Suivez les **Guides pratiques** — construisez une API JSON et un site simple étape par étape.
3. Lisez les **Concepts fondamentaux** pendant que vous créez routes, contrôleurs et vues.
4. Utilisez **Base de données** et **Eloquent ORM** quand vous ajoutez la persistance.
5. Consultez les **Sujets avancés** pour l'authentification, les fichiers, Pinker et les services partagés.
6. Utilisez **Tests** avant de déployer en production.

Tout le code applicatif se trouve sous `apps/{package}/`. Le noyau du framework est `vendor/pinoox/pincore/` — n'y placez pas la logique métier de votre application.

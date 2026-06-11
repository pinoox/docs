# Qu'est-ce que Pinoox ?

[← Retour à l'index](../README.md)

Pinoox est un framework PHP moderne à code source ouvert (3.x) construit sur l'architecture HMVC et le concept d'**app** (application). Il rend le développement web modulaire simple : chaque app est une unité MVC indépendante sous `apps/{package}/`, tandis que le cœur partagé du framework se trouve dans `vendor/pinoox/pincore/`.

---

## Architecture centrée sur les apps

Dans une seule installation Pinoox, plusieurs apps indépendantes fonctionnent côte à côte :

```
{project_root}/
├── index.php              ← point d'entrée web
├── pinoox                 ← point d'entrée CLI
├── composer.json
├── vendor/pinoox/pincore/ ← cœur du framework (à modifier uniquement pour des changements du cœur)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← votre app
```

- **Projet** — le dossier qui contient `index.php` et `apps/` (le nom du dossier n'a pas d'importance).
- **App** — un module complet avec ses propres contrôleurs (Controllers), modèles, routes, thème et configuration.
- **Core (cœur)** — le moteur partagé (routeur, HTTP, base de données, Twig, CLI, et plus encore).

Écrivez la logique métier dans `apps/`, pas dans `vendor/pinoox/pincore/`.

---

## Cycle de vie d'une requête HTTP

```
Navigateur → index.php → bootstrap
       → résolution de l'app active (domaine ou préfixe d'URL)
       → chargement de app.php et routes/
       → Flows → Contrôleur → Modèle (optionnel) → Vue ou JSON
```

---

## Nommage des apps

Format de package recommandé :

```
com_{vendor}_{name}
```

Exemple : `com_acme_shop` — le nom du dossier, la valeur `package` dans `app.php` et le segment de namespace doivent tous correspondre.

---

## Idéal pour

- Les sites multi-sections et les panneaux d'administration où chaque section peut être une app distincte
- Les équipes qui souhaitent développer, tester et maintenir des modules de manière indépendante
- Les projets PHP 8.1+ avec Composer et la CLI intégrée (`php pinoox …`)

---

## Documentation associée

- [Fonctionnalités de Pinoox](./features-pinoox.md)
- [Installer Pinoox](../start/installing-pinoox.md)
- [Votre première app](../start/your-first-app.md)
- [Tutoriel : API de notes](../examples/simple-api-app.md)
- [Tutoriel : annuaire téléphonique](../examples/phonebook-app.md)
- [Tutoriel : formulaire de contact](../examples/contact-form-app.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)

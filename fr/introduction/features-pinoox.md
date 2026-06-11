# Fonctionnalités de Pinoox

[← Retour à l'index](../README.md)

Pinoox 3.x est conçu pour un écosystème PHP modulaire : plusieurs apps indépendantes sur un cœur partagé, génération de code via la CLI, et des outils intégrés pour HTTP, la base de données, les thèmes et l'authentification.

---

## Architecture HMVC et apps indépendantes

Chaque app sous `apps/{package}/` possède une structure MVC complète :

| Couche | Chemin d'exemple |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| Vue (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

Ajouter ou désactiver une app n'affecte pas les autres.

---

## CLI et développement rapide

Depuis la racine du projet :

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

La CLI génère l'arborescence de dossiers standard, `app.php` et les fichiers de routes initiaux.

---

## Routage et Named Actions (actions nommées)

Les chemins d'URL et les gestionnaires logiques restent séparés :

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

Ce modèle facilite le refactoring et les tests.

---

## Flow (middleware)

Avant qu'une requête n'atteigne le contrôleur (Controller), les Flows s'exécutent — pour l'authentification, l'autorisation, la journalisation, et plus encore :

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Enregistrez les alias de Flow dans `app.php`.

---

## Vues et thèmes

- Templates Twig dans `theme/{themeName}/`
- Rendu avec **`View::render()`**
- Prise en charge des SPA avec Vite dans le thème (Vue/React)

---

## Base de données et Eloquent

- Query Builder et Eloquent via le Portal `DB`
- Migrations et seeders dans le dossier `database/migrations/` de chaque app
- Préfixe de table basé sur le nom du package (ex. `com_acme_blog_posts`)

---

## API et réponses JSON

Étendez **`ApiController`** et utilisez l'enveloppe standard :

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Internationalisation

Fichiers de traduction dans `lang/{locale}/*.lang.php` — adaptés aux apps multilingues.

---

## Documentation associée

- [Qu'est-ce que Pinoox ?](./what-is-pinoox.md)
- [Installer Pinoox](../start/installing-pinoox.md)
- [Router (routeur)](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Retour à l'index](../README.md)

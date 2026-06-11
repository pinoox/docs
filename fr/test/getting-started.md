# Premiers pas avec les tests dans Pinoox

[← Retour à l'index](../README.md)

Pinoox utilise une approche unique pour le **cœur du framework** (`tests/`) et **chaque app** (`apps/{package}/tests/`) : [Pest](https://pestphp.com/), un bootstrap partagé et `AppTestKit`. Ce guide parcourt ce flux standard avec des exemples pratiques.

---

## Stack de test

| Outil | Rôle |
|------|------|
| Pest | Exécution des tests PHP |
| `Pinoox\Component\Test\AppTestKit` | Boot environnement, app temporaire, requêtes HTTP |
| `tests/bootstrap.php` | Point d'entrée partagé pour les tests cœur et app |

---

## Exécuter les tests

```bash
# Tous les tests du cœur
vendor/bin/pest

# Depuis la CLI (sélection interactive du paquet)
php pinoox test

# Une app spécifique
php pinoox test com_my_shop

# Filtrer par nom de test
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Feature ou Unit uniquement
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

En CI, vous pouvez aussi utiliser les scripts dans `composer.json` :

```bash
composer test          # tests plateforme
composer test:apps     # tous les tests d'apps
```

---

## Structure du dossier de tests d'app

L'exécution de `php pinoox app:create` crée automatiquement le dossier `tests/` :

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

Créer un nouveau test :

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## Le fichier `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

Le helper `appPackage()` définit l'app active pour les helpers et la détection automatique.

---

## Helpers globaux

| Helper | Rôle |
|--------|---------|
| `appPackage($package?)` | Définir / lire le paquet actif |
| `inApp($package, fn)` | Exécuter du code dans `App::meeting()` |
| `appPath($package, $sub = '')` | Chemin vers le dossier de l'app |
| `fakeApp($package, $files)` | Créer une app temporaire avec fichiers personnalisés |
| `deleteFakeApp($package)` | Supprimer une app temporaire |
| `appGet($package, $uri, ...)` | Requête GET → `TestResponse` |
| `appPost($package, $uri, $data)` | Requête POST |
| `appPostJson($package, $uri, $json)` | Requête POST JSON |
| `pinooxBoot()` | Démarrer l'environnement de test |

---

## Unit — tester une classe Component

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — smoke test de boot d'app

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Cœur vs app

| Emplacement | Rôle | Cas de base |
|----------|---------|-----------|
| `tests/Feature/` | Framework, portals, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, intégration | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, logique pure | `Tests\AppTestCase` |

---

## Mode test

Dans l'environnement de test, `mode` est automatiquement défini à `test` :

```php
config('~pinoox')->get('mode'); // 'test'
```

En CI, configurez `.env.testing` ou `APP_ENV=test` si nécessaire.

---

## Conseils

1. Après `fakeApp()`, appelez toujours `deleteFakeApp()` dans `afterEach`.
2. Utilisez `inApp()` pour config, portals ou modèles dans une app.
3. Utilisez `appGet` / `appPostJson` pour les routes et API.
4. Routes → **Feature** ; classes `Component/` → **Unit**.
5. Utilisez `php pinoox test:create` au lieu de copier les fichiers à la main.

---

## Documentation associée

- [Tests HTTP](./http-tests.md)
- [Tests console](./console-tests.md)
- [Tests navigateur (HTML)](./browser-tests.md)
- [Tests base de données](./database.md)
- [Mocking](./mocking.md)
- [Votre première app](../start/your-first-app.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)

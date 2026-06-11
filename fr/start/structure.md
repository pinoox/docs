# Structure du projet

[← Retour à l'index](../README.md)

Pinoox utilise l'architecture HMVC : chaque app sous `apps/{package}/` est un module MVC complet et indépendant. Le cœur du framework se trouve dans `vendor/pinoox/pincore/` et n'est modifié que pour des changements de la plateforme elle-même.

---

## Disposition du projet

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← cœur (package Composer)
├── apps/                    ← toutes les apps
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← fichiers téléversés et stockage des apps
```

---

## Disposition d'une app

```
apps/com_acme_shop/
├── app.php                  ← manifeste (obligatoire)
├── boot.php                 ← routes/événements programmatiques (optionnel)
├── schedule.php             ← tâches cron (optionnel)
├── Controller/              ← gestionnaires HTTP
├── Model/                   ← modèles Eloquent
├── Flow/                    ← middleware
├── Component/               ← logique métier
├── Portal/                  ← façades de l'app (optionnel)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← constantes de noms d'actions (optionnel)
├── theme/default/           ← Twig + ressources
├── lang/en/                 ← traductions
├── config/                  ← configuration de l'app
├── database/migrations/
└── pinker/                  ← miroir de build
```

Les vues ne sont pas dans un dossier `View/` séparé — les templates se trouvent dans `theme/{themeName}/`.

---

## app.php — champs clés

```php
<?php

return [
    'package' => 'com_acme_shop',   // = nom du dossier
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespaces

PSR-4 : `App\` → `apps/`

| Fichier | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Règles de nommage

- Package : `com_{vendor}_{name}` — ex. `com_acme_shop`
- Nom du dossier = `package` dans `app.php` = segment de namespace
- Préfixe des tables de base de données : `{package}_` (ex. `com_acme_shop_orders`)

---

## Frontière entre app et cœur

| Changement | Emplacement |
|--------|----------|
| Nouveau endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| Bug du framework | `pinoox/pincore` (en amont) |
| UI | `apps/{package}/theme/` |

Gardez les apps indépendantes — utilisez les façades `Pinoox\Portal\*` plutôt que de coupler les apps entre elles.

---

## Documentation associée

- [Votre première app](./your-first-app.md)
- [Router (routeur)](../basic/routers.md)
- [Controllers (contrôleurs)](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Retour à l'index](../README.md)

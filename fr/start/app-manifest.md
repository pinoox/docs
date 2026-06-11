# Référence du manifeste app.php

[← Retour à l'index](../README.md)

`app.php` est le manifeste de votre app. Les valeurs par défaut se trouvent dans `vendor/pinoox/pincore/Component/Package/data/source.php` — ne surchargez que ce dont vous avez besoin.

---

## Identité et activation

| Clé | Rôle |
|-----|---------|
| `package` | Nom du dossier = namespace (`com_acme_shop`) |
| `name` | Nom d'affichage |
| `enable` | Activer / désactiver l'app |
| `description`, `developer`, `icon` | Métadonnées |
| `version-name`, `version-code` | Version de l'app |
| `sys-app`, `hidden`, `dock` | App système / cachée / dock du gestionnaire |
| `minpin` | Version minimale de la plateforme |

---

## Routeur et démarrage (boot)

| Clé | Rôle |
|-----|---------|
| `router.routes` | Fichiers `routes/*.php` |
| `boot` | Exécuter `boot.php` (true par défaut) |
| `boot-global` | Démarrer à chaque requête HTTP |
| `extends` | Démarrer quand l'app hôte démarre |
| `loader` | Fichiers supplémentaires (`func.php`) |
| `depends` | Apps requises |

Voir [boot.php et événements](../advanced/boot-and-events.md).

---

## Flow et sécurité

| Clé | Rôle |
|-----|---------|
| `flow` | Flows globaux (BootFlow) |
| `alias` | Nom → classe Flow |
| `auth` | mode, lifetime, JWT/cookie |
| `access` | RBAC : `groups`, `super_roles` |
| `transport` | Partager utilisateur/fichier/accès avec la plateforme |

Voir [Flows](../basic/flows.md), [Gestion des utilisateurs](../advanced/user-management.md), [Accès](../advanced/access-permissions.md).

---

## UI et thème

| Clé | Rôle |
|-----|---------|
| `theme` | Dossier du thème actif |
| `theme-context`, `theme-contexts`, `theme-extends` | Multi-contexte / héritage |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Locale par défaut |
| `open` | Comportement d'ouverture dans le gestionnaire |

---

## Base de données et stockage

| Clé | Rôle |
|-----|---------|
| `database` | Surcharge de la connexion à la base de données |
| `table.prefix` | Préfixe des tables |
| `transport.user` / `file_storage` / `access` | Préréglages ou clés granulaires |
| `filesystem` | disk, thumbs, access |

---

## Exécution (runtime)

| Clé | Rôle |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Surcharges de mode |
| `cache` | Précompiler (bake) routes/api/boot/twig |
| `log`, `redis`, `date` | Surcharges par app |
| `container` | Liaisons d'injection de dépendances (DI) |

---

## Pinker / Pinx

| Clé | Rôle |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | exclude/include pour les packages |

---

## Exemple combiné

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## Documentation associée

- [Structure du projet](./structure.md)
- [Config](../basic/config.md)

---

[← Retour à l'index](../README.md)

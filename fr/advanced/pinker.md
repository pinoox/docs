# Pinker et cache

[← Retour à l'index](../README.md)

**Pinker** est la couche de bake/runtime de Pinoox 3.x : la configuration et le cache sont compilés depuis la source vers des fichiers PHP qui peuvent être `include`s pour un démarrage plus rapide. Chemin standard par application : **`pinker/apps/{package}/`**.

---

## Structure des dossiers

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← app.php compilé (baked)
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← templates compilés
```

Au niveau du projet :

```
pinker/config/          ← config compilée (non sensible à l'environnement)
pinker/state/config/    ← surcharges post-installation (par ex. database)
```

---

## Commandes CLI

```bash
# Reconstruire Pinker pour une application
php pinoox pinker:rebuild com_acme_shop

# Alias court
php pinoox bake com_acme_shop

# Statut : comparer la source avec la sortie compilée
php pinoox pinker:status com_acme_shop

# Construire le cache (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Twig uniquement
php pinoox cache:build com_acme_shop --only=twig

# Pinker uniquement
php pinoox cache:build com_acme_shop --only=pinker

# Vider le cache
php pinoox cache:clear com_acme_shop
```

---

## Quand reconstruire

| Événement | Commande |
|-------|---------|
| Modification de `app.php` ou de la config | `pinker:rebuild` |
| Modification d'une route / api | `cache:build` |
| Modification d'un `.twig` en production | `cache:build --only=twig` |
| Après l'installation sur le serveur | `cache:build` + `pinker:rebuild` |
| Avant de construire le `.pinx` | `cache:build` (cache dans le paquet) |

---

## Activer le cache à l'exécution

Dans `apps/{package}/app.php` :

```php
'cache' => [
    'enabled' => false,   // valeur par défaut — passez à true en production si nécessaire
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## Miroir d'application — `pinker/app.php`

Chaque application peut avoir un miroir compilé :

```
apps/com_acme_shop/pinker/app.php   ← source/référence dans le dépôt
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## Helper `pinker()`

Pour le bake manuel de données :

```php
pinker($data, ['lifetime' => 3600]);
```

En général, on utilise plutôt la CLI ; rarement nécessaire dans le code d'application.

---

## Workflow de déploiement recommandé

```bash
# 1. construire le frontend
php pinoox theme:frontend build com_acme_shop

# 2. cache
php pinoox cache:build com_acme_shop

# 3. pinker (spécifique à l'environnement)
php pinoox pinker:rebuild com_acme_shop
```

---

## Conseils

- Ne modifiez pas `pinker/state/` manuellement — c'est l'installateur qui y écrit.
- En développement, le cache d'exécution est généralement désactivé ; ne reconstruisez qu'après des changements importants.
- Le `.pinx` peut embarquer un cache pré-construit ; sur le serveur cible, exécutez `cache:build --only=pinker` une seule fois.

---

## Documentation associée

- [Config](../basic/config.md)
- [Templates Twig](../basic/templates.md)
- [Référence CLI](../start/cli-reference.md)
- [Routeur](../basic/routers.md)

---

[← Retour à l'index](../README.md)

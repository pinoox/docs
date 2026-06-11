# Transport (ressources partagées)

[← Retour à l'index](../README.md)

Dans l'architecture HMVC, les applications peuvent partager entre elles les utilisateurs, l'authentification, les fichiers et les permissions via le bloc **`transport`** dans `app.php`. Sans transport, chaque application garde chaque ressource **locale** à son propre paquet.

| Terme | Signification |
|------|---------|
| **`platform`** | Portée logique partagée — les lignes DB partagées utilisent `app = platform` |
| **`pincore/`** | Uniquement le dossier physique du framework — **jamais** une valeur de portée de transport |

---

## Fonctionnement

Le transport comporte deux couches :

1. **Scénario** — un préréglage en un seul mot qui se développe en plusieurs clés granulaires.
2. **Clé granulaire** — un nom en plusieurs mots pour une ressource partagée précise.

```php
// app.php
'transport' => [
    'full' => 'platform',           // préréglage de scénario
    'file_storage' => 'local',      // surcharge granulaire
],
```

**Ordre de résolution :** clé granulaire explicite → scénario correspondant.

Les clés granulaires l'emportent toujours sur l'expansion du scénario. Si une clé n'est pas définie et qu'aucun scénario ne la couvre, l'application garde cette ressource **locale** (paquet courant).

---

## Valeurs de portée

Chaque scénario ou clé granulaire reçoit une portée :

| Portée | Signification |
|-------|---------|
| `local` | Paquet de l'application courante (valeur par défaut si omis) |
| `platform` | Portée partagée de la plateforme (`app = platform`, tables `pinx_*`) |
| `host` | Application qui a ouvert celle-ci (aperçu / `App::meeting()`) |
| `{package}` | Application explicite, par ex. `com_pinoox_manager` |

Pour **`auth_config`** et **`auth_cookie`**, `platform` et `{package}` se résolvent vers l'application qui **fournit les paramètres d'authentification** (typiquement `com_pinoox_manager` lorsqu'il est installé).

---

## Référence des scénarios

Préréglages en un seul mot. À utiliser dans `app.php` sous la forme `'transport' => ['{scenario}' => '{scope}']`.

| Scénario | Description | Clés granulaires incluses |
|----------|-------------|------------------------|
| `full` | Toutes les ressources partagées | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Système de connexion : comptes, auth, tokens de session | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | Uploads de fichiers et métadonnées | `file_storage` |
| `access` | Rôles et permissions | `access_table` |

---

## Référence des clés granulaires

Noms de ressources en plusieurs mots. À utiliser pour partager ou surcharger une seule ressource.

| Clé granulaire | Contrôle | Utilisée par |
|--------------|----------|---------|
| `user_table` | Colonne `app` de `UserModel` / portée globale | Comptes utilisateurs |
| `auth_config` | Mode d'auth, secret JWT, durées de vie (source du bloc `auth`) | `AuthConfig`, flux de connexion |
| `auth_cookie` | Clé client / nom du cookie (`auth.key`) | Stockage des tokens cookie & SPA |
| `session_token` | Colonne `app` de `TokenModel` / lignes de session en DB | Persistance de session |
| `file_storage` | Colonne `app` de `FileModel` / chemins d'upload | Uploads & métadonnées de fichiers |
| `access_table` | Portée `app` des modèles de rôles & permissions | `RoleModel`, `PermissionModel`, `can()` |

---

## Configurations courantes

**Fournisseur d'authentification pour la plateforme (par ex. manager) :**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Application consommatrice — tout est partagé, pas de bloc auth local :**

```php
'transport' => ['full' => 'platform'],
```

**Connexion partagée uniquement :**

```php
'transport' => ['user' => 'platform'],
```

**Application autonome** — omettez `transport`, ou fixez tout en local :

```php
'transport' => ['user' => 'local'],
```

**Surcharger une ressource au sein d'un scénario :**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## API de code

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // paquet résolu pour une clé granulaire
Transport::authSource();                       // application propriétaire des paramètres d'auth, ou null
Transport::sharesAuthWith($guest, $host);      // vérification d'auth inter-applications
Transport::resolved();                         // toutes les clés granulaires → portée
Transport::activeScenarios();                  // par ex. ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Base de données

Les tables de portée plateforme utilisent la connexion **`platform`** et le préfixe **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## Documentation associée

- [Manifeste app.php](../start/app-manifest.md)
- [Gestion des utilisateurs](./user-management.md)
- [Accès et permissions](./access-permissions.md)
- [Gestion des fichiers](./file-management.md)

---

[← Retour à l'index](../README.md)

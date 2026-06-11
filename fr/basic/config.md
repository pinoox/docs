# Config

[← Retour à l'index](../README.md)

Les paramètres de Pinoox 3.x sont stockés dans des fichiers PHP sous `config/` (cœur et app). Approche standard : **`config('key')`** pour lire et **`config('name')->set(...)->save()`** pour écrire.

---

## Lecture

```php
// Clé simple
$siteName = config('app.name');

// Clé imbriquée (notation point)
$merchant = config('payment.merchant_id');

// Valeur par défaut
$timeout = config('api.timeout', 30);

// Objet config pour le chaînage
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## Écriture et enregistrement

**Appelez toujours `save()` après les modifications :**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## Données imbriquées — `setLinear` / `getLinear`

```php
// Lecture
$themeName = config('theme.panel.name');

// Écriture
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Emplacements des fichiers de config

| Emplacement | Contenu |
|----------|----------|
| `pincore/config/*.config.php` | Paramètres du cœur (DB, domaine, …) |
| `apps/{package}/config/*.config.php` | Paramètres de l'app |
| `pinker/config/` | Version compilée (production) |
| `pinker/state/config/` | Surcharges post-installation (ex. DB) |

En développement, les valeurs sensibles sont lues depuis `.env` via `env()` / `_env()`.

---

## Exemple : paramètres d'une passerelle de paiement

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'stripe',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller ou Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## Exemple : menu dynamique

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'Reports', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Portal — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

En pratique, `config()` encapsule le même Portal — un seul style suffit.

---

## Conseils

- Ne commitez pas de secrets (clés API, mots de passe DB) dans git ; utilisez `.env` ou `pinker/state`.
- Nom de fichier : `{name}.config.php` → `config('{name}.key')`.
- Après un déploiement en production, exécutez `php pinoox pinker:rebuild` pour compiler la config.

---

## Documentation associée

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [Chemin de fichier](./path.md)
- [Manifeste app.php](../start/app-manifest.md)

---

[← Retour à l'index](../README.md)

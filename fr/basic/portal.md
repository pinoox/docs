# Portal (Facade)

[← Retour à l'index](../README.md)

Dans Pinoox 3.x, un Portal est une passerelle statique vers les services du cœur — le pattern **Facade** pour un accès simple à View, DB, Lang, etc. Pour le travail quotidien, utilisez **`Pinoox\Portal\*`** ; pour vos propres services d'app, créez des Portals sous votre app.

---

## Portals du cœur (courants)

```php
use Pinoox\Portal\View;
use Pinoox\Portal\Url;
use Pinoox\Portal\Path;
use Pinoox\Portal\Lang;
use Pinoox\Portal\Validation;
use Pinoox\Portal\Database\DB;

View::render('home', $data);
Url::link('products');
Url::forApp('com_acme_shop');
Path::get('storage/logs');
Lang::get('welcome.title');
config('payment.merchant_id');   // helper → Portal Config
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## Pourquoi les Portals ?

- Code court et lisible sans résoudre manuellement le conteneur
- Un point d'entrée stable dans Controller, Flow et Component
- Auto-complétion IDE via `@method` sur les classes Portal

---

## Portal pour un service d'app

### 1. Créer un Component

```php
// apps/com_acme_shop/Component/PriceCalculator.php
namespace App\com_acme_shop\Component;

class PriceCalculator
{
    public function withTax(float $price, float $rate = 0.09): float
    {
        return round($price * (1 + $rate), 2);
    }
}
```

### 2. Générer un Portal avec la CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Lier dans la classe Portal — `__register()` et `__bind()`

```php
// apps/com_acme_shop/Portal/PriceCalculator.php
namespace App\com_acme_shop\Portal;

use Pinoox\Component\Source\Portal;

class PriceCalculator extends Portal
{
    public static function __register(): void
    {
        self::__bind(\App\com_acme_shop\Component\PriceCalculator::class);
    }
}
```

### 4. Utilisation

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. Actualiser les métadonnées IDE

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs helper

| Tâche | Recommandé |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` ou `Lang::get()` |
| URL | `url('path')` ou `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` ou `$request->validate()` |
| Service d'app | Portal personnalisé dans `Portal/` |

---

## Conseils

- Gardez la logique métier dans `Component/` ; le Portal n'est qu'une facade
- Après l'ajout de méthodes à un Component, exécutez `portal:update`
- Ne modifiez pas directement les Portals du cœur ; étendez sous `apps/{package}/Portal/`

---

## Documentation associée

- [Services d'app](../advanced/services.md)
- [Config](./config.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)

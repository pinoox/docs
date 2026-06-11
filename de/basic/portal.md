# Portal (Facade)

[← Zurück zum Index](../README.md)

In Pinoox 3.x ist ein Portal ein statisches Gateway zu Core-Services — das **Facade**-Muster für einfachen Zugriff auf View, DB, Lang und mehr. Für die tägliche Arbeit **`Pinoox\Portal\*`** verwenden; für eigene App-Services Portals unter der App anlegen.

---

## Core-Portals (häufig)

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
config('payment.merchant_id');   // Helper → Config Portal
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## Warum Portals?

- Kurzer, lesbarer Code ohne manuelles Auflösen des Containers
- Ein stabiler Einstiegspunkt in Controller, Flow und Component
- IDE-Autovervollständigung über `@method` auf Portal-Klassen

---

## Portal für einen App-Service

### 1. Component erstellen

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

### 2. Portal per CLI generieren

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. In der Portal-Klasse binden — `__register()` und `__bind()`

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

### 4. Verwendung

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. IDE-Metadaten aktualisieren

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs. Helper

| Aufgabe | Empfohlen |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` oder `Lang::get()` |
| URL | `url('path')` oder `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` oder `$request->validate()` |
| App-Service | eigenes Portal in `Portal/` |

---

## Tipps

- Geschäftslogik in `Component/` halten; das Portal ist nur eine Facade
- Nach neuen Methoden in einer Component `portal:update` ausführen
- Core-Portals nicht direkt bearbeiten; unter `apps/{package}/Portal/` erweitern

---

## Verwandte Dokumentation

- [App-Services](../advanced/services.md)
- [Config](./config.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zum Index](../README.md)

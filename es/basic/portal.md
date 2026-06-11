# Portal (Facade)

[← Volver al índice](../README.md)

En Pinoox 3.x un Portal es una puerta de entrada estática a los servicios del núcleo — el patrón **Facade** para acceder fácilmente a View, DB, Lang y más. Para el trabajo diario usa **`Pinoox\Portal\*`**; para los servicios de tu propia app, crea Portals dentro de tu app.

---

## Portals del núcleo (comunes)

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

## ¿Por qué Portals?

- Código corto y legible sin resolver el contenedor manualmente
- Un punto de entrada estable en Controller, Flow y Component
- Autocompletado del IDE mediante `@method` en las clases Portal

---

## Portal para un servicio de la app

### 1. Crea un Component

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

### 2. Genera un Portal con la CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Vincula en la clase Portal — `__register()` y `__bind()`

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

### 4. Uso

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. Actualiza los metadatos del IDE

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs helper

| Tarea | Recomendado |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` o `Lang::get()` |
| URL | `url('path')` o `Url::link()` |
| Path | `path('reference')` |
| Validación | `Validation::validate()` o `$request->validate()` |
| Servicio de la app | Portal personalizado en `Portal/` |

---

## Consejos

- Mantén la lógica de negocio en `Component/`; el Portal es solo una facade
- Después de agregar métodos a un Component, ejecuta `portal:update`
- No edites los Portals del núcleo directamente; extiende bajo `apps/{package}/Portal/`

---

## Documentación relacionada

- [Servicios de la app](../advanced/services.md)
- [Configuración (Config)](./config.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)

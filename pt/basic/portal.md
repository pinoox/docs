# Portal (Facade)

[← Voltar ao índice](../README.md)

No Pinoox 3.x, um Portal é um gateway estático para serviços do núcleo — o padrão **Facade** para acesso simples a View, DB, Lang e mais. No dia a dia use **`Pinoox\Portal\*`**; para seus próprios serviços de app, crie Portals no seu app.

---

## Portals do núcleo (comuns)

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

## Por que Portals?

- Código curto e legível sem resolver o container manualmente
- Um ponto de entrada estável em Controller, Flow e Component
- Autocompletar da IDE via `@method` nas classes Portal

---

## Portal para serviço de app

### 1. Criar um Component

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

### 2. Gerar um Portal com CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Vincular na classe Portal — `__register()` e `__bind()`

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

### 5. Atualizar metadados da IDE

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs helper

| Tarefa | Recomendado |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` ou `Lang::get()` |
| URL | `url('path')` ou `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` ou `$request->validate()` |
| Serviço de app | Portal personalizado em `Portal/` |

---

## Dicas

- Mantenha lógica de negócio em `Component/`; o Portal é apenas uma facade
- Após adicionar métodos a um Component, execute `portal:update`
- Não edite Portals do núcleo diretamente; estenda em `apps/{package}/Portal/`

---

## Documentação relacionada

- [Serviços do app](../advanced/services.md)
- [Config](./config.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

# Portal (Facade)

[← Вернуться к оглавлению](../README.md)

В Pinoox 3.x Portal — это статический шлюз к сервисам ядра — паттерн **Facade** для простого доступа к View, DB, Lang и другим компонентам. Для повседневной работы используйте **`Pinoox\Portal\*`**; для собственных сервисов приложения создавайте Portal в своём приложении.

---

## Основные Portal (частые)

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
config('payment.merchant_id');   // helper → Config Portal
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## Зачем нужны Portal?

- Короткий, читаемый код без ручного разрешения контейнера
- Одна стабильная точка входа в Controller, Flow и Component
- Автодополнение IDE через `@method` в классах Portal

---

## Portal для сервиса приложения

### 1. Создайте Component

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

### 2. Сгенерируйте Portal через CLI

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Привяжите в классе Portal — `__register()` и `__bind()`

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

### 4. Использование

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. Обновите метаданные IDE

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs helper

| Задача | Рекомендуется |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` или `Lang::get()` |
| URL | `url('path')` или `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` или `$request->validate()` |
| Сервис приложения | свой Portal в `Portal/` |

---

## Советы

- Бизнес-логику держите в `Component/`; Portal — только фасад
- После добавления методов в Component выполните `portal:update`
- Не редактируйте Portal ядра напрямую; расширяйте в `apps/{package}/Portal/`

---

## Связанные документы

- [Сервисы приложения](../advanced/services.md)
- [Конфигурация](./config.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)

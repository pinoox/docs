# Portal (Facade)

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 Portal은 core service에 대한 static gateway입니다 — View, DB, Lang 등에 간단히 접근하는 **Facade** 패턴. 일상 작업에는 **`Pinoox\Portal\*`**를 사용하고, 앱 전용 service는 앱 아래 Portal을 만드세요.

---

## Core Portal (자주 사용)

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

## Portal을 쓰는 이유

- container를 수동 resolve 없이 짧고 읽기 쉬운 code
- Controller, Flow, Component에서 하나의 안정적인 진입점
- Portal class의 `@method`로 IDE auto-complete

---

## 앱 service용 Portal

### 1. Component 생성

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

### 2. CLI로 Portal 생성

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. Portal class에 bind — `__register()`와 `__bind()`

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

### 4. 사용

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. IDE metadata 갱신

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal vs helper

| Task | Recommended |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` or `Lang::get()` |
| URL | `url('path')` or `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` or `$request->validate()` |
| App service | custom Portal in `Portal/` |

---

## Tips

- business logic은 `Component/`에; Portal은 facade만
- Component에 method 추가 후 `portal:update` 실행
- core Portal을 직접 편집하지 말고 `apps/{package}/Portal/` 아래에서 확장

---

## 관련 문서

- [App Services](../advanced/services.md)
- [Config](./config.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)

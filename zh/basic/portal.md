# Portal（门面）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，Portal 是核心服务的静态入口 — 即**门面（Facade）**模式，用于便捷访问 View、DB、Lang 等。日常工作请使用 **`Pinoox\Portal\*`**；对于你自己的应用服务，可在应用下创建 Portal。

---

## 核心 Portal（常用）

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
config('payment.merchant_id');   // 辅助函数 → Config Portal
Validation::validate($data, $rules);
DB::table('users')->get();
```

---

## 为什么使用 Portal？

- 代码简短易读，无需手动解析容器
- 在 Controller、Flow 和 Component 中拥有统一稳定的入口
- 通过 Portal 类上的 `@method` 获得 IDE 自动补全

---

## 为应用服务创建 Portal

### 1. 创建 Component

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

### 2. 用 CLI 生成 Portal

```bash
php pinoox portal:create PriceCalculator -p com_acme_shop
```

### 3. 在 Portal 类中绑定 — `__register()` 与 `__bind()`

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

### 4. 使用

```php
use App\com_acme_shop\Portal\PriceCalculator;

$total = PriceCalculator::withTax(100_000);
```

### 5. 刷新 IDE 元数据

```bash
php pinoox portal:update PriceCalculator -p com_acme_shop
```

---

## Portal 与辅助函数对比

| 任务 | 推荐方式 |
|------|-------------|
| View | `View::render()` |
| Config | `config('key')` |
| Lang | `t('key')` 或 `Lang::get()` |
| URL | `url('path')` 或 `Url::link()` |
| Path | `path('reference')` |
| Validation | `Validation::validate()` 或 `$request->validate()` |
| 应用服务 | `Portal/` 中的自定义 Portal |

---

## 小贴士

- 业务逻辑保留在 `Component/` 中；Portal 只是门面
- 给 Component 添加方法后，运行 `portal:update`
- 不要直接修改核心 Portal；在 `apps/{package}/Portal/` 下进行扩展

---

## 相关文档

- [应用服务（Services）](../advanced/services.md)
- [配置（Config）](./config.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)

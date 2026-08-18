# Kernel و pipeline بوت

[← بازگشت به فهرست](../README.md)

پینوکس چطور اپ را بوت می‌کند، کنترلر را resolve می‌کند، و متادیتای production را کش می‌کند — در حالی که **Portal / HMVC / Flow** معماری اصلی می‌مانند.

برای ثبت route، listener و افزونه در `boot.php` به [boot.php و رویدادها](./boot-and-events.md) مراجعه کنید. رویداد دامنه و auto-discovery: [رویدادها](./events.md).

---

## چرخهٔ درخواست

```text
index.php
  → bootstrap.php
  → AppProvider::boot()
      → prerequisite()  ← BootPipeline (یک‌بار برای هر پکیج)
      → HttpKernel::handle()
          → زنجیره Flow
          → Controller
          → View / JSON
```

موتور HTTP همان Symfony `HttpKernel` است. پینوکس اپ‌های HMVC، DI از طریق Portal، و میان‌افزار Flow را روی آن می‌سازد.

---

## Pipeline بوت

`AppProvider::prerequisite()` یک pipeline مرتب اجرا می‌کند:

| مرحله | کاربرد |
|-------|--------|
| `composer` | بارگذاری `vendor/autoload.php` اپ در صورت وجود |
| `loader` | اجرای ورودی‌های `app.php` → `loader` |
| `app.boot` | `AppBootstrap::ensure()` — `boot.php`، رجیستری routes/API |
| `container` | DI اپ + ثبت کنترلر (اختیاری / opt-in) |
| `events` | listenerهای قدیمی `app.php` → `event` |
| `database` | اتصال‌های DB مخصوص اپ |
| `api` | ارائه‌دهندگان OpenAPI / GraphQL |
| `session` | درایور session و شروع خودکار اختیاری |

مشاهدهٔ مراحل:

```php
use Pinoox\Portal\Kernel\Boot;

Boot::bootStages();
// یا AppProvider::___()->bootStages();
```

افزودن مرحله از `boot.php`:

```php
use Pinoox\Portal\App\AppProvider;

AppProvider::___()->pipeline()->add('metrics', function () {
    // منطق بوت سفارشی
}, after: 'app.boot');
```

با احتیاط استفاده کنید — برای توسعهٔ اپ ترجیح دهید رویدادهای `AppRegister` را به کار ببرید. [boot.php و رویدادها](./boot-and-events.md).

---

## مدل دو کانتینر

| کانتینر | نقش |
|---------|-----|
| **Symfony `ContainerBuilder`** (`container()` / `pincore()`) | سرویس‌های Portal، aliasهای kernel |
| **Illuminate Container** (`Container::Illuminate()`) | تزریق سازنده / bindings |

`ServiceContainerBootstrap` مقدار `service_container` را ثبت می‌کند و وقتی فعال باشد bindings را پل می‌زند.

---

## کانتینر اپ و DI کنترلر (opt-in)

در `apps/{package}/app.php` فعال کنید:

```php
'container' => [
    'enabled' => true,
    'autowire_controllers' => true,
    'bindings' => [
        \App\com_my_shop\Component\CartContract::class => \App\com_my_shop\Component\CartService::class,
    ],
    'singletons' => [
        \App\com_my_shop\Component\CartService::class => true,
    ],
],
```

فایل اختیاری: `apps/{package}/bindings.php` (استاب: `pincore/stubs/bindings.php.stub`).

### تزریق سازنده در کنترلر

```php
namespace App\com_my_shop\Controller;

use App\com_my_shop\Component\CartContract;
use Pinoox\Component\Kernel\Controller\Controller;

class CheckoutController extends Controller
{
    public function __construct(private CartContract $cart)
    {
    }

    public function index()
    {
        return $this->json($this->cart->summary());
    }
}
```

وقتی `container.enabled` برابر `false` باشد (پیش‌فرض)، کنترلرها مثل قبل ساخته می‌شوند (`new Controller()` + `setContainer()`).

---

## کش production

کش بوت (`php pinoox cache:build --only=boot`) این موارد را هم ذخیره می‌کند:

- مانیفست‌های API / GraphQL
- **bindings کانتینر + لیست کنترلر** وقتی `container.enabled` فعال است

در runtime، `BootCacheStore::tryHydrate()` قبل از مرحلهٔ `container` در pipeline، bindings را بازیابی می‌کند — بدون اسکن فایل‌سیستم در production.

بعد از تغییر این‌ها دوباره بسازید:

- `boot.php`
- `bindings.php`
- بخش container در `app.php`
- فایل‌های زیر `Controller/`

```bash
php pinoox cache:build com_my_shop --only=boot
php pinoox cache:clear com_my_shop --only=boot
```

بیشتر: [Pinker و Cache](./pinker.md).

---

## چه چیزی ثابت می‌ماند

- **Portal** — فاسادهای استاتیک به کامپوننت‌ها (`View`، `Router`، `Date`، …)
- **HMVC** — ماژول‌های `apps/{package}`، `App::meeting()`
- **Flow** — alias میان‌افزار در `app.php`، `flows: ['auth']` روی route
- **Symfony HttpKernel** — pipeline درخواست / رویداد / exception

تغییر شکستی نیست: DI کانتینر **اختیاری (opt-in)** است.

---

## مستندات مرتبط

- [boot.php و رویدادها](./boot-and-events.md)
- [رویدادها (Events)](./events.md)
- [Pinker و Cache](./pinker.md)
- [Flow](../basic/flows.md)
- [مرجع app.php](../start/app-manifest.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)

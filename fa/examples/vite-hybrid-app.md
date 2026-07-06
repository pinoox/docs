# نمونه عملی: Vite hybrid (Twig + ویجت JS)

[← بازگشت به فهرست](../README.md)

صفحه **محصول** با HTML کامل در Twig (SEO و meta در PHP) + **ویجت تعاملی Vite** (Vanilla JS) برای محاسبه قیمت بر اساس تعداد. پروفایل **hybrid** — بدون Vue/React.

**پکیج:** `com_acme_vite_shop`  
**آدرس:** `http://localhost/pinoox/shop`  
**پروفایل:** `hybrid` · **stack:** `vite`  
**سورس کامل:** [docs/source/vite-hybrid-app/](../../source/vite-hybrid-app/) — کپی در `apps/`
---

## پیش‌نیاز

- Node.js 18+
- [Views](../basic/views.md) و [`vite_tags()`](../basic/templates.md)

---

## گام ۱ — ساخت اپ

```bash
php pinoox app:create com_acme_vite_shop --simple
php pinoox app:router set /shop com_acme_vite_shop
```

---

## گام ۲ — کنترلر و SEO

```bash
php pinoox controller:create ProductController com_acme_vite_shop
```

```php
<?php
namespace App\com_acme_vite_shop\Controller;

use Pinoox\Component\Helpers\PinooxScriptHelper;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class ProductController extends Controller
{
    public function show(int $id = 1)
    {
        $products = [
            1 => ['id' => 1, 'title' => 'ماگ پینوکس', 'summary' => 'ماگ سرامیکی با لوگوی پینوکس', 'unit_price' => 89000],
            2 => ['id' => 2, 'title' => 'تی‌شرت توسعه‌دهنده', 'summary' => 'نخ پنبه‌ای، دوخت محکم', 'unit_price' => 245000],
        ];

        $product = $products[$id] ?? $products[1];

        View::shareSeo([
            'title' => $product['title'] . ' | فروشگاه نمونه',
            'description' => $product['summary'],
        ]);

        return View::render('pages/product.twig', [
            'product' => $product,
            'bootstrap' => PinooxScriptHelper::bootstrap([
                'productId' => $product['id'],
                'unitPrice' => $product['unit_price'],
                'currency' => 'تومان',
            ]),
        ]);
    }
}
```

`routes/web.php`:

```php
<?php

use App\com_acme_vite_shop\Controller\ProductController;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('/', [ProductController::class, 'show'])->name('home');
    get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
});
```

---

## گام ۳ — Vite در تم (بدون Vue/React)

در `theme/default/`:

**`package.json`**

```json
{
  "name": "vite-shop",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "vite": "^5.4.0"
  }
}
```

**`vite.config.js`**

```js
import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const target = env.VITE_SERVER_URL || 'http://127.0.0.1:8000';

    return {
        root: '.',
        build: {
            outDir: 'dist',
            manifest: true,
            rollupOptions: {
                input: {
                    priceWidget: 'src/widgets/price-calculator.js',
                },
            },
        },
        server: {
            proxy: {
                '/api': target,
            },
        },
    };
});
```

**`frontend.config.php`**

```php
<?php

return [
    'profile' => 'hybrid',
    'stack' => 'vite',
    'entry' => 'src/widgets/price-calculator.js',
    'manifest' => 'dist/.vite/manifest.json',
    'dev' => [
        'enabled' => (bool) _env('VITE_DEV', false),
        'url' => rtrim((string) _env('VITE_DEV_SERVER', 'http://127.0.0.1:5173'), '/'),
    ],
];
```

```bash
php pinoox fe com_acme_vite_shop install
```

---

## گام ۴ — ویجت Vanilla JS

**`src/boot.js`** (مشترک با stubهای SPA):

```js
export function getBoot() {
    return globalThis.__PINOOX__ ?? {};
}
```

**`src/widgets/price-calculator.js`**

```js
import { getBoot } from '../boot.js';

const boot = getBoot();
const unitPrice = Number(boot.unitPrice ?? 0);
const currency = boot.currency ?? '';

const root = document.getElementById('price-widget');

if (root && unitPrice > 0) {
    root.innerHTML = `
        <label>
            تعداد:
            <input type="number" id="qty" min="1" value="1" />
        </label>
        <p id="total" style="font-weight:600;margin-top:.5rem;"></p>
    `;

    const qtyInput = root.querySelector('#qty');
    const totalEl = root.querySelector('#total');

    function render() {
        const qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);
        qtyInput.value = qty;
        const total = unitPrice * qty;
        totalEl.textContent = `جمع: ${total.toLocaleString('fa-IR')} ${currency}`;
    }

    qtyInput.addEventListener('input', render);
    render();
}
```

---

## گام ۵ — قالب Twig (Outline)

`pages/product.twig`:

```twig
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ product.title }}</title>
    {{ seo_tags()|raw }}
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: Tahoma, system-ui, sans-serif; background: #f1f5f9; margin: 0; line-height: 1.5; }
        .page { max-width: 640px; margin: 0 auto; padding: 2rem 1rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; }
        .price { font-size: 1.25rem; color: #2563eb; }
    </style>
</head>
<body>
<div class="page">
    <article class="panel">
        <h1>{{ product.title }}</h1>
        <p>{{ product.summary }}</p>
        <p class="price">قیمت واحد: {{ product.unit_price|number_format(0, '.', ',') }} تومان</p>
    </article>

    <section class="panel">
        <h2>محاسبه سفارش</h2>
        <div id="price-widget">در حال بارگذاری…</div>
    </section>
</div>

{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/widgets/price-calculator.js')|raw }}
</body>
</html>
```

> **قانون hybrid:** عنوان، توضیح و محتوای اصلی در Twig — ویجت فقط بخش تعاملی را mount می‌کند. داده اولیه از `bootstrap` می‌آید، نه fetch تکراری.

---

## گام ۶ — dev

`php pinoox fe dev` URLها را از روتر اپ resolve می‌کند. برای dev محلی به `.env` پر شده نیاز ندارید — کلیدهای خالی `VITE_*` در runtime inject می‌شوند.

overrideهای دستی اختیاری در `theme/default/.env`:

```env
# ذخیره بلوک VITE_* autogenerated در هر اجرای fe dev (پیش‌فرض: false)
ENV_SERVER_SYNC=false

# overrideهای دستی (وقتی تنظیم شده‌اند استفاده می‌شوند):
# VITE_SERVER_URL=http://localhost/pinoox/shop
# VITE_DEV_PORT=5173
```

```bash
php pinoox fe com_acme_vite_shop dev --no-serve
```

در **پلتفرم چند اپ** (مثلاً welcome + manager)، به‌جای چند ترمینال `fe dev` از `php pinoox fe dev:apps` با نام کامل package استفاده کنید. [فرانت‌اند و Vite — dev:apps](../basic/frontend-vite.md) را ببینید.

صفحه **PHP** را در مرورگر باز کنید (نه port Vite). با تغییر تعداد، جمع لحظه‌ای به‌روز می‌شود.

---

## گام ۷ — build

```bash
php pinoox fe com_acme_vite_shop build
```

فایل manifest در `dist/.vite/manifest.json` — Twig در production تگ `<script>` صحیح را inject می‌کند.

---

## چند entry (اختیاری)

برای چند ویجت، `rollupOptions.input` را گسترش دهید:

```js
input: {
    priceWidget: 'src/widgets/price-calculator.js',
    gallery: 'src/widgets/gallery.js',
},
```

در Twig هر node جدا:

```twig
<div id="gallery"></div>
{{ vite_tags('src/widgets/gallery.js')|raw }}
```

---

## تست

1. View Source — `<title>` و meta از `shareSeo()` آمده باشد.
2. بدون JS — عنوان و قیمت واحد همچنان دیده شوند (`noscript` اختیاری).
3. با JS — ویجت تعداد و جمع را نشان دهد.

---

## مقایسه پروفایل‌ها

| پروفایل | SEO | Node در production | مثال |
|---------|-----|-------------------|------|
| `twig` | عالی | خیر | [دفترچه تلفن](./phonebook-app.md) |
| `hybrid` | عالی | خیر | **این نمونه** |
| `spa` | کم‌اهمیت | خیر | [Vue SPA](./vue-spa-app.md) |

---

## مستندات مرتبط

- [فرانت‌اند و Vite](../basic/frontend-vite.md)
- [قالب — Templates](../basic/templates.md)
- [CLI فرانت — theme:frontend](../start/cli-reference.md)

---

[← بازگشت به فهرست](../README.md)

# شرح تطبيقي: Vite هجين (Twig + عنصر JS)

[← العودة إلى الفهرس](../README.md)

**صفحة منتج** بـ HTML كامل في Twig (SEO والبيانات الوصفية في PHP) مع **عنصر Vite** تفاعلي (Vanilla JS) يحسب السعر حسب الكمية. ملف شخصي **hybrid** — دون Vue/React.

**الحزمة (Package):** `com_acme_vite_shop`  
**الرابط (URL):** `http://localhost/pinoox/shop`  
**الملف الشخصي (Profile):** `hybrid` · **المكدس (stack):** `vite`  
**الكود المصدري الكامل:** [docs/source/vite-hybrid-app/](../../source/vite-hybrid-app/) — انسخه إلى `apps/`
---

## المتطلبات المسبقة

- Node.js 18+
- [العروض (Views)](../basic/views.md) و [`vite_tags()`](../basic/templates.md)

---

## الخطوة 1 — إنشاء التطبيق

```bash
php pinoox app:create com_acme_vite_shop --simple
php pinoox app:router set /shop com_acme_vite_shop
```

---

## الخطوة 2 — المتحكم و SEO

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
            1 => ['id' => 1, 'title' => 'Pinoox Mug', 'summary' => 'Ceramic mug with Pinoox logo', 'unit_price' => 89000],
            2 => ['id' => 2, 'title' => 'Developer T-Shirt', 'summary' => 'Cotton fabric, durable stitch', 'unit_price' => 245000],
        ];

        $product = $products[$id] ?? $products[1];

        View::shareSeo([
            'title' => $product['title'] . ' | Sample shop',
            'description' => $product['summary'],
        ]);

        return View::render('pages/product.twig', [
            'product' => $product,
            'bootstrap' => PinooxScriptHelper::bootstrap([
                'productId' => $product['id'],
                'unitPrice' => $product['unit_price'],
                'currency' => 'USD',
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

## الخطوة 3 — Vite في الثيم (دون Vue/React)

في `theme/default/`:

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

## الخطوة 4 — عنصر Vanilla JS

**`src/boot.js`** (مشترك مع stubs SPA):

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
            Qty:
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
        totalEl.textContent = `Total: ${total.toLocaleString()} ${currency}`;
    }

    qtyInput.addEventListener('input', render);
    render();
}
```

---

## الخطوة 5 — قالب Twig (Outline)

`pages/product.twig`:

```twig
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>{{ product.title }}</title>
    {{ seo_tags()|raw }}
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; margin: 0; line-height: 1.5; }
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
        <p class="price">Unit price: {{ product.unit_price|number_format(2, '.', ',') }}</p>
    </article>

    <section class="panel">
        <h2>Order calculator</h2>
        <div id="price-widget">Loading…</div>
    </section>
</div>

{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/widgets/price-calculator.js')|raw }}
</body>
</html>
```

> **قاعدة hybrid:** العنوان، الوصف، والمحتوى الرئيسي في Twig — العنصر يثبّت فقط الجزء التفاعلي. البيانات الأولية من `bootstrap`، وليس من طلب API مكرر.

---

## الخطوة 6 — `.env` والتطوير

```env
VITE_SERVER_URL=http://localhost/pinoox/shop
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
```

```bash
php pinoox fe com_acme_vite_shop dev --no-serve
```

افتح الصفحة في MAMP؛ تغيير الكمية يحدّث الإجمالي مباشرة.

---

## الخطوة 7 — البناء

```bash
php pinoox fe com_acme_vite_shop build
```

الـ manifest في `dist/.vite/manifest.json` — Twig يحقن وسوم `<script>` الصحيحة في الإنتاج.

---

## نقاط دخول متعددة (اختياري)

وسّع `rollupOptions.input` لعدة عناصر:

```js
input: {
    priceWidget: 'src/widgets/price-calculator.js',
    gallery: 'src/widgets/gallery.js',
},
```

في Twig، ثبّت كل عنصر في عقدة منفصلة:

```twig
<div id="gallery"></div>
{{ vite_tags('src/widgets/gallery.js')|raw }}
```

---

## الاختبار

1. View Source — `<title>` والبيانات الوصفية من `shareSeo()`.
2. دون JS — العنوان وسعر الوحدة يظهران (اختياري `noscript`).
3. مع JS — العنصر يعرض الكمية والإجمالي المتغيّر.

---

## مقارنة الملفات الشخصية

| الملف الشخصي | SEO | Node في الإنتاج | مثال |
|---------|-----|-------------------|---------|
| `twig` | ممتاز | لا | [Phonebook](./phonebook-app.md) |
| `hybrid` | ممتاز | لا | **هذا الشرح** |
| `spa` | أولوية منخفضة | لا | [Vue SPA](./vue-spa-app.md) |

---

## توثيقات ذات صلة

- [القوالب](../basic/templates.md)
- [CLI `theme:frontend`](../start/cli-reference.md)

---

[← العودة إلى الفهرس](../README.md)

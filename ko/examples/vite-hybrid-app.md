# 워크스루: Vite hybrid (Twig + JS widget)

[← 색인으로 돌아가기](../README.md)

Twig의 full HTML(SEO와 meta는 PHP)과 수량별 가격을 계산하는 interactive **Vite widget**(Vanilla JS)이 있는 **product page**. **Hybrid** profile — Vue/React 없음.

**Package:** `com_acme_vite_shop`  
**URL:** `http://localhost/pinoox/shop`  
**Profile:** `hybrid` · **stack:** `vite`  
**Full source:** [docs/source/vite-hybrid-app/](../../source/vite-hybrid-app/) — `apps/`에 copy
---

## 사전 요구 사항

- Node.js 18+
- [Views](../basic/views.md) and [`vite_tags()`](../basic/templates.md)

---

## 단계 1 — 앱 생성

```bash
php pinoox app:create com_acme_vite_shop --simple
php pinoox app:router set /shop com_acme_vite_shop
```

---

## 단계 2 — Controller와 SEO

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

## 단계 3 — 테마의 Vite (Vue/React 없음)

In `theme/default/`:

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

## 단계 4 — 바닐라 JS 위젯

**`src/boot.js`** (shared with SPA stubs):

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

## 단계 5 — Twig 템플릿 (Outline)

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

> **Hybrid rule:** title, description, main content는 Twig에 — widget은 interactive block만 mount. 초기 data는 duplicate API fetch가 아니라 `bootstrap`에서.

---

## 단계 6 — `.env` 및 개발

```env
VITE_SERVER_URL=http://localhost/pinoox/shop
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
```

```bash
php pinoox fe com_acme_vite_shop dev --no-serve
```

MAMP에서 page를 열고; 수량 변경 시 live total이 업데이트됩니다.

---

## 단계 7 — 빌드

```bash
php pinoox fe com_acme_vite_shop build
```

Manifest at `dist/.vite/manifest.json` — Twig injects the correct `<script>` tag in production.

---

## Multiple entries (optional)

Extend `rollupOptions.input` for several widgets:

```js
input: {
    priceWidget: 'src/widgets/price-calculator.js',
    gallery: 'src/widgets/gallery.js',
},
```

In Twig, mount each node separately:

```twig
<div id="gallery"></div>
{{ vite_tags('src/widgets/gallery.js')|raw }}
```

---

## Test

1. View Source — `<title>` and meta from `shareSeo()`.
2. Without JS — title and unit price still visible (optional `noscript`).
3. With JS — widget shows quantity and running total.

---

## Profile comparison

| Profile | SEO | Node in production | Example |
|---------|-----|-------------------|---------|
| `twig` | Excellent | No | [Phonebook](./phonebook-app.md) |
| `hybrid` | Excellent | No | **이 실습 가이드** |
| `spa` | Low priority | No | [Vue SPA](./vue-spa-app.md) |

---

## 관련 문서

- [Templates](../basic/templates.md)
- [CLI `theme:frontend`](../start/cli-reference.md)

---

[← 색인으로 돌아가기](../README.md)

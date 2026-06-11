# ウォークスルー: Vite ハイブリッド（Twig + JS ウィジェット）

[← 索引に戻る](../README.md)

Twig で完全な HTML（PHP で SEO と meta）の **商品ページ** に、数量で価格を計算するインタラクティブ **Vite ウィジェット**（Vanilla JS）を追加。**Hybrid** プロファイル — Vue/React なし。

**Package:** `com_acme_vite_shop`  
**URL:** `http://localhost/pinoox/shop`  
**Profile:** `hybrid` · **stack:** `vite`  
**完全なソース:** [docs/source/vite-hybrid-app/](../../source/vite-hybrid-app/) — `apps/` にコピー
---

## 前提条件

- Node.js 18+
- [View](../basic/views.md) と [`vite_tags()`](../basic/templates.md)

---

## ステップ 1 — アプリを作成

```bash
php pinoox app:create com_acme_vite_shop --simple
php pinoox app:router set /shop com_acme_vite_shop
```

---

## ステップ 2 — Controller and SEO

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

## ステップ 3 — テーマ内の Vite（Vue/React なし）

`theme/default/` 内:

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

## ステップ 4 — Vanilla JS widget

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

## ステップ 5 — Twig template (Outline)

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

> **Hybrid rule:** title, description, and main content live in Twig — the widget only mounts the interactive block. Initial data comes from `bootstrap`, not a duplicate API fetch.

---

## ステップ 6 — `.env` and dev

```env
VITE_SERVER_URL=http://localhost/pinoox/shop
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
```

```bash
php pinoox fe com_acme_vite_shop dev --no-serve
```

MAMP でページを開く。数量を変更するとリアルタイム合計が更新されます。

---

## ステップ 7 — ビルド

```bash
php pinoox fe com_acme_vite_shop build
```

`dist/.vite/manifest.json` の manifest — 本番では Twig が正しい `<script>` タグを注入します。

---

## 複数エントリ（任意）

複数ウィジェット用に `rollupOptions.input` を拡張:

```js
input: {
    priceWidget: 'src/widgets/price-calculator.js',
    gallery: 'src/widgets/gallery.js',
},
```

Twig では各ノードを個別にマウントします:

```twig
<div id="gallery"></div>
{{ vite_tags('src/widgets/gallery.js')|raw }}
```

---

## テスト

1. ページのソースを表示 — `shareSeo()` による `<title>` と meta。
2. JS なし — タイトルと単価は表示されたまま（任意で `noscript`）。
3. JS あり — ウィジェットが数量と合計を表示。

---

## プロファイル比較

| プロファイル | SEO | 本番での Node | 例 |
|---------|-----|-------------------|---------|
| `twig` | 優秀 | 不要 | [Phonebook](./phonebook-app.md) |
| `hybrid` | 優秀 | 不要 | **このウォークスルー** |
| `spa` | 低優先度 | 不要 | [Vue SPA](./vue-spa-app.md) |

---

## 関連ドキュメント

- [Templates](../basic/templates.md)
- [CLI `theme:frontend`](../start/cli-reference.md)

---

[← 索引に戻る](../README.md)

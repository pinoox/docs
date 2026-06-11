# 实战演练：Vite 混合应用（Twig + JS 组件）

[← 返回索引](../README.md)

一个**产品页**：Twig 提供完整 HTML（SEO 与 meta 在 PHP 中），外加由 **Vite 组件**（Vanilla JS）按数量计算价格的交互区块。**混合（hybrid）** 配置 — 无 Vue/React。

**包名（Package）：** `com_acme_vite_shop`  
**URL：** `http://localhost/pinoox/shop`  
**配置：** `hybrid` · **技术栈：** `vite`  
**完整源码：** [docs/source/vite-hybrid-app/](../../source/vite-hybrid-app/) — 复制到 `apps/`
---

## 前置条件

- Node.js 18+
- [视图](../basic/views.md) 与 [`vite_tags()`](../basic/templates.md)

---

## 步骤 1 — 创建应用

```bash
php pinoox app:create com_acme_vite_shop --simple
php pinoox app:router set /shop com_acme_vite_shop
```

---

## 步骤 2 — 控制器与 SEO

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

`routes/web.php`：

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

## 步骤 3 — 主题中的 Vite（无 Vue/React）

在 `theme/default/` 中：

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

## 步骤 4 — Vanilla JS 组件

**`src/boot.js`**（与 SPA 存根共用）：

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

## 步骤 5 — Twig 模板（概要）

`pages/product.twig`：

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

> **混合规则：** 标题、描述和主要内容在 Twig 中 — 组件只挂载交互区块。初始数据来自 `bootstrap`，不要重复请求 API。

---

## 步骤 6 — `.env` 与开发

```env
VITE_SERVER_URL=http://localhost/pinoox/shop
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
```

```bash
php pinoox fe com_acme_vite_shop dev --no-serve
```

在 MAMP 中打开页面；修改数量会实时更新总价。

---

## 步骤 7 — 构建

```bash
php pinoox fe com_acme_vite_shop build
```

清单位于 `dist/.vite/manifest.json` — 生产环境中 Twig 会注入正确的 `<script>` 标签。

---

## 多个入口（可选）

扩展 `rollupOptions.input` 以支持多个组件：

```js
input: {
    priceWidget: 'src/widgets/price-calculator.js',
    gallery: 'src/widgets/gallery.js',
},
```

在 Twig 中分别挂载各节点：

```twig
<div id="gallery"></div>
{{ vite_tags('src/widgets/gallery.js')|raw }}
```

---

## 测试

1. 查看源代码 — `<title>` 和 meta 来自 `shareSeo()`。
2. 无 JS — 标题和单价仍可见（可选 `noscript`）。
3. 有 JS — 组件显示数量与累计总价。

---

## 配置对比

| 配置 | SEO | 生产环境 Node | 示例 |
|---------|-----|-------------------|---------|
| `twig` | 优秀 | 否 | [电话簿](./phonebook-app.md) |
| `hybrid` | 优秀 | 否 | **本演练** |
| `spa` | 优先级低 | 否 | [Vue SPA](./vue-spa-app.md) |

---

## 相关文档

- [模板](../basic/templates.md)
- [CLI `theme:frontend`](../start/cli-reference.md)

---

[← 返回索引](../README.md)

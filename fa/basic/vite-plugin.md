# @pinooxhq/vite-plugin

[← بازگشت به فهرست](../README.md)

**[@pinooxhq/vite-plugin](https://www.npmjs.com/package/@pinooxhq/vite-plugin)** تم پینوکس را به PHP وصل می‌کند: فایل dev state، proxy در dev، refresh برای Twig/PHP و manifest تولید برای `vite_tags()`.

در هر تم Vite (`apps/{package}/theme/{theme}/`) نصب کنید. CLI پینوکس (`php pinoox fe dev`، `pinx fe:dev`) این پکیج npm را انتظار دارد — نه stub قدیمی `vite.pinoox.mjs`.

---

## نحوه کار

| سرور | پیش‌فرض | نقش |
|--------|---------|------|
| PHP | `:8000` | پوسته Twig، routeها، API |
| Vite | `:5173` | JS/CSS با HMR |

```
مرورگر → PHP (Twig + vite_tags)
              ↓
         .pinoox/dev.json فعال + حالت HMR؟
         بله → کلاینت Vite + entry از origin Vite
         خیر → دارایی hash‌شده از dist/.vite/manifest.json
```

`pinoox()` در `vite.config.js`:

1. **`.pinoox/dev.json`** می‌نویسد تا PHP، HMR را فعال کند (جایگزین `dist/hot` قدیمی).
2. routeهای اپ را به PHP proxy می‌کند (`VITE_DEV_PROXY`).
3. با تغییر **Twig** یا **PHP اپ**، reload کامل می‌دهد.
4. **entryهای build** و **manifest** را برای production تنظیم می‌کند.

**همیشه URL اپ PHP** که `fe dev` چاپ می‌کند را باز کنید — نه مستقیم `http://127.0.0.1:5173`.

برای دستورات CLI، متغیرهای env و dev چند اپ به [فرانت‌اند و Vite](./frontend-vite.md) مراجعه کنید.

---

## نصب

داخل پوشه تم:

```bash
npm install -D @pinooxhq/vite-plugin vite
```

یا از ریشه پروژه پینوکس:

```bash
php pinoox fe install com_my_shop --theme=default
php pinoox fe spark install                 # میانبر نام پوشه تم
```

`fe install` / `fe dev` می‌تواند نسخه پلاگین متناسب با release پینوکس را همگام کند. برای `vite.config.js` قدیمی که هنوز `vite.pinoox.mjs` import می‌کند از `--fix-vite` استفاده کنید.

**اسکریپت‌های `package.json`:**

```json
{
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "watch": "vite build --watch"
  }
}
```

---

## `vite.config.js` حداقلی

### JavaScript ساده (vanilla)

```js
import { defineConfig } from 'vite';
import pinoox from '@pinooxhq/vite-plugin';

export default defineConfig({
    plugins: [
        pinoox(['src/main.js']),
    ],
});
```

### Vue

```js
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import pinoox from '@pinooxhq/vite-plugin';
import { pinooxVueTemplateOptions } from '@pinooxhq/vite-plugin/vue';

export default defineConfig({
    plugins: [
        pinoox(['src/main.js']),
        vue(pinooxVueTemplateOptions()),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
});
```

`pinooxVueTemplateOptions()` URL دارایی‌ها در قالب Vue SFC را درست می‌کند وقتی HTML از PHP و دارایی از Vite سرو می‌شود (origin متفاوت در dev).

### React

```js
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import pinoox from '@pinooxhq/vite-plugin';

export default defineConfig({
    plugins: [
        pinoox(['src/main.jsx']),
        react(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
});
```

در تم React **`@pinooxhq/vite-plugin/vue` import نکنید**.

### چند entry (JS + CSS)

```js
pinoox([
    'src/main.js',
    'src/assets/styles/app-view-error.scss',
])
```

Twig باید همه entryها را لیست کند:

```twig
{{ vite_tags(['src/main.js', 'src/assets/styles/app-view-error.scss'])|raw }}
```

---

## API تابع `pinoox()`

```js
// یک entry
pinoox('src/main.js')

// چند entry
pinoox(['src/main.js', 'src/assets/styles/app.css'])

// پیکربندی کامل
pinoox({
    entries: ['src/main.js'],
    refresh: true,              // true | false | string[] (globهای Twig)
    hotFile: '.pinoox/dev.json',
    env: { VITE_DEV_PORT: '5174' },
    build: { rollupOptions: { /* ادغام */ } },
    server: { /* ادغام */ },
})
```

| قابلیت | توضیح |
|---------|-------------|
| Build entries | `build.rollupOptions.input` از مسیرهای شما |
| Manifest | `build.manifest: true` → `dist/.vite/manifest.json` |
| Dev state | نوشتن `.pinoox/dev.json` برای HMR در PHP (legacy: `dist/hot`) |
| Dev proxy | فوروارد routeها به PHP (`VITE_SERVER_URL`، `VITE_DEV_PROXY`) |
| Twig refresh | reload کامل با تغییر `*.twig` تم |
| PHP refresh | reload کامل با تغییر مسیرهای `VITE_DEV_REFRESH` (از `fe dev`) |
| Dev assets | بازنویسی `/src/`، `/node_modules/`، … به origin Vite |

پلاگین‌های فریمورک (Vue، React، Tailwind، …) را **بعد از** `pinoox()` در آرایه `plugins` قرار دهید.

---

## exportهای پکیج

| Import | کاربرد |
|--------|---------|
| `@pinooxhq/vite-plugin` | `pinoox()` پیش‌فرض |
| `@pinooxhq/vite-plugin/vue` | `pinooxVueTemplateOptions()`، wrapper اختیاری `vue()` |

---

## یکپارچه‌سازی Twig

| بخش | نقش |
|-------|------|
| `pinoox_bootstrap()` | `window.__PINOOX__` — URLها، locale، props صفحه |
| `vite_tags('src/main.js')` | اسکریپت HMR در dev؛ تگ hash‌شده در production |
| `vite_asset('src/logo.png')` | URL استاتیک نسخه‌دار از manifest |
| `frontend.config.php` | stack، entry، مسیر manifest، port dev |

مثال `partials/scripts.twig`:

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/main.js')|raw }}
```

---

## گردش کار توسعه

```bash
# یک دستور — PHP + Vite + env (پیشنهادی)
php pinoox fe spark dev
# یا میانبر:
php pinoox dev spark

# پلتفرم — چند اپ
php pinoox fe dev:apps

# دستی — دو ترمینال
php pinoox serve --app=com_my_shop@/
cd apps/com_my_shop/theme/default && npm run dev
```

`fe dev` تا آماده شدن Vite صبر می‌کند، سپس URL PHP را چاپ می‌کند. ویرایش Twig → reload کامل. ویرایش JS/CSS → HMR.

---

## بیلد production

```bash
php pinoox fe build com_my_shop
# یا:
cd apps/com_my_shop/theme/default && npm run build
```

خروجی: `dist/.vite/manifest.json` و دارایی‌های hash‌شده زیر `dist/assets/`. بدون `.pinoox/dev.json` فعال — PHP فقط از manifest استفاده می‌کند.

---

## `vite.pinoox.mjs` قدیمی

تم‌های قدیمی `pinooxHot`، `pinooxServer` و `pinooxRefresh` را از فایل همگام‌شده `vite.pinoox.mjs` import می‌کردند. تم‌های جدید باید از پکیج npm استفاده کنند:

```bash
npm install -D @pinooxhq/vite-plugin
php pinoox fe spark dev --fix-vite
```

exportهای سطح پایین (`pinooxHot`، `pinooxServer`، …) همچنان از `@pinooxhq/vite-plugin` برای setupهای پیشرفته در دسترس‌اند.

---

## مستندات مرتبط

- [فرانت‌اند و Vite](./frontend-vite.md) — CLI، env، HMR در مقابل manifest
- [قالب Twig](./templates.md)
- [Pinx CLI — دستورات فرانت‌اند](../start/pinx-cli.md)
- [README پکیج npm](https://github.com/pinoox/vite-plugin)

---

[← بازگشت به فهرست](../README.md)

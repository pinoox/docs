# فرانت‌اند و Vite

[← بازگشت به فهرست](../README.md)

تم‌های پینوکس می‌توانند فرانت‌اند **Vite** (Vue، React یا vanilla JS) داشته باشند. PHP قالب Twig را رندر می‌کند؛ Vite دارایی‌های سمت کلاینت را build و serve می‌کند. دستور `php pinoox fe` (نام مستعار `theme:frontend`) URLهای dev، hot reload و manifestهای production را وصل می‌کند.

---

## ساختار تم

```
apps/com_my_shop/theme/default/
├── frontend.config.php    # stack، manifest، overrideهای dev
├── package.json
├── vite.config.js
├── vite.pinoox.mjs        # در fe dev/build از pincore همگام می‌شود
├── .env                   # overrideهای اختیاری Vite (به‌صورت پیش‌فرض تغییر نمی‌کند)
├── dist/
│   ├── hot                # در dev توسط Vite نوشته می‌شود (سیگنال HMR برای PHP)
│   └── .vite/manifest.json
├── src/
└── partials/scripts.twig
```

`frontend.config.php` منبع حقیقت سمت PHP برای stack، entryها، مسیر manifest و تنظیمات dev است. برای بلوک `frontend` در سطح اپ به [app manifest](../start/app-manifest.md) مراجعه کنید.

---

## CLI — `php pinoox fe`

از **ریشه پلتفرم** اجرا کنید. package را حذف کنید تا از لیست انتخاب کنید.

| Action | Command | Purpose |
|--------|---------|---------|
| `info` | `php pinoox fe info {package}` | stack، manifest، فایل hot، wiring Vite |
| `install` | `php pinoox fe install {package}` | `npm install` / `npm ci` در تم |
| `dev` | `php pinoox fe dev {package}` | PHP `serve` + Vite HMR (URLهای zero-config) |
| `dev:apps` | `php pinoox fe dev:apps` | یک `serve` PHP + Vite برای **چند** اپ |
| `build` | `php pinoox fe build {package}` | build تولید (`dist/`) |
| `watch` | `php pinoox fe watch {package}` | rebuild با ذخیره (بدون HMR) |
| `scaffold` | `php pinoox fe scaffold {package} vue` | کپی stubهای vue/react/vite در تم |

**نام‌های مستعار:** `theme:frontend`، `frontend`.

### گزینه‌های `fe dev`

| Option | Description |
|--------|-------------|
| `--no-serve` | فقط Vite؛ PHP را خودتان اجرا کنید (MAMP، Apache و غیره) |
| `--serve-host` | host سرور dev PHP (پیش‌فرض از `SERVER_HOST`) |
| `--serve-port` | port سرور dev PHP (پیش‌فرض از `SERVER_PORT`) |
| `--serve-app` | اپ قفل‌شده برای `php pinoox serve` (پیش‌فرض: package فعلی) |
| `--fix-vite` | اتصال خودکار `pinooxHot` / `pinooxServer` در `vite.config.js` |
| `--env-file` | نام فایل env تم (پیش‌فرض `.env`) |
| `--no-install` | رد کردن npm install |
| `--install` | اجبار npm install |

### `fe dev:apps` — چند اپ همزمان

وقتی روی **بیش از یک اپ** در یک پلتفرم کار می‌کنید (مثلاً welcome در `/` و manager در `/manager`)، از این دستور استفاده کنید. یک ترمینال یک `php pinoox serve` مشترک و برای هر اپ یک سرور Vite — هر کدام روی port خودش.

```bash
# تعاملی — جدول packageها، سپس شماره یا نام را تایپ کنید
php pinoox fe dev:apps

# نام package صریح (فقط نام کامل com_*)
php pinoox fe dev:apps com_pinoox_manager,com_pinoox_welcome
php pinoox fe dev:apps --apps=com_pinoox_manager,com_pinoox_welcome
```

**ورودی تعاملی** (بعد از جدول):

| ورودی | نتیجه |
|--------|--------|
| `1,7` | اپ‌ها با شماره ردیف جدول |
| `com_pinoox_manager,com_pinoox_welcome` | نام کامل package (جدا شده با کاما) |
| `all` | همه اپ‌هایی که تم فرانت‌اند دارند |

نام‌های کوتاه مثل `manager` یا `welcome` **پذیرفته نمی‌شوند** — از نام کامل package (`com_*`) استفاده کنید.

| Option | Description |
|--------|-------------|
| `--apps` | لیست package با کاما (برای اسکریپت / CI) |
| `--serve-host` | host سرور dev PHP |
| `--serve-port` | port سرور dev PHP (پیش‌فرض `8000`) |
| `--fix-vite` | اتصال خودکار `vite.config.js` برای هر تم |
| `--no-install` | رد کردن npm install |

CLI برای هر اپ یک URL چاپ می‌کند و لاگ Vite را با پیشوند می‌نویسد (`[manager]`، `[welcome]`، …). **Ctrl+C** سرور PHP و همه پروسه‌های Vite را متوقف می‌کند.

وقتی portهای پیش‌فرض تداخل دارند، در `frontend.config.php` هر تم port منحصربه‌فرد بدهید:

```php
'dev' => ['port' => 5174],
```

`dev-stack` نام مستعار منسوخ‌شده برای `dev:apps` است.

**دو بار** `fe dev` بدون `--no-serve` اجرا نکنید — هر دو port `8000` را می‌گیرند و فقط یک اپ route می‌شود. از `fe dev:apps` استفاده کنید، یا: یک `php pinoox serve` به‌علاوه `fe dev {package} --no-serve` برای هر اپ.

**گردش کار:** **URL اپ PHP** را در مرورگر باز کنید (مثلاً `http://127.0.0.1:8000/manager`)، نه port Vite. CLI یک URL اپلیکیشن چاپ می‌کند؛ خطوط URL Vite مخفی هستند. وقتی فایل hot وجود دارد، PHP تگ‌های HMR را inject می‌کند.

---

## متغیرهای محیطی

### زمان اجرا (پیش‌فرض)

در `fe dev`، پینوکس URLهای dev را از **روتر اپ** (mount path، پیشوندهای proxy) resolve می‌کند و مقادیر خالی `VITE_*` را به پروسه npm می‌دهد. **فایل `.env` تم تغییر نمی‌کند** مگر اینکه opt-in کنید (پایین).

مقادیر موجود در `.env` تم همیشه برنده‌اند. مقادیر resolve‌شده خودکار فقط کلیدهای خالی را در runtime پر می‌کنند.

| Variable | Purpose |
|----------|---------|
| `VITE_HOT_FILE` | مسیر نسبی به فایل hot (پیش‌فرض `dist/hot`) |
| `VITE_SERVER_URL` | base URL اپ PHP (برای proxy Vite) |
| `VITE_DEV_PORT` | port سرور dev Vite |
| `VITE_DEV_SERVER` | URL کامل origin Vite |
| `VITE_DEV_PROXY` | mount pathهای جدا شده با کاما برای proxy |
| `VITE_DEV_REFRESH` | globهای watch اضافی (توسط CLI برای Flow، routes، Controller) |
| `VITE_DEV` | در dev توسط CLI روی `true` تنظیم می‌شود |
| `PINOOX_CORE_PATH` | مسیر pincore (برای stubهای مشترک) |

### ذخیره بلوک autogenerated (اختیاری)

به `.env` تم اضافه کنید:

```env
ENV_SERVER_SYNC=true
```

وقتی `true` باشد، هر اجرای `fe dev` یک بلوک علامت‌گذاری‌شده در فایل env می‌نویسد یا به‌روز می‌کند:

```env
# @pinoox-fe-dev autogenerated
VITE_HOT_FILE=dist/hot
VITE_SERVER_URL=http://127.0.0.1:8000/manager
VITE_DEV_PORT=5173
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_DEV_PROXY=/manager
# @pinoox-fe-dev end
```

پیش‌فرض `false` است (یا کلید را حذف کنید). کلیدهای دستی **خارج از** بلوک هرگز حذف نمی‌شوند.

از `--env-file=.env.local` برای فایل دیگر استفاده کنید.

---

## overrideهای dev در `frontend.config.php`

تشخیص مبتنی بر روتر برای بیشتر نصب‌های چند اپ کافی است. وقتی mount یا portها متفاوت‌اند override کنید:

```php
<?php

return [
    'stack' => 'vue',
    'manifest' => 'dist/.vite/manifest.json',
    'dev' => [
        'port' => 5174,
        'hot' => 'dist/hot',
        'server_url' => 'http://127.0.0.1:8000/my-shop',
        'proxy' => ['/my-shop', '/api'],       // جایگزین لیست خودکار
        'proxy_extra' => ['/uploads'],          // با لیست خودکار ادغام می‌شود
    ],
];
```

`dev.server_url` و `dev.proxy` بر تشخیص روتر برای targetهای proxy Vite اولویت دارند.

---

## `vite.pinoox.mjs`

در `fe dev` / `fe build` به تم همگام می‌شود. از `vite.config.js` import کنید:

```js
import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import {
    pinooxHot,
    pinooxServer,
    pinooxRefresh,
    pinooxDevAssets,
    pinooxVueTemplateOptions,
    createPinooxViteConfig,
} from './vite.pinoox.mjs';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            vue(pinooxVueTemplateOptions()),
            pinooxHot({ env }),
            pinooxDevAssets(env),
            pinooxRefresh(true, env),
        ],
        server: pinooxServer(env),
    };
});
```

یا از factory استفاده کنید:

```js
import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import { createPinooxViteConfig } from './vite.pinoox.mjs';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return createPinooxViteConfig({
        env,
        stack: 'vue',
        plugins: [vue()],
    });
});
```

| Export | Role |
|--------|------|
| `pinooxHot` | فایل hot می‌نویسد تا PHP HMR را فعال کند |
| `pinooxServer` | port، proxy، `origin`، `printUrls: false` |
| `pinooxRefresh` | reload کامل وقتی Twig یا PHP اپ تغییر کند |
| `pinooxDevAssets` | `/src/...` را در dev به origin Vite بازنویسی می‌کند |
| `pinooxVueTemplateOptions` | URL دارایی SFC Vue روی origin Vite |
| `createPinooxViteConfig` | factory zero-config برای موارد بالا |

`pinooxServer` مقدار `server.origin` را روی URL Vite تنظیم می‌کند تا دارایی‌ها درست بارگذاری شوند وقتی HTML از PHP زیر mount path (مثلاً `/manager`) سرو می‌شود.

### reload کامل صفحه در dev

`pinooxRefresh` به‌صورت پیش‌فرض watch می‌کند:

- فایل‌های `**/*.twig` تم
- `Flow/` اپ (middleware)
- `routes/` اپ
- `Controller/` اپ

CLI globهای مطلق PHP را از طریق `VITE_DEV_REFRESH` inject می‌کند. `env` را به `pinooxRefresh(true, env)` بدهید تا مسیرهای اضافی اعمال شوند.

---

## helperهای Twig

به‌صورت سراسری در viewهای Twig ثبت شده‌اند:

| Helper | Purpose |
|--------|---------|
| `vite_tags('src/main.js')` | تگ‌های dev HMR یا production `<script>` / `<link>` |
| `vite_tags(['src/a.js', 'src/b.scss'])` | چند entry |
| `vite_css_tags(...)` | فقط تگ‌های stylesheet |
| `vite_js_tags(...)` | فقط تگ‌های script |
| `vite_asset('src/logo.png')` | URL نسخه‌دار از manifest |

مثال `main.twig`:

```twig
<head>
    {{ vite_tags('src/main.js')|raw }}
</head>
```

صفحات hybrid (Twig + widget):

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/widgets/price-calculator.js')|raw }}
```

**Production:** `php pinoox fe build {package}` را اجرا کنید تا `dist/.vite/manifest.json` وجود داشته باشد. Twig نام فایل‌های hash‌شده را از manifest می‌خواند.

**Development:** وقتی `dist/hot` وجود دارد و runtime در production نیست، helperها تگ script را به سرور dev Vite اشاره می‌دهند.

---

## dev در مقابل production

| Mode | `APP_ENV` | Hot file | Manifest | Browser URL |
|------|-----------|----------|----------|-------------|
| Dev | `development` (و غیره) | `dist/hot` موجود | وقتی hot هست نادیده گرفته می‌شود | URL اپ PHP |
| Prod | `production` | نادیده گرفته می‌شود | `dist/.vite/manifest.json` | URL اپ PHP |

وقتی `APP_ENV=production` باشد، پینوکس **هرگز** Vite HMR را فعال نمی‌کند — حتی اگر `dist/hot` از جلسه قبلی `fe dev` باقی مانده باشد. دارایی‌های build از manifest همیشه استفاده می‌شوند.

`php pinoox serve` همان قانون را رعایت می‌کند: در حالت production، build سرو می‌شود نه سرور dev.

---

## mount path و چند اپ

در نصب پلتفرم، `fe dev` mount روتر هر اپ را می‌خواند (مثلاً `com_pinoox_manager` → `/manager`). `VITE_SERVER_URL` می‌شود `http://host:port/manager` و پیشوندهای proxy آن مسیر را شامل می‌شوند.

برای **دو اپ یا بیشتر همزمان**، از `php pinoox fe dev:apps` استفاده کنید (بالا). هر package یک `FrontendDevSession`، port Vite و فایل `dist/hot` جدا دارد؛ PHP یک‌بار از طریق روتر کامل سرو می‌شود.

وقتی تشخیص روتر اشتباه است با `frontend.config.php` یا مقادیر دستی `.env` override کنید.

---

## مستندات مرتبط

- [قالب Twig](./templates.md)
- [View — ویو](./views.md)
- [مرجع CLI — `theme:frontend`](../start/cli-reference.md)
- [راهنمای Vite hybrid](../examples/vite-hybrid-app.md)
- [راهنمای Vue SPA](../examples/vue-spa-app.md)

---

[← بازگشت به فهرست](../README.md)

# فرانت‌اند و Vite

[← بازگشت به فهرست](../README.md)

تم‌های پینوکس می‌توانند فرانت‌اند **Vite** (Vue، React یا vanilla JS) داشته باشند. PHP قالب Twig را رندر می‌کند؛ Vite دارایی‌های سمت کلاینت را build و serve می‌کند. دستور `php pinoox fe` (نام مستعار `theme:frontend`) URLهای dev، hot reload و manifestهای production را وصل می‌کند. تم‌ها از پکیج npm [**@pinooxhq/vite-plugin**](./vite-plugin.md) در `vite.config.js` استفاده می‌کنند.

---

## ساختار تم

```
apps/com_my_shop/theme/default/
├── frontend.config.php    # stack، manifest، overrideهای dev
├── package.json           # @pinooxhq/vite-plugin در devDependencies
├── vite.config.js         # pinoox() از @pinooxhq/vite-plugin
├── .env                   # overrideهای اختیاری Vite (به‌صورت پیش‌فرض تغییر نمی‌کند)
├── dist/
│   ├── hot                # در dev توسط Vite نوشته می‌شود (سیگنال HMR برای PHP)
│   └── .vite/manifest.json
├── src/
└── partials/scripts.twig
```

`frontend.config.php` منبع حقیقت سمت PHP برای stack، entryها، مسیر manifest و تنظیمات dev است. برای بلوک `frontend` در سطح اپ به [app manifest](../start/app-manifest.md) مراجعه کنید. متادیتا و ارث‌بری تم در [theme.php](./theme-manifest.md) است.

---

## پروفایل‌های فرانت‌اند

برای هر تم **یک پروفایل** انتخاب کنید. پروفایل می‌گوید تم *چطور* رندر می‌شود — نه کدام کتابخانه JS.

| پروفایل | کاربرد | رندر | SEO | Node در production |
|---------|--------|------|-----|-------------------|
| **`twig`** | لندینگ، محتوا، صفحات ساده | HTML کامل Twig | عالی | خیر |
| **`hybrid`** | فروشگاه عمومی، بلاگ، کاتالوگ | Twig + جزیره‌های Vite | عالی (meta در PHP) | خیر |
| **`spa`** | پنل ادمین، داشبورد | شِل Twig + روتر کلاینت | لازم نیست (پشت auth) | خیر |
| **`ssg`** | صفحات بازاریابی استاتیک | پیش‌رندر در build | عالی | خیر (فقط زمان build) |

درخت تصمیم:

```text
پنل ادمین است؟
  بله → spa
  خیر → SEO مهم است؟
          خیر → twig
          بله → UI کلاینت سنگین در صفحات عمومی؟
                  خیر → twig
                  بله → SPA کامل روی URLهای عمومی؟
                          بله → ssg (یا hybrid اگر مسیرها کم‌اند)
                          خیر → hybrid
```

`'profile' => 'spa'` (و غیره) را در `frontend.config.php` بگذارید یا از `app.php` → `frontend` override کنید.

---

## تم‌های چندکانتکست

site + panel (یا بیشتر) هر کدام می‌توانند پوشه تم و بلوک `frontend` جدا داشته باشند. Flowهای `theme.site` / `theme.panel` را روی مسیرهای HTML بگذارید — [کانتکست تم](./theme-contexts.md).

---

## CLI — `php pinoox fe`

از **ریشه پلتفرم** اجرا کنید. package را حذف کنید تا از لیست انتخاب کنید.

**میانبر:** `php pinoox dev {package}` به `fe {package} dev` فوروارد می‌شود با همان گزینه‌ها (`--no-serve`، `--network`، `--fix-vite`، …).

| Action | Command | Purpose |
|--------|---------|---------|
| `info` | `php pinoox fe info {package}` | stack، manifest، فایل hot، wiring Vite |
| `install` | `php pinoox fe install {package}` | `npm install` / `npm ci` در تم |
| `dev` | `php pinoox fe dev {package}` | PHP `serve` + Vite HMR (تا آماده شدن Vite صبر می‌کند) |
| `dev` | `php pinoox dev {package}` | همان `fe dev` (میانبر) |
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
| `--serve-app` | اپ قفل‌شده برای `php pinoox serve` (پیش‌فرض: `package@/` برای dev تک‌اپ) |
| `--network` / `-N` | bind PHP + Vite روی LAN (`0.0.0.0`) |
| `--vite-host` | host bind Vite (پیش‌فرض `127.0.0.1`) |
| `--vite-network` | bind Vite روی `0.0.0.0` برای LAN |
| `--verbose-vite` | نمایش URLهای کامل راه‌اندازی Vite |
| `--fix-vite` | اتصال خودکار `@pinooxhq/vite-plugin` در `vite.config.js` |
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

**گردش کار:** CLI تا آماده شدن Vite صبر می‌کند، سپس URLها را چاپ می‌کند. **URL اپ PHP** را در مرورگر باز کنید (مثلاً `http://127.0.0.1:8000/manager` برای روتر پلتفرم، یا `http://127.0.0.1:8000/` برای `fe dev com_pinoox_manager` تک‌اپ)، **نه** port Vite. وقتی حالت HMR فعال است و فایل hot وجود دارد، PHP تگ‌های HMR را inject می‌کند.

**dev تک‌اپ** package را روی `/` mount می‌کند (`package@/`). **dev پلتفرم** از روتر کامل استفاده می‌کند — برای HMR چند اپ همزمان از `fe dev:apps` استفاده کنید.

---

## HMR در مقابل manifest (`serve` در مقابل `fe dev`)

PHP با `PINOOX_VITE_HMR` و بررسی runtime بین HMR و manifest تولید انتخاب می‌کند:

| دستور | `PINOOX_VITE_HMR` | PHP سرو می‌کند | Twig `vite_tags()` |
|---------|-------------------|----------------|-------------------|
| `php pinoox fe dev` / `php pinoox dev` | `1` | HMR از `dist/hot` + Vite | سرور dev Vite |
| `php pinoox serve` | `0` | فقط دارایی build‌شده | `dist/.vite/manifest.json` |
| `pinx dev` (تک‌اپ) | `1` وقتی استک Vite تنظیم باشد | همان `fe dev` | HMR |
| `pinx dev --no-frontend` | `0` | فقط manifest | دارایی build‌شده |

`php pinoox serve` هرگز HMR را فعال نمی‌کند — حتی اگر `dist/hot` از جلسه dev قبلی باقی مانده باشد. برای live reload از `fe dev` استفاده کنید.

وقتی `APP_ENV=production` باشد، پینوکس همیشه از manifest استفاده می‌کند بدون توجه به `dist/hot`.

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

## `@pinooxhq/vite-plugin`

در تم نصب کنید و `pinoox()` را از `vite.config.js` فراخوانی کنید:

```js
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import pinoox from '@pinooxhq/vite-plugin';
import { pinooxVueTemplateOptions } from '@pinooxhq/vite-plugin/vue';

export default defineConfig({
    plugins: [
        pinoox(['src/main.js']),
        vue(pinooxVueTemplateOptions()),
    ],
});
```

`pinoox()` جایگزین الگوی قدیمی import کردن `pinooxHot`، `pinooxServer` و `pinooxRefresh` از فایل همگام‌شده `vite.pinoox.mjs` است. برای migrate کردن config قدیمی از `php pinoox fe dev --fix-vite` استفاده کنید.

API کامل، مثال‌های stack (React، vanilla، چند entry) و راه‌اندازی npm: [**@pinooxhq/vite-plugin**](./vite-plugin.md).

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

`php pinoox serve` مقدار `PINOOX_VITE_HMR=0` را تنظیم می‌کند و دارایی‌های build‌شده را از manifest سرو می‌کند — نه سرور dev Vite. برای HMR از `fe dev` استفاده کنید.

---

## mount path و چند اپ

در نصب پلتفرم، `fe dev` mount روتر هر اپ را می‌خواند (مثلاً `com_pinoox_manager` → `/manager`). `VITE_SERVER_URL` می‌شود `http://host:port/manager` و پیشوندهای proxy آن مسیر را شامل می‌شوند.

برای **دو اپ یا بیشتر همزمان**، از `php pinoox fe dev:apps` استفاده کنید (بالا). هر package یک `FrontendDevSession`، port Vite و فایل `dist/hot` جدا دارد؛ PHP یک‌بار از طریق روتر کامل سرو می‌شود.

وقتی تشخیص روتر اشتباه است با `frontend.config.php` یا مقادیر دستی `.env` override کنید.

---

## مستندات مرتبط

- [@pinooxhq/vite-plugin](./vite-plugin.md)
- [قالب Twig](./templates.md)
- [کانتکست تم](./theme-contexts.md)
- [مانیفست تم (`theme.php`)](./theme-manifest.md)
- [View — ویو](./views.md)
- [CLI وابستگی‌ها (`deps`)](../start/deps-cli.md)
- [مرجع CLI — `theme:frontend`](../start/cli-reference.md)
- [راهنمای Vite hybrid](../examples/vite-hybrid-app.md)
- [راهنمای Vue SPA](../examples/vue-spa-app.md)

---

[← بازگشت به فهرست](../README.md)

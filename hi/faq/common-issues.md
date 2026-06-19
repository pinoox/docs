# Common Issues

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox पर installation, runtime, और development के दौरान frequent errors के practical fixes। हर section **एक approach** recommend करता है।

---

## `composer install` fails

**Symptoms:** missing extension, low PHP version, or network timeout.

**Fix:**

1. PHP 8.2+ और extensions `mysqli`, `zip`, `mbstring`, `json` enable करें।
2. Install से पहले platform check चलाएँ:

```bash
php launcher/check.php
```

3. फिर install:

```bash
composer install --no-interaction
```

Shared hosting पर PATH में `composer` न हो तो vendor locally build करके upload करें।

---

## Permission errors (file access)

**Symptoms:** Cannot write to `cache/`, `storage/`, `pinker/`.

**Fix (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

Web server user (जैसे `www-data` या `apache`) writable folders में write कर सके। Windows/MAMP पर project folder `Program Files` के बाहर रखें।

---

## `.htaccess` / rewrite not working

**Symptoms:** 404 on all URLs except `index.php`; API does not return JSON in the browser.

**Fix:**

1. Apache `mod_rewrite` enable करें।
2. DocumentRoot के लिए `AllowOverride All` set करें।
3. Project root में `.htaccess` exists हो।
4. Quick test: `http://localhost/pinoox/api/v1/ping` — JSON दिखे तो rewrite works।

nginx पर `.htaccess` की जगह server config में `try_files` और `index.php` rules लिखें।

---

## Database connection fails

**Symptoms:** `SQLSTATE[HY000] [2002] Connection refused` or access denied.

**Fix:**

1. MySQL/MariaDB running हो।
2. `config/database.config.php` या `.env` में values check करें:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Database पहले create करें (`CREATE DATABASE ... utf8mb4`)।
4. cPanel पर host `localhost` न हो सकता — panel से hostname उपयोग करें।

---

## Pinker rebuild required

**Symptoms:** stale config or routes; changes to `app.php` are not applied.

**Fix:**

```bash
php pinoox pinker:rebuild com_my_shop
# or alias:
php pinoox bake com_my_shop

# all apps:
php pinoox pinker:rebuild all
```

Routes, config change, या production deploy के बाद rebuild आमतौर पर ज़रूरी।

---

## Route not found (404 on endpoint)

**Symptoms:** route is defined in code but you get 404.

**Fix:**

1. Route file `apps/{package}/routes/` में हो और `app.php` → `router.routes` में listed हो।
2. URL app prefix (`app:router`) से match करें:

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Pinker rebuild चलाएँ (ऊपर देखें)।
4. सही HTTP method (`GET` vs `POST`) उपयोग करें।

---

## 404 — app not resolved

**Symptoms:** default page or 404; wrong app loads.

**Fix:**

1. Path/host mapping check करें:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. `config/domain.config.php` (या relevant map) में host और path सही set करें।
3. App `app.php` में `'enable' => true` हो।
4. App folder name `'package'` से match करे (`com_my_shop` जैसा)।

---

## Tests fail

```bash
php pinoox test com_my_shop
```

- `.env.testing` with a separate DB
- migrations run: `php pinoox migrate com_my_shop`
- after `fakeApp()` → `deleteFakeApp()`

Details: [Getting started with testing](../test/getting-started.md)

---

## संबंधित docs

- [Installing Pinoox](../start/installing-pinoox.md)
- [Project structure](../start/structure.md)
- [Routers](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Database getting started](../database/getting-started.md)
- [Contact support](./contact-support.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

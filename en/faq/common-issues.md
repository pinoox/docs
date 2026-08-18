# Common Issues

[← Back to index](../README.md)

Practical fixes for frequent errors during installation, runtime, and development on Pinoox. Each section recommends **one approach**.

---

## `composer install` fails

**Symptoms:** missing extension, low PHP version, or network timeout.

**Fix:**

1. Enable PHP 8.2+ and extensions `mysqli`, `zip`, `mbstring`, `json`.
2. Run the platform check before install:

```bash
php launcher/check.php
```

3. Install again:

```bash
composer install --no-interaction
```

On shared hosting, if `composer` is not in PATH, build vendor locally and upload it.

---

## Permission errors (file access)

**Symptoms:** Cannot write to `cache/`, `storage/`, `pinker/`.

**Fix (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

The web server user (e.g. `www-data` or `apache`) must be able to write to writable folders. On Windows/MAMP, keep the project folder outside `Program Files`.

---

## `.htaccess` / rewrite not working

**Symptoms:** 404 on all URLs except `index.php`; API does not return JSON in the browser.

**Fix:**

1. Enable Apache `mod_rewrite`.
2. Set `AllowOverride All` for the DocumentRoot.
3. Ensure `.htaccess` exists in the project root.
4. Quick test: `http://localhost/pinoox/api/v1/ping` — if you see JSON, rewrite works.

On nginx, write `try_files` and `index.php` rules in the server config instead of `.htaccess`.

---

## Database connection fails

**Symptoms:** `SQLSTATE[HY000] [2002] Connection refused` or access denied.

**Fix:**

1. Ensure MySQL/MariaDB is running.
2. Check values in `config/database.config.php` or `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Create the database beforehand (`CREATE DATABASE ... utf8mb4`).
4. On cPanel, the host may not be `localhost` — use the hostname from the panel.

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

After changing routes, config, or deploying to production, a rebuild is usually required.

---

## Route not found (404 on endpoint)

**Symptoms:** route is defined in code but you get 404.

**Fix:**

1. Ensure the route file is in `apps/{package}/routes/` and listed in `app.php` → `router.routes`.
2. Match the URL with the app prefix (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Run a Pinker rebuild (see above).
4. Use the correct HTTP method (`GET` vs `POST`).

---

## 404 — app not resolved

**Symptoms:** default page or 404; wrong app loads.

**Fix:**

1. Check path/host mapping:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. Set host and path correctly in `config/domain.config.php` (or the relevant map).
3. Ensure `'enable' => true` in the app's `app.php`.
4. App folder name must equal `'package'` in `app.php` (e.g. `com_my_shop`).

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

## Staging copied from production shares the same Pinoox ID

**Symptoms:** Hub, license, or telemetry treats staging as production.

**Fix:** Delete `pinker/state/identity.php` on the copy and boot once. A new [Pinoox ID](../advanced/pinoox-id.md) is created. Copying `pinker/state/` copies the install identity on purpose.

---

## Related docs

- [Installing Pinoox](../start/installing-pinoox.md)
- [Project structure](../start/structure.md)
- [Routers](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Pinoox ID](../advanced/pinoox-id.md)
- [Database getting started](../database/getting-started.md)
- [Contact support](./contact-support.md)

---

[← Back to index](../README.md)

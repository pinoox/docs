# Install Pinoox And Pinx

[Back to index](../README.md)

The recommended way to build a Pinoox app is a **Pinx single-app project**.

Pinx gives you a normal project folder, a local dev server, DevDB for database-free development, Pinx Inspector, testing commands, and `.pinx` build/release commands.

---

## Requirements

| Tool | Version | Notes |
| --- | --- | --- |
| PHP | 8.2+ | Required |
| Composer | 2.x | Required |
| ext-zip | enabled | Required for `.pinx` packages |
| Node.js | 18+ | Optional, only for Vue/React/Vite themes |
| MySQL/PostgreSQL/SQLite | optional | DevDB is the default local development database |

---

## Option A: Install Pinx Globally

Use this if you create multiple Pinoox apps.

```bash
composer global require pinoox/pinx-cli
pinx new my-shop
cd my-shop
pinx doctor
pinx migrate
pinx dev
```

Open:

```text
http://127.0.0.1:8000
http://127.0.0.1:8000/~inspector
```

The generated `.env` is intentionally small:

```dotenv
APP_ENV=development
DB_CONNECTION=devdb
```

That is enough for local development. Add MySQL/PostgreSQL/SQLite credentials only when you want to use a real database.

---

## Option B: Use The Project Template

Use this if you do not want a global command first.

```bash
composer create-project pinoox/app my-shop
cd my-shop
php bin/pinx doctor
php bin/pinx migrate
php bin/pinx dev
```

You can still install the global `pinx` command later.

---

## What Gets Installed

A Pinx project includes:

- `pinoox/pincore` as the framework core
- `pinoox/pinx-cli` in `require-dev`
- `pinoox/devdb` in `require-dev`
- `pinoox/pinx-inspector` in `require-dev`
- a minimal `.env`
- app files at the project root
- `platform/` files for local routing and dev server integration

---

## Classic Platform Install

Use the full platform only when you need a multi-app installation with platform-level management.

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Then configure your web server and database and run the installer in the browser.

For single-app development, prefer Pinx.

---

## Next

- [Create your first app](./your-first-app.md)
- [Single-app structure](./structure.md)
- [DevDB](./devdb.md)
- [Pinx CLI](./pinx-cli.md)

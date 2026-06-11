# Installing Pinoox

[← Back to index](../README.md)

This guide covers installing Pinoox 3.x. There are two ways to start:

| Route | Best for |
|-------|----------|
| **A. Single-app with [Pinx CLI](./pinx-cli.md)** | Building one app — fastest start, no manager UI |
| **B. Full platform (classic)** | Hosting multiple apps with the graphical installer and manager |

---

## Requirements

| Tool | Version |
|------|---------|
| PHP | 8.1 or higher (with ext-mysqli, ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (optional) | 18+ — only for frontend theme builds |

---

## Route A — Single-app with Pinx CLI

Install the [Pinx CLI](./pinx-cli.md) once, create a new app, run it:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggests com_my_shop — confirm or edit in the wizard
cd my-shop
cp .env.example .env          # set DB_* if you use a database
pinx setup                    # migrate platform + app, run seeders
pinx dev                      # http://127.0.0.1:8000
```

Or without a global install, via the project template:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

Run `pinx doctor` at any time to check PHP, env, DB, and build readiness. See the full [Pinx CLI guide](./pinx-cli.md) for the day-to-day workflow and command reference.

---

## Route B — Full platform (classic)

### 1. Get the project

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Alternatively, download the latest release from [GitHub](https://github.com/pinoox/pinoox), extract it, then run `composer install`.

---

### 2. Place it in your web server

Put the project folder in your document root:

| Environment | Example path |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Set the document root to the **project root** (the folder that contains `index.php`) — not a `public` subfolder.

---

### 3. Create the database

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Run the installer

Open your browser:

```
http://localhost/pinoox
```

The system app `com_pinoox_installer` runs. The GUI steps are:

1. Check PHP requirements
2. Accept the license agreement
3. Enter database credentials
4. Create the admin account
5. Finish installation

---

### 5. After installation

Main layout:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← apps
├── vendor/pinoox/pincore/  ← core
└── config/             ← project config
```

Create your first app:

```bash
php pinoox app:create com_acme_blog
```

---

## Quick troubleshooting

| Problem | Fix |
|---------|-----|
| Blank page | Run `composer install` and check PHP error logs |
| 404 on sub-routes | Enable mod_rewrite / `.htaccess` |
| Missing extension error | Enable ext-mysqli and ext-zip in php.ini |
| Installer does not open | Verify document root and write permissions on runtime folders |

---

## Related docs

- [Pinx CLI (single-app)](./pinx-cli.md)
- [Your first app](./your-first-app.md)
- [Project structure](./structure.md)
- [What is Pinoox?](../introduction/what-is-pinoox.md)

---

[← Back to index](../README.md)

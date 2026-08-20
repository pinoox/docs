# Pinroll — Developer Quick Start

[← Back to index](../README.md)

This page is for developers who want to **ship a Pinoox app or platform to a host** without reading every technical detail first.

Full reference (all flags, PinGate, provision, …): [Pinroll — release & deploy](../deploy/pinroll.md)

---

## What is Pinroll?

Pinroll is Pinoox’s deploy tool. You install it on **your machine**. It uploads files via FTP (or SSH) and installs them on the host through **PinGate** — one file on the server: `pingate.php`.

The host does **not** need Pinroll in `vendor/`. Only `pingate.php` is required on the server.

---

## Install (once)

```bash
composer require --dev pinoox/pinroll
php pinoox pinroll:init
```

Then fill in host credentials:

| What | Where |
|------|--------|
| FTP host, user, password | `.env` with `PINROLL_*` **or** `.pinoox/pinroll.config.php` |
| Site URL + PinGate token | `.pinoox/pinroll.config.php` (after `connect`) |

`.pinoox/` is gitignored — keep secrets there.

---

## Three common situations

### A) Blank host — first time setup

The FTP folder is empty; there is no `index.php` yet.

```bash
# In .env: PINROLL_HOST, PINROLL_USER, PINROLL_PASSWORD, PINROLL_SITE
# and database: PINROLL_DB_*
php pinoox pinroll:provision
```

After success, the site is live. Later updates use `deploy`, not `provision` again.

---

### B) Site already running — connect once

```bash
php pinoox pinroll:connect
php pinoox pinroll:check
php pinoox pinroll:deploy
```

`connect` runs once: asks for FTP path and site URL, uploads `pingate.php`, and saves the token in `.pinoox/pinroll.config.php`.

---

### C) Update one app only

```bash
php pinoox pinroll:deploy --app=com_pinoox_manager
```

Or, if you set a default app in config:

```bash
php pinoox pinroll:deploy
```

---

## What does `deploy` do?

When you run `pinroll:deploy` (with remote install), you usually see:

1. **Build** — create the `.pinx` package (step progress bar)
2. **Connect FTP** — connect to the host
3. **Ensure PinGate** — verify `pingate.php`
4. **Cleanup leftovers** — remove old/partial files
5. **Upload** — send the `.pinx` with a percent bar
6. **Install** — install via PinGate

Upload only (no install):

```bash
php pinoox pinroll:push --app=com_pinoox_manager
php pinoox pinroll:install --app=com_pinoox_manager
```

---

## Configuration — keep it simple

Store the **site origin** only, not the full PinGate URL:

```text
✅ https://example.com
❌ https://example.com/pingate.php?route=
```

Pinroll adds `/pingate.php?route=` at runtime.

The **token** works like an FTP password — **one token per host**, shared with teammates:

- First developer: `pinroll:connect` → token is created and saved in the overlay
- Others: copy the same token into `.pinoox/pinroll.config.php` or `.env` (`PINROLL_TOKEN`)

See resolved config (token redacted):

```bash
php pinoox pinroll:config
```

---

## Day-to-day commands

| Task | Command |
|------|---------|
| Deploy one app | `php pinoox pinroll:deploy --app=package_name` |
| Deploy platform + all apps | `php pinoox pinroll:deploy --full` |
| Migrate after deploy | `php pinoox pinroll:setup` |
| Roll back files | `php pinoox pinroll:rollback` |
| Test connection | `php pinoox pinroll:check` |
| Update pincore only | `php pinoox pinroll:pincore` |
| Sync any folder | `php pinoox pinroll:sync --from=./path --to=remote/path` |
| Refresh pingate manually | `php pinoox pinroll:gate` |

---

## When something fails

| Symptom | Simple fix |
|---------|------------|
| `401` / Missing bearer token | Token in config does not match the host; ask a teammate or run `connect` again |
| `503` or PinGate not responding | Run `php pinoox pinroll:gate`; new deploys also auto-check pingate |
| FTP error | Run `pinroll:check`; verify `PINROLL_HOST` / `USER` / `PASSWORD` |
| Install failed | Check logs: `storage/pinroll/gate/` on your dev machine |
| Windows / MAMP HTTPS errors | Pinroll 1.5.2+ usually handles this; run `pinroll:check` again |

---

## Security

Without a token, PinGate returns `401` — outsiders cannot install or roll back.

Do not commit tokens. Keep `.pinoox/` and `.env` gitignored.

---

## After deploy

If you have migrations or patches:

```bash
php pinoox pinroll:setup
```

Or on the host (SSH):

```bash
php pinoox migrate com_pinoox_manager
php pinoox cache:build com_pinoox_manager
```

---

## More docs

- [Pinroll — release & deploy (full)](../deploy/pinroll.md)
- [Pinroll overview](../advanced/pinroll.md)
- [Common issues — Pinroll](../faq/common-issues.md#pinroll)

---

[← Back to index](../README.md)

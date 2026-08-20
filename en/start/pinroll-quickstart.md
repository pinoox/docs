# Pinroll — Developer Quick Start

[← Back to index](../README.md)

This page is for developers who want to **ship a Pinoox app or platform to a host** without reading every technical detail first.

Full reference (all flags, PinGate, provision, …): [Pinroll — release & deploy](../deploy/pinroll.md)

---

## What is Pinroll?

Pinroll is Pinoox’s deploy tool. You install it on **your machine**. It uploads via **FTP**, **SSH**, or **Pinion** (chunked HTTP) and installs on the host through **PinGate** — one file: `pingate.php`.

The host does **not** need Pinroll in `vendor/`. Only `pingate.php` is required on the server.

---

## Install (once)

```bash
composer require --dev pinoox/pinroll
php pinoox pinroll:init
```

---

## Pick a setup method

| Method | When | Command |
|--------|------|---------|
| **Zip kit** | No FTP — File Manager only | `php pinoox pinroll:kit` |
| **FTP** | Shared hosting | `php pinoox pinroll:connect --via=ftp` |
| **SSH** | VPS | `php pinoox pinroll:connect --via=ssh` |
| **FTP once → Pinion** | Bootstrap gate via FTP, then HTTP uploads | `php pinoox pinroll:connect --bootstrap-ftp` |
| **Interactive** | Not sure | `php pinoox pinroll:connect` |

### Zip kit (no FTP) — simplest for many hosts

```bash
php pinoox pinroll:kit
# → storage/pinroll/pinroll-kit-production.zip
```

Extract the zip into `public_html` (you should see `pingate.php` and `storage/pinroll/tokens/…`). Then:

```bash
php pinoox pinroll:check
php pinoox pinroll:deploy
```

Later uploads use **Pinion** (HTTP) — no FTP required.

---

## Three common situations

### A) Blank host — first time setup

Empty folder, no `index.php` yet. Usually with FTP/SSH:

```bash
# In .env: PINROLL_HOST, PINROLL_USER, PINROLL_PASSWORD, PINROLL_SITE
# and database: PINROLL_DB_*
php pinoox pinroll:provision
```

After success, later updates use `deploy`, not `provision` again.

---

### B) Site already running — connect once

```bash
php pinoox pinroll:connect          # method picker, or --via=pinion / ftp / ssh
php pinoox pinroll:check
php pinoox pinroll:deploy
```

`connect` asks for deploy path + site URL and prepares PinGate (auto-upload or kit zip). Token is saved in `.pinoox/pinroll.config.php`.

---

### C) Update one app only

```bash
php pinoox pinroll:deploy --app=com_pinoox_manager
```

---

## What does `deploy` do?

1. **Build** — create the `.pinx` package
2. **Connect** — host transport (FTP / SSH / Pinion)
3. **Ensure PinGate** — verify `pingate.php`
4. **Cleanup leftovers** — remove old/partial files
5. **Upload** — send the `.pinx`
6. **Install** — install via PinGate

Upload only (no install):

```bash
php pinoox pinroll:push --app=com_pinoox_manager
php pinoox pinroll:install --app=com_pinoox_manager
```

---

## Configuration — keep it simple

Store the **site origin** only:

```text
✅ https://example.com
❌ https://example.com/pingate.php?route=
```

**One token per host**, shared with teammates.

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
| Extract kit (no FTP) | `php pinoox pinroll:kit` |
| Update pincore only | `php pinoox pinroll:pincore` |
| Sync any folder | `php pinoox pinroll:sync --from=./path --to=remote/path` |
| Refresh pingate | `php pinoox pinroll:gate` |

`pincore` and `sync` **zip** the folder, upload with the host’s `via`, and extract on the server via PinGate (`POST ?route=sync`) — not FTP-only.

---

## When something fails

| Symptom | Simple fix |
|---------|------------|
| `401` / Missing bearer token | Token mismatch; ask a teammate or run `connect` / `kit` again |
| `503` or PinGate not responding | `php pinoox pinroll:gate`; deploys also auto-check pingate |
| FTP error | `pinroll:check`; verify `PINROLL_HOST` / `USER` / `PASSWORD` |
| `Action "…" is already registered` | Refresh pingate (`pinroll:gate`); install uses skip_cache + cache rebuild |
| Install failed | Logs: `storage/pinroll/gate/` on your dev machine |
| Windows / MAMP HTTPS errors | Pinroll 1.5.2+ usually handles this; run `pinroll:check` |

---

## Security

Without a token, PinGate returns `401`. Do not commit tokens.

---

## After deploy

```bash
php pinoox pinroll:setup
```

---

## More docs

- [Pinroll — release & deploy (full)](../deploy/pinroll.md)
- [Pinroll overview](../advanced/pinroll.md)
- [Common issues — Pinroll](../faq/common-issues.md#pinroll)

---

[← Back to index](../README.md)

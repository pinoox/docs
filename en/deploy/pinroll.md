# Pinroll — Release & Deploy

[← Back to index](../README.md)

**Pinroll** (`pinoox/pinroll`) is the official Pinoox release rollout engine. It builds app packages, ships them to remote **hosts**, installs them via **PinGate**, and supports rollback, hooks, and retention.

Pinroll is a **Composer library** — not a Pinoox app. CLI commands register automatically when the package is installed.


| Concept | Meaning |
|---------|---------|
| **Host** | Where to deploy (`production`, `staging`, …) — the config key is the name |
| **Transport (`via`)** | How to send files (`ftp`, `ssh`, `pinion`, `local`) |
| **PinGate** | HTTP entry on the host (`pingate.php` + `gate/`) for install / status / rollback |
| **Bundle** | Optional build recipe (`--bundle=…`); normal deploys auto-detect apps |

---

## Install

On a full Pinoox **platform** project, add Pinroll as a **production** dependency (required on the host for PinGate):

```bash
composer require pinoox/pinroll
```

```json
"require": {
  "php": "^8.2",
  "pinoox/pincore": "^3.8",
  "pinoox/pinroll": "^1.1"
}
```

Do **not** put `pinoox/pinroll` only in `require-dev`. Platform builds and host vendor packs strip `require-dev`, and PinGate needs Pinroll at runtime.

---

## Setup process

```mermaid
flowchart LR
    A[pinroll:init] --> B[Fill .env]
    B --> C[pinroll:connect]
    C --> D[pinroll:apps]
    D --> E[pinroll:check]
    E --> F[Ready to deploy]
```

| Step | Command | What it does |
|------|---------|--------------|
| 1 | `php pinoox pinroll:init` | Creates `pinroll/pinroll.config.php` |
| 2 | Edit `.env` | Set `PINROLL_*` FTP/SSH credentials |
| 3 | `php pinoox pinroll:connect` | Deploy path, site URL, upload PinGate |
| 4 | `php pinoox pinroll:apps` | Choose default app packages for the host |
| 5 | `php pinoox pinroll:check` | Verify transport + PinGate |
| 6 | `php pinoox pinroll:deploy` | Build, upload, and install (go live) |

```bash
php pinoox pinroll:init
# fill PINROLL_* in .env
php pinoox pinroll:connect
php pinoox pinroll:apps
php pinoox pinroll:check
php pinoox pinroll:deploy
```

---

## Project setup

```bash
php pinoox pinroll:init
```

Scaffolds:

```
pinroll/
  pinroll.config.php
```

Build recipes are auto-detected from `apps/` (no `pinroll/bundles/*.php` required for normal app deploy). Optional custom recipes: `pinroll/bundles/{name}.php` with `--bundle={name}`.

---



## Configuration



### Hosts (`pinroll/pinroll.config.php`)

```php
<?php

return [
    // Used when CLI omits the host argument
    'default_host' => 'production',

    // Global defaults — inherited by every host unless overridden
    'keep' => 2,
    'store' => 'both',      // local | remote | both
    'auto_clean' => true,   // prune beyond keep after successful install

    'hosts' => [
        'production' => [
            'deploy_path' => 'public_html',
            'via' => 'ftp',

            // Default packages for push/install (or use pinroll:apps)
            'apps' => ['com_pinoox_shop'],

            'gate' => [
                'url' => env('PINROLL_PRODUCTION_URL', ''),
                'token' => env('PINROLL_PRODUCTION_TOKEN', ''),
            ],

            'ftp' => [
                'host' => env('PINROLL_PRODUCTION_HOST', ''),
                'user' => env('PINROLL_PRODUCTION_USER', ''),
                'password' => env('PINROLL_PRODUCTION_PASSWORD', ''),
            ],

            'hooks' => [
                'before_install' => ['php pinoox migrate --force'],
                'after_install' => ['php pinoox cache:build'],
            ],
        ],
    ],
];
```


| Key                             | Description                                                     |
| ------------------------------- | --------------------------------------------------------------- |
| `default_host`                  | Host used when CLI omits the host name                          |
| `deploy_path`                   | Deploy root relative to FTP/SSH login                           |
| `hostname`                      | Optional connection address when it differs from transport host |
| `via`                           | Default transport: `ftp`, `ssh`, `pinion`, or `local`           |
| `gate.url` / `gate.token`       | PinGate credentials                                             |
| `ftp` / `ssh`                   | Connection credentials                                          |
| `apps`                          | Default app packages for push/install                           |
| `hooks`                         | Shell commands around push, install, rollback                   |
| `keep` / `store` / `auto_clean` | Retention (global or per-host)                                  |




### `.env` keys

```env
PINROLL_PRODUCTION_URL=https://example.com/pingate.php?route=
PINROLL_PRODUCTION_TOKEN=…
PINROLL_PRODUCTION_HOST=ftp.example.com
PINROLL_PRODUCTION_USER=…
PINROLL_PRODUCTION_PASSWORD=…
```

`pinroll:connect` / `pinroll:gate` write URL + token into `.env` when needed.

---



## Apps selection

If `hosts.*.apps` is empty and you do not pass `--app` / `--apps`, interactive push/deploy prompts for packages.

Set defaults once:

```bash
php pinoox pinroll:apps                         # interactive picker
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:apps --all
php pinoox pinroll:apps --list
php pinoox pinroll:apps --clear                 # remove apps[] (prompt again on push)
```

---



## Connect

```bash
php pinoox pinroll:connect          # ask deploy path + site URL upload PinGate and verify connection
php pinoox pinroll:connect --reset  # re-run full setup
```

When the host is already configured (`deploy_path` + gate URL + transport credentials), connect **skips setup prompts**, shows current settings, and runs connectivity checks.

---



## CLI vocabulary

```bash
# Uses default_host
php pinoox pinroll:push
php pinoox pinroll:install
php pinoox pinroll:deploy

# Explicit app / host
php pinoox pinroll:deploy --app=com_pinoox_shop
php pinoox pinroll:install staging --app=com_pinoox_shop
```


| Command           | Purpose                        |
| ----------------- | ------------------------------ |
| `pinroll:push`    | Build + upload (no install)    |
| `pinroll:install` | Install staged release on host |
| `pinroll:deploy`  | Push + install (go live)       |


---



## Local modes



### A. `via: local` — transport

Archives go to `storage/pinroll/incoming/` on this machine (no FTP/SSH).

```bash
php pinoox pinroll:push --via=local --app=com_pinoox_shop
```



### B. `pinroll:install --local` — install on this host

Run after SSH into production (site root):

```bash
php pinoox pinroll:install --local
php pinoox pinroll:install --local --list
```



### C. `store: local` / `both` — retention


| `store`            | Archives kept on                   | After install     |
| ------------------ | ---------------------------------- | ----------------- |
| `remote` (default) | Host `storage/pinroll/incoming/`   | Trimmed to `keep` |
| `local`            | Dev machine incoming + pinx export | Local trim only   |
| `both`             | Dev machine **and** host           | Both trimmed      |


With `store: local|both`, push also copies the `.pinx` into local `storage/pinroll/incoming/` for rollback re-push.

---



## Retention


| Key          | Values                      | Behavior                                      |
| ------------ | --------------------------- | --------------------------------------------- |
| `keep`       | `0`…`N`                     | Newest N kept; `0` disables trimming          |
| `store`      | `local` | `remote` | `both` | Which side(s) retain archives                 |
| `auto_clean` | bool                        | After successful install, prune beyond `keep` |


On **multi-app** `pinroll:deploy`, retention cleanup runs only after the **last** install so sibling staged releases are not deleted mid-batch.

**What local cleanup prunes**

- `storage/pinroll/incoming/*.pinx`
- `apps/{package}/pinx/export/*.pinx` (newest N per app)
- local release/session temp dirs under `storage/`

**What remote cleanup prunes**

- Host `storage/pinroll/incoming/` (via PinGate `/cleanup`)

```bash
php pinoox pinroll:cleanup              # remote (uses keep from config)
php pinoox pinroll:cleanup --local      # this machine
php pinoox pinroll:cleanup --dry-run
php pinoox pinroll:cleanup -k=2
```

---



## Hooks

```php
'hooks' => [
    'before_push' => ['npm run build'],
    'after_push' => [],
    'before_install' => ['php pinoox migrate --force'],
    'after_install' => ['php pinoox cache:build'],
    'before_rollback' => [],
    'after_rollback' => [],
],
```


| Hook                                 | Runs on                    | When                  |
| ------------------------------------ | -------------------------- | --------------------- |
| `before_push` / `after_push`         | Developer machine          | Around archive upload |
| `before_install` / `after_install`   | Remote host (or `--local`) | Around Pinx install   |
| `before_rollback` / `after_rollback` | Host / local pipeline      | Around rollback       |


---



## Rollback & migrations

`pinroll:rollback` re-installs a **previous package** (code) with force. It does **not** automatically reverse every DB migration or data patch.


| Layer                        | On rollback                                             |
| ---------------------------- | ------------------------------------------------------- |
| App files / Pinx package     | Restored from previous archive                          |
| Migrations with `down()`     | Only if you run migrate rollback explicitly (e.g. hook) |
| One-way patches / data fixes | Not undone                                              |


Practical guidance:

1. Prefer **forward-fix** releases for schema issues.
2. Write reversible migrations when rollback matters.
3. Keep `keep >= 2` (and `store: both`) so a previous archive exists.
4. Take a DB backup before risky production deploys.

```bash
php pinoox pinroll:rollback
php pinoox pinroll:rollback --deploy-id=20260710_091021_3f980930
php pinoox pinroll:migrate:dry-run
```

---



## Host vendor

PinGate and remote install need a complete platform `vendor/` on the host (including `pinoox/pinroll` and `pinoox/pincore`).

`pinroll:vendor` builds a **production** `pinroll/vendor.zip` with the same **PlatformComposer** pipeline used by `pinx:build platform`:

- Strips `require-dev` (Pest, DevDB, Inspector, …)
- Keeps production packages (including `pinoox/pinroll` when it is in `require`)
- Materializes Composer path repositories into real files

```bash
# Build zip only
php pinoox pinroll:vendor

# Build, FTP upload vendor.zip, extract on host via PinGate POST /vendor
php pinoox pinroll:vendor --push
```

| Flag | Effect |
|------|--------|
| (default) | Write `pinroll/vendor.zip` |
| `--push` | FTP upload + PinGate extract (FTP hosts) |
| `--prune` | Also prune tests/docs inside vendor (optional) |
| `-o` / `--output=` | Custom zip path |

**Recommended first-time / core update flow**

```bash
php pinoox pinroll:gate -n          # upload PinGate (includes /vendor extract route)
php pinoox pinroll:vendor --push -n
php pinoox pinroll:check
```

PinGate `POST /vendor` only accepts `vendor.zip` next to `pingate.php`, extracts **only** `vendor/` entries (zip-slip safe), rate-limits bad tokens, and deletes the zip after success.

> Prefer `pinroll:vendor --push` over `pinroll:deploy --vendor`. The `--vendor` flag on push/deploy syncs the raw local `vendor/` tree over FTP and only when no apps are being deployed.

---

## App frontend (theme dist)

App deploys run `fe:build` before `pinx:build`. Production `.pinx` packages include theme `dist/` and exclude theme `src/` / Vite tooling (even when `dist/` is gitignored).

---

## Quick start (FTP + PinGate)

```bash
php pinoox pinroll:init
# fill PINROLL_* in .env
php pinoox pinroll:connect
php pinoox pinroll:apps --apps=com_pinoox_shop
php pinoox pinroll:vendor --push   # host vendor (PlatformComposer + PinGate extract)
php pinoox pinroll:check
php pinoox pinroll:deploy
```

---

## PinGate routes

| Method | Path        | Purpose                              |
| ------ | ----------- | ------------------------------------ |
| `GET`  | `/status`   | Health / version                     |
| `GET`  | `/incoming` | List staged releases                 |
| `POST` | `/install`  | Install staged release (`/apply` BC) |
| `POST` | `/vendor`   | Extract uploaded `vendor.zip` (safe) |
| `POST` | `/rollback` | Re-install previous release          |
| `POST` | `/cleanup`  | Prune old archives                   |
| `GET`  | `/history`  | Rollout history                      |

Auth: `Authorization: Bearer {token}`.

---

## CLI reference

| Command            | Purpose                                       |
| ------------------ | --------------------------------------------- |
| `pinroll:init`     | Scaffold `pinroll/pinroll.config.php`         |
| `pinroll:connect`  | Setup / verify host (`--reset` to redo)       |
| `pinroll:apps`     | Set `hosts.*.apps` in config                  |
| `pinroll:vendor`   | Production `vendor.zip` (`--push` to host)    |
| `pinroll:gate`     | Build / upload PinGate                        |
| `pinroll:check`    | Verify host / PinGate                         |
| `pinroll:push`     | Build & upload only                           |
| `pinroll:install`  | Install staged release                        |
| `pinroll:deploy`   | Push + install                                |
| `pinroll:rollback` | Rollback via PinGate or local re-push         |
| `pinroll:cleanup`  | Prune archives (`--local`, `--dry-run`, `-k`) |

### Push / deploy options

| Flag                 | Effect               |
| -------------------- | -------------------- |
| (default)            | app `.pinx` only     |
| `--all`              | app + vendor + theme |
| `--vendor`           | FTP sync of `vendor/` (no apps in same run) |
| `--theme`            | theme dist sync      |
| `--app=` / `--apps=` | Package selection    |
| `--via=`             | Transport override   |
| `--host=`            | Host override        |


---



## Transports


| `via`    | Use case                                  |
| -------- | ----------------------------------------- |
| `ftp`    | Shared hosting — upload + PinGate install |
| `ssh`    | VPS — SFTP upload, SSH install            |
| `pinion` | Chunked HTTP upload through PinGate       |
| `local`  | Same machine / smoke tests                |


---



## Related docs

- [Pinion protocol](../advanced/pinion.md)
- [Pinx CLI](../start/pinx-cli.md)
- [CLI reference](../start/cli-reference.md)

---

[← Back to index](../README.md)
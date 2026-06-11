# Transport (shared resources)

[← Back to index](../README.md)

In the HMVC architecture, apps can share users, auth, files, and permissions with each other through the **`transport`** block in `app.php`. Without transport, each app keeps every resource **local** to its own package.

| Term | Meaning |
|------|---------|
| **`platform`** | Logical shared scope — shared DB rows use `app = platform` |
| **`pincore/`** | Physical framework folder only — **never** a transport scope value |

---

## How it works

Transport has two layers:

1. **Scenario** — a single-word preset that expands to several granular keys.
2. **Granular key** — a multi-word name for one specific shared resource.

```php
// app.php
'transport' => [
    'full' => 'platform',           // scenario preset
    'file_storage' => 'local',      // granular override
],
```

**Resolution order:** explicit granular key → matching scenario.

Granular keys always win over scenario expansion. If a key is not set and no scenario covers it, the app keeps that resource **local** (current package).

---

## Scope values

Every scenario or granular key is assigned one scope:

| Scope | Meaning |
|-------|---------|
| `local` | Current app package (default when omitted) |
| `platform` | Shared platform scope (`app = platform`, `pinx_*` tables) |
| `host` | App that opened this one (preview / `App::meeting()`) |
| `{package}` | Explicit app, e.g. `com_pinoox_manager` |

For **`auth_config`** and **`auth_cookie`**, `platform` and `{package}` resolve to the app that **provides auth settings** (typically `com_pinoox_manager` when installed).

---

## Scenarios reference

Single-word presets. Use in `app.php` as `'transport' => ['{scenario}' => '{scope}']`.

| Scenario | Description | Granular keys included |
|----------|-------------|------------------------|
| `full` | All shared resources | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Login system: accounts, auth, session tokens | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | File uploads and metadata | `file_storage` |
| `access` | Roles and permissions | `access_table` |

---

## Granular keys reference

Multi-word resource names. Use to share or override a single resource.

| Granular key | Controls | Used by |
|--------------|----------|---------|
| `user_table` | `UserModel` `app` column / global scope | User accounts |
| `auth_config` | Auth mode, JWT secret, lifetimes (`auth` block source) | `AuthConfig`, login flow |
| `auth_cookie` | Client key / cookie name (`auth.key`) | Cookie & SPA token storage |
| `session_token` | `TokenModel` `app` column / DB session rows | Session persistence |
| `file_storage` | `FileModel` `app` column / upload paths | Uploads & file metadata |
| `access_table` | Role & permission model `app` scope | `RoleModel`, `PermissionModel`, `can()` |

---

## Common setups

**Auth provider for the platform (e.g. manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Consumer app — shared everything, no local auth block:**

```php
'transport' => ['full' => 'platform'],
```

**Shared login only:**

```php
'transport' => ['user' => 'platform'],
```

**Standalone app** — omit `transport`, or pin everything locally:

```php
'transport' => ['user' => 'local'],
```

**Override one resource inside a scenario:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## Code API

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // resolved package for a granular key
Transport::authSource();                       // app that owns auth settings, or null
Transport::sharesAuthWith($guest, $host);      // cross-app auth check
Transport::resolved();                         // all granular keys → scope
Transport::activeScenarios();                  // e.g. ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Database

Platform-scoped tables use connection **`platform`** and prefix **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## Related docs

- [app.php manifest](../start/app-manifest.md)
- [User management](./user-management.md)
- [Access & permissions](./access-permissions.md)
- [File management](./file-management.md)

---

[← Back to index](../README.md)

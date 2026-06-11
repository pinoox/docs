# Transport (shared resources)

[← 색인으로 돌아가기](../README.md)

HMVC 아키텍처에서 앱은 `app.php`의 **`transport`** block을 통해 user, auth, file, permission을 서로 공유할 수 있습니다. transport 없으면 각 앱은 모든 resource를 자체 package에 **local**로 유지합니다.

| Term | Meaning |
|------|---------|
| **`platform`** | Logical shared scope — 공유 DB row는 `app = platform` |
| **`pincore/`** | Physical framework folder만 — transport scope 값으로 **사용 금지** |

---

## 동작 방식

Transport는 두 계층:

1. **Scenario** — 여러 granular key로 확장되는 단일 단어 preset.
2. **Granular key** — 하나의 shared resource용 multi-word 이름.

```php
// app.php
'transport' => [
    'full' => 'platform',           // scenario preset
    'file_storage' => 'local',      // granular override
],
```

**Resolve 순서:** explicit granular key → matching scenario.

Granular key는 항상 scenario expansion보다 우선합니다. key가 설정되지 않고 scenario가 커버하지 않으면 앱은 해당 resource를 **local**(현재 package)로 유지합니다.

---

## Scope 값

모든 scenario 또는 granular key에 하나의 scope 할당:

| Scope | Meaning |
|-------|---------|
| `local` | Current app package (생략 시 default) |
| `platform` | Shared platform scope (`app = platform`, `pinx_*` tables) |
| `host` | 이 앱을 연 앱 (preview / `App::meeting()`) |
| `{package}` | Explicit app, 예: `com_pinoox_manager` |

**`auth_config`**와 **`auth_cookie`**에서 `platform`과 `{package}`는 **auth 설정을 제공하는** 앱(설치 시 보통 `com_pinoox_manager`)으로 resolve됩니다.

---

## Scenario reference

단일 단어 preset. `app.php`에서 `'transport' => ['{scenario}' => '{scope}']`로 사용.

| Scenario | Description | Granular keys included |
|----------|-------------|------------------------|
| `full` | All shared resources | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Login system: accounts, auth, session tokens | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | File uploads and metadata | `file_storage` |
| `access` | Roles and permissions | `access_table` |

---

## Granular key reference

Multi-word resource 이름. 단일 resource 공유 또는 override.

| Granular key | Controls | Used by |
|--------------|----------|---------|
| `user_table` | `UserModel` `app` column / global scope | User accounts |
| `auth_config` | Auth mode, JWT secret, lifetimes (`auth` block source) | `AuthConfig`, login flow |
| `auth_cookie` | Client key / cookie name (`auth.key`) | Cookie & SPA token storage |
| `session_token` | `TokenModel` `app` column / DB session rows | Session persistence |
| `file_storage` | `FileModel` `app` column / upload paths | Uploads & file metadata |
| `access_table` | Role & permission model `app` scope | `RoleModel`, `PermissionModel`, `can()` |

---

## 일반적인 설정

**Platform auth provider (예: manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Consumer app — 전부 공유, local auth block 없음:**

```php
'transport' => ['full' => 'platform'],
```

**Login만 공유:**

```php
'transport' => ['user' => 'platform'],
```

**Standalone app** — `transport` 생략, 또는 전부 local:

```php
'transport' => ['user' => 'local'],
```

**Scenario 내부 하나 override:**

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

Platform-scoped table은 connection **`platform`**, prefix **`pinx_`** 사용.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## 관련 문서

- [app.php manifest](../start/app-manifest.md)
- [User management](./user-management.md)
- [Access & permissions](./access-permissions.md)
- [File management](./file-management.md)

---

[← 색인으로 돌아가기](../README.md)

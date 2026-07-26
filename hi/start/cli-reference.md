# Pinoox CLI संदर्भ

[← इंडेक्स पर वापस जाएँ](../README.md)

हर कमांड **प्रोजेक्ट रूट** से चलाएँ:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

जब किसी package की आवश्यकता हो और वह न दिया गया हो, तो Pinoox एक इंटरैक्टिव पिकर दिखाता है।

> **सिंगल-ऐप** प्रोजेक्ट्स के लिए, स्टैंडअलोन [Pinx CLI](./pinx-cli.md) का उपयोग करें (`pinx dev`, `pinx setup`, `pinx build`, …)।

---

## सामान्य उपनाम (aliases)

| Alias | कमांड |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |
| `users` | `user:list` |

| `roles` | `role:list` |

| `permissions` | `permission:list` |

| `tokens` | `token:list` |

| `files` | `file:list` |
| `pinion` | `pinion:list` |

| `databases` | `db:list` |

| `make:permission` | `permission:create` |

---

## ऐप्स

| कमांड | उद्देश्य |
|---------|---------|
| `app:create {package}` | ऐप स्कैफ़ोल्ड करें (`--simple`, `--stack`, `--profile`) |
| `app:list` | ऐप्स की सूची |
| `app:delete` | ऐप हटाएँ |
| `app:router set /path {package}` | URL मैपिंग |
| `app:domain` | होस्ट → ऐप मैप |
| `app:resolve` | सक्रिय ऐप को डीबग करें |

---

## स्कैफ़ोल्डिंग

| कमांड | आउटपुट |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest क्लास |
| `seeder:create` | `database/seeders/` |
| `test:create` | Pest फ़ाइल |
| `theme:frontend` | फ्रंटएंड टूलिंग (Vue/React/Twig) |

---

## डेटाबेस

| कमांड | उद्देश्य |
|---------|---------|
| `migrate {package}` | Migrations चलाएँ (app, `platform`, `pincore`) |
| `migrate:create` | नई migration फ़ाइल |
| `migrate:status` / `migrate:rollback` | स्थिति / रोलबैक |
| `seeder:run` | Seeders चलाएँ |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../advanced/patches.md) |
| `query` | Raw SQL (डीबग) |


### Connection management (`db:*`)



Inspect and persist platform connections (Pinker `~database`) and per-app `database` blocks.



| Command | Purpose |

|---------|---------|

| `db:list` | List platform connections or app DB settings (`--all`, `--test`, `--json`) |

| `db:show {target}` | Connection details for `platform`, a connection name, or an app package |

| `db:test {target}` | Test connectivity; ad-hoc probe with `--host`, `--database`, `--username`, … |

| `db:create {name}` | Add a platform connection (interactive or `--set key=value`) |

| `db:update {target}` | Update platform or app database settings |

| `db:prefix {package} {prefix}` | Change app table prefix (`--use` to pick platform connection) |



```bash

php pinoox db:list --test

php pinoox db:show platform

php pinoox db:show com_my_shop --json

php pinoox db:test mysql

php pinoox db:prefix com_my_shop shop_

```



> CLI writes to **Pinker**. Runtime may still override values when `.env` defines `DB_*` keys (`env-over-pinker`).



See [Database getting started](../database/getting-started.md).



---



## Users, roles & permissions



Commands respect `transport.user` / access scope (usually `platform`). Omit `{package}` to pick from the interactive list.



| Command | Purpose |

|---------|---------|

| `user:list` / `user:show` / `user:create` / `user:update` / `user:delete` | User CRUD |

| `user:password` / `user:status` / `user:role` | Password, status, role assignment |

| `role:list` / `role:create` / `role:show` / `role:update` / `role:delete` | Role CRUD |

| `role:permission` | Attach or detach permissions on a role |

| `permission:list` / `permission:create` / `permission:show` / `permission:delete` | Permission CRUD |



```bash

php pinoox user:list com_my_shop --status=active --json

php pinoox role:create com_my_shop --key=editor --name=Editor

php pinoox permission:create com_my_shop blog.posts.edit

php pinoox role:permission editor --attach=blog.posts.edit

```



See [User management](../advanced/user-management.md) and [Access & permissions](../advanced/access-permissions.md).



---



## Tokens



Manage `TokenModel` rows for the transport scope (`transport.session_token` in `app.php`).



| Command | Purpose |

|---------|---------|

| `token:list` / `token:show` | Inspect tokens (keys masked in list output) |

| `token:create` | Create token for a user (`--user`, `--lifetime`, `--unit`) |

| `token:update` / `token:delete` | Update metadata or remove one token |

| `token:revoke-user` | Revoke all tokens for a user (like `Auth::revokeSessions`) |

| `token:purge` | Delete expired tokens |



```bash

php pinoox token:list platform

php pinoox token:create com_my_shop --user=1 --lifetime=30 --unit=day

php pinoox token:revoke-user 1

```



See [Token management](../advanced/token-management.md).



---



## Files



Manage upload metadata and storage for the `FileModel` scope (`transport.file_storage`).



| Command | Purpose |

|---------|---------|

| `file:list` / `file:show` | List or inspect records (shows storage `present` / `missing`) |

| `file:update` | Update metadata, access, or links |

| `file:delete` | Remove DB row, storage, or both (`--db-only`, `--storage-only`, `--force`) |

| `file:purge` | Bulk cleanup of orphaned or old files |



```bash

php pinoox file:list com_my_shop

php pinoox file:show 12

php pinoox file:delete 12 --storage-only --force

```



See [File management](../advanced/file-management.md).

---

## Pinion (resumable uploads)

Manage in-progress chunked upload sessions (temp storage under `storage/pinion`):

| Command | Purpose |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

See [Pinion प्रोटोकॉल](../advanced/pinion.md).

---

## Cache और Pinker

| कमांड | उद्देश्य |
|---------|---------|
| `cache:build` / `cache:clear` | रनटाइम cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + config रीसेट करें |

---

## Schedule

| कमांड | उद्देश्य |
|---------|---------|
| `schedule:list` | Cron कार्यों की सूची |
| `schedule:run` | नियत (due) कार्य चलाएँ |

देखें [Schedule](../advanced/schedule.md)।

---

## Router

| कमांड | उद्देश्य |
|---------|---------|
| `route:actions {package}` | Named Actions की सूची |

---

## Pinx पैकेजिंग

| कमांड | उद्देश्य |
|---------|---------|
| `pinx:build` | `.pinx` पैकेज बनाएँ |
| `pinx:install` | पैकेज इंस्टॉल करें |
| `pinx:info` | मेटाडेटा |
| `wizard:list` / `wizard:install` | इंस्टॉल विज़ार्ड |

---

## डेवलपमेंट

| कमांड | उद्देश्य |
|---------|---------|
| `test` | Pest टेस्ट |
| `serve` | बिल्ट-इन डेव सर्वर |
| `log:view` / `log:clear` | लॉग |
| `deps` | सभी ऐप्स में Composer/npm |
| `version` / `mode:show` | संस्करण / रनटाइम मोड |

---

## Package आर्ग्युमेंट

| मान | अर्थ |
|-------|---------|
| `com_my_shop` | विशिष्ट ऐप |
| `platform` | प्लेटफ़ॉर्म migrations/patches/seeders |
| `pincore` | फ्रेमवर्क कोर |
| `all` | सभी ऐप्स (cache/pinker) |

---

## संबंधित दस्तावेज़

- [आपका पहला ऐप](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../advanced/patches.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

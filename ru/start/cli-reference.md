# Справочник Pinoox CLI

[← Вернуться к оглавлению](../README.md)

Выполняйте все команды из **корня проекта**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Если требуется указать пакет, а он опущен, Pinoox показывает интерактивный выбор.

> Для проектов с **одним приложением** используйте автономный [Pinx CLI](./pinx-cli.md) (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Частые псевдонимы

| Псевдоним | Команда |
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

## Приложения

| Команда | Назначение |
|---------|---------|
| `app:create {package}` | Создание каркаса приложения (`--simple`, `--stack`, `--profile`) |
| `app:list` | Список приложений |
| `app:delete` | Удаление приложения |
| `app:router set /path {package}` | Привязка URL |
| `app:domain` | Карта «хост → приложение» |
| `app:resolve` | Отладка активного приложения |

---

## Генерация кода

| Команда | Результат |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | Класс FormRequest |
| `seeder:create` | `database/seeders/` |
| `test:create` | Файл Pest |
| `theme:frontend` | Фронтенд-инструменты (Vue/React/Twig) |

---

## База данных

| Команда | Назначение |
|---------|---------|
| `migrate {package}` | Запуск миграций (приложение, `platform`, `pincore`) |
| `migrate:create` | Новый файл миграции |
| `migrate:status` / `migrate:rollback` | Статус / откат |
| `seeder:run` | Запуск сидеров |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Патчи](../database/patches.md) |
| `query` | Сырой SQL (отладка) |

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

## Pinion (возобновляемые загрузки)

Управление активными сессиями поблочной загрузки (временное хранилище: `storage/pinion`):

| Команда | Назначение |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

См. [протокол Pinion](../advanced/pinion.md).

---

## Кэш и Pinker

| Команда | Назначение |
|---------|---------|
| `cache:build` / `cache:clear` | Кэш времени выполнения |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Сброс Pinker + конфигурации |

---

## Планировщик

| Команда | Назначение |
|---------|---------|
| `schedule:list` | Список cron-задач |
| `schedule:run` | Запуск задач, срок которых наступил |

См. [Планировщик](../advanced/schedule.md).

---

## Роутер

| Команда | Назначение |
|---------|---------|
| `route:actions {package}` | Список именованных действий (Named Actions) |

---

## Упаковка Pinx

| Команда | Назначение |
|---------|---------|
| `pinx:build` | Сборка пакета `.pinx` |
| `pinx:install` | Установка пакета |
| `pinx:info` | Метаданные |
| `wizard:list` / `wizard:install` | Мастер установки |

---

## Разработка

| Команда | Назначение |
|---------|---------|
| `test` | Тесты Pest |
| `serve` | Встроенный dev-сервер |
| `log:view` / `log:clear` | Журналы |
| `deps` | Composer/npm по всем приложениям |
| `version` / `mode:show` | Версия / режим выполнения |

---

## Аргумент пакета

| Значение | Смысл |
|-------|---------|
| `com_my_shop` | Конкретное приложение |
| `platform` | Миграции/патчи/сидеры платформы |
| `pincore` | Ядро фреймворка |
| `all` | Все приложения (cache/pinker) |

---

## Связанные документы

- [Ваше первое приложение](./your-first-app.md)
- [Миграции](../database/migrations.md)
- [Патчи](../database/patches.md)

---

[← Вернуться к оглавлению](../README.md)

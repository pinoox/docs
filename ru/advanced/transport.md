# Transport (общие ресурсы)

[← Назад к оглавлению](../README.md)

В архитектуре HMVC приложения могут совместно использовать пользователей, аутентификацию, файлы и разрешения через блок **`transport`** в `app.php`. Без transport каждое приложение хранит все ресурсы **локально** в своём пакете.

| Термин | Значение |
|------|---------|
| **`platform`** | Логическая общая область — общие строки БД используют `app = platform` |
| **`pincore/`** | Только физическая папка фреймворка — **никогда** не является значением области transport |

---

## Как это работает

Transport имеет два уровня:

1. **Сценарий (Scenario)** — пресет из одного слова, который разворачивается в несколько детальных ключей.
2. **Детальный ключ (Granular key)** — составное имя одного конкретного общего ресурса.

```php
// app.php
'transport' => [
    'full' => 'platform',           // пресет-сценарий
    'file_storage' => 'local',      // детальное переопределение
],
```

**Порядок разрешения:** явный детальный ключ → подходящий сценарий.

Детальные ключи всегда имеют приоритет над развёрткой сценария. Если ключ не задан и ни один сценарий его не покрывает, приложение оставляет этот ресурс **локальным** (текущий пакет).

---

## Значения области (scope)

Каждому сценарию или детальному ключу назначается одна область:

| Область | Значение |
|-------|---------|
| `local` | Текущий пакет приложения (по умолчанию, если не указано) |
| `platform` | Общая область платформы (`app = platform`, таблицы `pinx_*`) |
| `host` | Приложение, которое открыло данное (предпросмотр / `App::meeting()`) |
| `{package}` | Конкретное приложение, например `com_pinoox_manager` |

Для **`auth_config`** и **`auth_cookie`** значения `platform` и `{package}` разрешаются в приложение, которое **предоставляет настройки аутентификации** (обычно `com_pinoox_manager`, если он установлен).

---

## Справочник сценариев

Пресеты из одного слова. Используются в `app.php` как `'transport' => ['{scenario}' => '{scope}']`.

| Сценарий | Описание | Включённые детальные ключи |
|----------|-------------|------------------------|
| `full` | Все общие ресурсы | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Система входа: учётные записи, аутентификация, токены сессий | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | Загрузки файлов и метаданные | `file_storage` |
| `access` | Роли и разрешения | `access_table` |

---

## Справочник детальных ключей

Составные имена ресурсов. Используются для совместного доступа или переопределения одного ресурса.

| Детальный ключ | Управляет | Используется |
|--------------|----------|---------|
| `user_table` | Колонка `app` в `UserModel` / глобальная область | Учётные записи пользователей |
| `auth_config` | Режим аутентификации, секрет JWT, время жизни (источник блока `auth`) | `AuthConfig`, процесс входа |
| `auth_cookie` | Клиентский ключ / имя cookie (`auth.key`) | Хранение токена в cookie и SPA |
| `session_token` | Колонка `app` в `TokenModel` / строки сессий в БД | Постоянство сессий |
| `file_storage` | Колонка `app` в `FileModel` / пути загрузок | Загрузки и метаданные файлов |
| `access_table` | Область `app` моделей ролей и разрешений | `RoleModel`, `PermissionModel`, `can()` |

---

## Типичные конфигурации

**Провайдер аутентификации для платформы (например, manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Приложение-потребитель — всё общее, без локального блока auth:**

```php
'transport' => ['full' => 'platform'],
```

**Только общий вход:**

```php
'transport' => ['user' => 'platform'],
```

**Автономное приложение** — опустите `transport` или зафиксируйте всё локально:

```php
'transport' => ['user' => 'local'],
```

**Переопределение одного ресурса внутри сценария:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## API в коде

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // разрешённый пакет для детального ключа
Transport::authSource();                       // приложение-владелец настроек auth или null
Transport::sharesAuthWith($guest, $host);      // межприложенческая проверка auth
Transport::resolved();                         // все детальные ключи → область
Transport::activeScenarios();                  // например, ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## База данных

Таблицы с областью platform используют соединение **`platform`** и префикс **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## Связанные документы

- [Манифест app.php](../start/app-manifest.md)
- [Управление пользователями](./user-management.md)
- [Доступ и разрешения](./access-permissions.md)
- [Управление файлами](./file-management.md)

---

[← Назад к оглавлению](../README.md)

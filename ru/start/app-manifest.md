# Справочник по манифесту app.php

[← Вернуться к оглавлению](../README.md)

`app.php` — это манифест вашего приложения. Значения по умолчанию находятся в `vendor/pinoox/pincore/Component/Package/data/source.php` — переопределяйте только то, что вам нужно.

---

## Идентификация и активация

| Ключ | Назначение |
|-----|---------|
| `package` | Имя папки = пространство имён (`com_acme_shop`) |
| `name` | Отображаемое имя |
| `enable` | Включение / отключение приложения |
| `description`, `developer`, `icon` | Метаданные |
| `version-name`, `version-code` | Версия приложения |
| `sys-app`, `hidden`, `dock` | Системное приложение / скрытое / док менеджера |
| `minpin` | Минимальная версия платформы |

---

## Роутер и загрузка

| Ключ | Назначение |
|-----|---------|
| `router.routes` | Файлы `routes/*.php` |
| `boot` | Запуск `boot.php` (по умолчанию true) |
| `boot-global` | Загрузка при каждом HTTP-запросе |
| `extends` | Загрузка вместе с загрузкой хост-приложения |
| `loader` | Дополнительные файлы (`func.php`) |
| `depends` | Требуемые приложения |

См. [boot.php и события](../advanced/boot-and-events.md).

---

## Flow и безопасность

| Ключ | Назначение |
|-----|---------|
| `flow` | Глобальные flows (BootFlow) |
| `alias` | Имя → класс Flow |
| `auth` | mode, lifetime, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Совместное использование пользователей/файлов/доступа с платформой |

См. [Flows](../basic/flows.md), [Управление пользователями](../advanced/user-management.md), [Доступ](../advanced/access-permissions.md).

---

## UI и тема

| Ключ | Назначение |
|-----|---------|
| `theme` | Активная папка темы |
| `theme-context`, `theme-contexts`, `theme-extends` | Несколько контекстов / наследование |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Локаль по умолчанию |
| `open` | Поведение открытия в менеджере |

---

## База данных и хранилище

| Ключ | Назначение |
|-----|---------|
| `database` | Переопределение подключения к БД |
| `table.prefix` | Префикс таблиц |
| `transport.user` / `file_storage` / `access` | Пресеты или детальные ключи |
| `filesystem` | disk, миниатюры, доступ |

---

## Время выполнения

| Ключ | Назначение |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Переопределения режима |
| `cache` | Запекание routes/api/boot/twig |
| `log`, `redis`, `date` | Переопределения на уровне приложения |
| `container` | Привязки DI |

---

## Pinker / Pinx

| Ключ | Назначение |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | exclude/include для пакетов |

---

## Комбинированный пример

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## Связанные документы

- [Структура проекта](./structure.md)
- [Конфигурация (Config)](../basic/config.md)

---

[← Вернуться к оглавлению](../README.md)

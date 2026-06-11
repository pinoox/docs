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
| `seeder:create` | `database/seed/` |
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

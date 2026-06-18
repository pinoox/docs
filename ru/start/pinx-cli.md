# Pinx CLI (проекты с одним приложением)

[← Вернуться к оглавлению](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** — это CLI разработчика для Pinoox-проектов с **одним приложением**: создавайте каркас, запускайте, мигрируйте, собирайте и публикуйте пакеты `.pinx`, не прикасаясь к менеджеру нескольких приложений.

Он построен на `pinoox/pincore` и шаблоне `pinoox/app`. Корень вашего проекта **и есть** приложение: один `app.php`, один пакет, один рабочий процесс.

> Для классических установок платформы с несколькими приложениями используйте [`php pinoox`](./cli-reference.md).

---

## Быстрый старт

Установите Pinx один раз, создайте новое приложение и запустите его:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # предложит com_my_shop — подтвердите или измените в мастере
cd my-shop
cp .env.example .env          # задайте DB_*, если используете базу данных
pinx setup                    # миграция платформы + приложения, запуск сидеров
pinx dev                      # http://127.0.0.1:8000
```

Если команда `pinx` не найдена, добавьте глобальную папку `bin` Composer в `PATH`:

- Linux / macOS: `~/.composer/vendor/bin` или `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| Шаг | Что делает |
|------|--------------|
| `composer global require` | Устанавливает команду `pinx` на вашу машину |
| `pinx new my-shop` | Создаёт каркас из `pinoox/app`; мастер предлагает имя пакета из 3 частей (например, `com_my_shop`) |
| `.env` | База данных и пути проекта — копируется из `.env.example` |
| `pinx setup` | Одним махом: миграции платформы → миграции приложения → сидеры |
| `pinx dev` | Dev-сервер PHP; также запускает Vite, если настроен фронтенд-стек |

Имена пакетов следуют формату `com_{vendor}_{name}` — например, `com_acme_shop`, `ir_yekdo_app`. Уже находитесь в пустой папке? Используйте `pinx init` вместо `pinx new`.

**Необязательная проверка перед `setup`:** `pinx doctor` сообщает о состоянии PHP, структуры, окружения, БД и готовности сборки.

---

## Альтернатива: `composer create-project`

Без глобальной установки — шаблон поставляется с `bin/pinx` внутри проекта:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## Чем отличается режим одного приложения

Классические установки Pinoox держат много приложений в `apps/` и выбирают одно во время выполнения. Режим **одного приложения** упрощает это:

- `app.php` в корне проекта содержит идентификацию пакета и настройки pinx
- `Controller/`, `Model/`, `routes/`, `theme/` находятся в корне — не внутри `apps/{package}/`
- `platform/` содержит локальную маршрутизацию и конфигурацию лаунчера (исключается из сборок `.pinx`)
- Pinx всегда работает с **вашим** приложением — без выбора пакета, без UI менеджера

```
my-shop/                    ← корень проекта = корень приложения
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← dev-хост + слой развёртывания (только локально)
├── bin/pinx                ← локальная точка входа CLI проекта
└── vendor/pinoox/pincore   ← фреймворк
```

---

## Варианты установки

| Где | Как | Когда использовать |
|-------|-----|-------------|
| **Глобально** | `composer global require pinoox/pinx-cli` | Рекомендуется — `pinx new` и `pinx init` из любого места |
| **В проекте** | Поставляется как `bin/pinx` в `pinoox/app` | После `composer create-project` — глобальная установка не нужна |

```bash
pinx -v          # версия CLI (например, pinx-cli 1.1.7)
pinx list        # обзор команд по группам
pinx help setup  # подробности по одной команде
```

---

## Повседневный рабочий процесс

```bash
pinx dev                    # локальный сервер (+ Vite, если в app.php задан frontend.stack)
pinx dev --open             # открыть браузер после запуска
pinx dev --no-frontend      # только PHP

pinx migrate                # запуск миграций приложения (--platform сначала запускает платформу)
pinx migrate:st             # статус миграций
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # список именованных действий (--validate, --json)
pinx test                   # запуск тестов приложения (Pest)
```

**Фронтенд** (когда `theme/` использует Vue/React + Vite):

```bash
pinx fe:info                # стек, npm-скрипты, пути
pinx fe:i                   # npm install
pinx fe:d                   # dev-сервер Vite
pinx fe:b                   # production-сборка
pinx fe:sc --stack=vue      # генерация стартовых файлов
```

**Зависимости:**

```bash
pinx deps:st                # статус Composer + npm
pinx deps:i                 # установить всё
pinx deps:up                # обновить всё
```

**Pinker** (кэш сборки):

```bash
pinx pinker:st              # кэш vs исходники
pinx pinker:rb              # пересборка
pinx pinker:df              # различия
```

---

## Выпуск в продакшен

Соберите пакет `.pinx` для установки на полную платформу Pinoox (Manager → Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # повышение версии в app.php + сборка
pinx release --sign         # подписать, если ключ настроен в app.php → pinx.sign
```

`pinx build` применяет разумные значения по умолчанию (исключает `vendor/`, `bin/`, `.env`, `platform/`, dev-инструменты). Переопределяйте в `app.php` только при необходимости:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor выполняет структурированную диагностику и предлагает команды для исправления при сбоях:

| Группа | Проверки |
|-------|--------|
| **Project** | `app.php`, идентификация пакета, структура `platform/` |
| **Runtime** | Версия PHP (≥ 8.1), расширения, пути с правом записи |
| **Dependencies** | Composer vendor, опционально Node/npm |
| **Environment** | Наличие `.env` и ключевых переменных |
| **Database** | Подключение (можно пропустить с `--skip-db`) |
| **Frontend** | Стек темы, `package.json` (можно пропустить с `--skip-frontend`) |
| **Build** | Готовность к экспорту, иконка, поля версии |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # отчёт, удобный для CI
pinx doctor --no-fixes      # скрыть предлагаемые команды
```

---

## Справочник команд

Запустите `pinx list` для обзора по разделам. Короткие псевдонимы указаны в скобках.

### Проект

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `new` | — | Каркас из `pinoox/app` (мастер или флаги) |
| `init` | — | Инициализация текущей директории (`--force` для перезаписи) |
| `setup` | — | БД: миграция платформы + приложения, затем сидеры |
| `doctor` | `dr` | Проверка состояния — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | Показать метаданные из `app.php` |

### Разработка

| Команда | Описание |
|---------|-------------|
| `dev` | Dev-сервер; Vite, если `frontend.stack` — vue/react |

### База данных

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `migrate:run` | `migrate` | Запуск миграций приложения (`--platform` сначала запускает платформу) |
| `migrate:status` | `migrate:st` | Статус миграций |
| `migrate:rollback` | `migrate:rb` | Откат последнего пакета (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Создание файла миграции |
| `migrate:platform` | `migrate:pl` | Только миграции платформы |
| `seeder:run` | `seed` | Запуск сидеров (`-c` класс) |

### Патчи

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `patch:run` | `patch` | Запуск ожидающих патчей |
| `patch:status` | `patch:st` | Статус патчей |
| `patch:rollback` | `patch:rb` | Откат последнего пакета патчей |

### Сборка и релиз

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `build` | `bld` | Сборка пакета `.pinx` |
| `release` | `rel` | Повышение версии + сборка (`--bump`, `--sign`) |

### Генерация кода

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Маршруты

| Команда | Описание |
|---------|-------------|
| `route:actions` / `routes` | Список именованных действий (`--validate`, `--json`) |

### Зависимости

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Статус Composer + npm |
| `deps:install` | `deps:i` | Установка зависимостей |
| `deps:update` | `deps:up` | Обновление зависимостей |

### Фронтенд

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Стек темы и npm-скрипты |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Production-сборка |
| `fe:dev` | `fe:d` | Dev-сервер Vite |
| `fe:scaffold` | `fe:sc` | Стартовые файлы (`--stack=vue\|react\|twig`) |

### Планировщик

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | Список cron-задач из `schedule.php` |
| `schedule:run` | `sched:run` | Запуск задач, срок которых наступил (`--dry-run`) |

### Pinion (возобновляемые загрузки)

Передаётся в `php pinoox pinion:*` — управление временными сессиями поблочной загрузки.

| Команда | Описание |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

См. [протокол Pinion](../advanced/pinion.md).

### Pinker

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Кэш vs исходники |
| `pinker:rebuild` | `pinker:rb` | Пересборка кэша |
| `pinker:diff` | `pinker:df` | Показать различия |
| `pinker:clear` | `pinker:cl` | Очистить кэш |
| `pinker:overrides` | `pinker:ov` | Список переопределений |

### Качество и документация

| Команда | Описание |
|---------|-------------|
| `test` / `pest` | Запуск тестов приложения (`--unit`, `--feature`) |
| `api:docs` | Документация REST API |
| `graphql:docs` | Документация GraphQL-схемы |

### Прочее

| Команда | Псевдонимы | Описание |
|---------|---------|-------------|
| `list` | — | Обзор команд по группам |
| `version` | `ver` | Версия CLI |

---

## Обнаружение приложения

Pinx поднимается вверх от текущей рабочей директории, пока не найдёт валидный проект с одним приложением:

1. `app.php` существует и возвращает массив с непустым ключом `package`
2. `pinoox/pincore` требуется в `composer.json`, либо присутствует `vendor/pinoox/pincore`

Переопределить обнаруженный пакет можно переменными окружения:

| Переменная | Назначение |
|----------|---------|
| `PINX_PACKAGE` | Принудительно задать целевой пакет CLI |
| `PINOOX_DEV_APP` | Псевдоним `PINX_PACKAGE` |
| `PINX_DEV=1` | Dev-режим (устанавливается pinx автоматически при делегировании в pincore) |

---

## Требования

- **PHP** ≥ 8.1 с расширениями, требуемыми `pinoox/pincore`
- **Composer** 2.x
- **Node.js** + npm — только при использовании фронтендов Vite/Vue/React
- **База данных** — MySQL/MariaDB или то, что настроено в вашем `.env` (необязательна для статических/Twig-only приложений)

---

## Связанные документы

- [Установка Pinoox](./installing-pinoox.md)
- [Справочник Pinoox CLI (несколько приложений)](./cli-reference.md)
- [Ваше первое приложение](./your-first-app.md)
- [Манифест app.php](./app-manifest.md)

---

[← Вернуться к оглавлению](../README.md)

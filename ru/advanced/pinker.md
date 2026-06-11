# Pinker и кэш

[← Назад к оглавлению](../README.md)

**Pinker** — это слой сборки/выполнения (bake/runtime) в Pinoox 3.x: конфигурация и кэш компилируются из исходников в PHP-файлы, которые можно подключать через `include` для более быстрой загрузки. Стандартный путь для каждого приложения: **`pinker/apps/{package}/`**.

---

## Структура папок

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← собранный app.php
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← скомпилированные шаблоны
```

На уровне проекта:

```
pinker/config/          ← собранная конфигурация (не зависящая от окружения)
pinker/state/config/    ← переопределения после установки (например, database)
```

---

## CLI-команды

```bash
# Пересобрать Pinker для одного приложения
php pinoox pinker:rebuild com_acme_shop

# Короткий алиас
php pinoox bake com_acme_shop

# Статус: сравнить исходники с собранным выводом
php pinoox pinker:status com_acme_shop

# Собрать кэш (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Только Twig
php pinoox cache:build com_acme_shop --only=twig

# Только Pinker
php pinoox cache:build com_acme_shop --only=pinker

# Очистить кэш
php pinoox cache:clear com_acme_shop
```

---

## Когда пересобирать

| Событие | Команда |
|-------|---------|
| Изменение `app.php` или конфигурации | `pinker:rebuild` |
| Изменение route / api | `cache:build` |
| Изменение `.twig` в production | `cache:build --only=twig` |
| После установки на сервер | `cache:build` + `pinker:rebuild` |
| Перед сборкой `.pinx` | `cache:build` (кэш внутри пакета) |

---

## Включение кэша во время выполнения

В `apps/{package}/app.php`:

```php
'cache' => [
    'enabled' => false,   // по умолчанию — при необходимости установите true в production
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## Зеркало приложения — `pinker/app.php`

Каждое приложение может иметь собранное зеркало:

```
apps/com_acme_shop/pinker/app.php   ← исходник/эталон в репозитории
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## Хелпер `pinker()`

Для ручной сборки данных:

```php
pinker($data, ['lifetime' => 3600]);
```

Обычно вместо этого используется CLI; в коде приложения нужен редко.

---

## Рекомендуемый процесс деплоя

```bash
# 1. сборка фронтенда
php pinoox theme:frontend build com_acme_shop

# 2. кэш
php pinoox cache:build com_acme_shop

# 3. pinker (зависит от окружения)
php pinoox pinker:rebuild com_acme_shop
```

---

## Советы

- Не редактируйте `pinker/state/` вручную — туда пишет установщик.
- В разработке runtime-кэш обычно выключен; пересобирайте только после крупных изменений.
- `.pinx` может поставляться с предварительно собранным кэшем; на целевом сервере один раз выполните `cache:build --only=pinker`.

---

## Связанные документы

- [Конфигурация (Config)](../basic/config.md)
- [Шаблоны Twig](../basic/templates.md)
- [Справочник CLI](../start/cli-reference.md)
- [Роутер](../basic/routers.md)

---

[← Назад к оглавлению](../README.md)

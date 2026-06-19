# Частые проблемы

[← Вернуться к оглавлению](../README.md)

Практические решения частых ошибок при установке, выполнении и разработке на Pinoox. В каждом разделе рекомендуется **один подход**.

---

## `composer install` завершается с ошибкой

**Симптомы:** отсутствует расширение, низкая версия PHP или таймаут сети.

**Решение:**

1. Включите PHP 8.2+ и расширения `mysqli`, `zip`, `mbstring`, `json`.
2. Запустите проверку платформы перед установкой:

```bash
php launcher/check.php
```

3. Установите снова:

```bash
composer install --no-interaction
```

На shared hosting, если `composer` не в PATH, соберите vendor локально и загрузите его.

---

## Ошибки прав доступа (доступ к файлам)

**Симптомы:** невозможно записать в `cache/`, `storage/`, `pinker/`.

**Решение (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

Пользователь веб-сервера (например, `www-data` или `apache`) должен иметь право записи в папки с правом записи. На Windows/MAMP держите папку проекта вне `Program Files`.

---

## `.htaccess` / rewrite не работает

**Симптомы:** 404 на всех URL, кроме `index.php`; API не возвращает JSON в браузере.

**Решение:**

1. Включите Apache `mod_rewrite`.
2. Установите `AllowOverride All` для DocumentRoot.
3. Убедитесь, что `.htaccess` существует в корне проекта.
4. Быстрый тест: `http://localhost/pinoox/api/v1/ping` — если видите JSON, rewrite работает.

На nginx пропишите правила `try_files` и `index.php` в конфигурации сервера вместо `.htaccess`.

---

## Не удаётся подключиться к базе данных

**Симптомы:** `SQLSTATE[HY000] [2002] Connection refused` или access denied.

**Решение:**

1. Убедитесь, что MySQL/MariaDB запущен.
2. Проверьте значения в `config/database.config.php` или `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Создайте базу данных заранее (`CREATE DATABASE ... utf8mb4`).
4. На cPanel хост может быть не `localhost` — используйте hostname из панели.

---

## Требуется пересборка Pinker

**Симптомы:** устаревшая конфигурация или маршруты; изменения в `app.php` не применяются.

**Решение:**

```bash
php pinoox pinker:rebuild com_my_shop
# или алиас:
php pinoox bake com_my_shop

# все приложения:
php pinoox pinker:rebuild all
```

После изменения маршрутов, конфигурации или деплоя в production обычно требуется пересборка.

---

## Маршрут не найден (404 на endpoint)

**Симптомы:** маршрут определён в коде, но вы получаете 404.

**Решение:**

1. Убедитесь, что файл маршрута в `apps/{package}/routes/` и указан в `app.php` → `router.routes`.
2. Сопоставьте URL с префиксом приложения (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Выполните пересборку Pinker (см. выше).
4. Используйте правильный HTTP-метод (`GET` vs `POST`).

---

## 404 — приложение не разрешено

**Симптомы:** страница по умолчанию или 404; загружается не то приложение.

**Решение:**

1. Проверьте сопоставление path/host:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. Правильно задайте host и path в `config/domain.config.php` (или соответствующей карте).
3. Убедитесь, что `'enable' => true` в `app.php` приложения.
4. Имя папки приложения должно совпадать с `'package'` в `app.php` (например, `com_my_shop`).

---

## Тесты падают

```bash
php pinoox test com_my_shop
```

- `.env.testing` с отдельной БД
- миграции выполнены: `php pinoox migrate com_my_shop`
- после `fakeApp()` → `deleteFakeApp()`

Подробности: [Начало работы с тестированием](../test/getting-started.md)

---

## Связанные документы

- [Установка Pinoox](../start/installing-pinoox.md)
- [Структура проекта](../start/structure.md)
- [Routers](../basic/routers.md)
- [Конфигурация](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Начало работы с базой данных](../database/getting-started.md)
- [Связаться с поддержкой](./contact-support.md)

---

[← Вернуться к оглавлению](../README.md)

# Документация Pinoox

Официальная документация для разработчиков приложений на платформе Pinoox (PHP 8.1+, архитектура HMVC).

Каждое руководство описывает **один рекомендуемый подход** с практическими примерами. Выберите раздел ниже или просматривайте по темам.

**Языки:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](./README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### Введение

#### [Что такое Pinoox?](./introduction/what-is-pinoox.md)
#### [Возможности Pinoox](./introduction/features-pinoox.md)
#### [Участие в развитии Pinoox](./introduction/contributions.md)

### Начало работы

#### [Установка Pinoox](./start/installing-pinoox.md)
#### [Ваше первое приложение](./start/your-first-app.md)
#### [Структура проекта](./start/structure.md)
#### [Справочник Pinoox CLI](./start/cli-reference.md)
#### [Pinx CLI (проекты с одним приложением)](./start/pinx-cli.md)
#### [Справочник по манифесту app.php](./start/app-manifest.md)

### Практические руководства

#### [Пошаговое руководство: приложение Notes API](./examples/simple-api-app.md)
#### [Пошаговое руководство: веб-приложение «Телефонная книга»](./examples/phonebook-app.md)
#### [Пошаговое руководство: приложение контактной формы](./examples/contact-form-app.md)
#### [Пошаговое руководство: простое блог-приложение](./examples/blog-app.md)
#### [Пошаговое руководство: доска задач (Todo)](./examples/task-board-app.md)
#### [Пошаговое руководство: приложение галереи изображений](./examples/gallery-app.md)
#### [Пошаговое руководство: Vue SPA-панель](./examples/vue-spa-app.md)
#### [Пошаговое руководство: React SPA-панель](./examples/react-spa-app.md)
#### [Пошаговое руководство: гибрид Vite (Twig + JS-виджет)](./examples/vite-hybrid-app.md)

### Основные концепции

#### [Роутер (Router)](./basic/routers.md)
#### [Контроллеры (Controllers)](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [HTTP-запрос (Request)](./basic/requests.md)
#### [HTTP-ответ (Response)](./basic/responses.md)
#### [URL и построение ссылок](./basic/url.md)
#### [Пути к файлам](./basic/path.md)
#### [Валидация](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Шаблоны Twig](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Конфигурация](./basic/config.md)
#### [Язык и перевод](./basic/language.md)

### Продвинутые темы

#### [Pinker и кэш](./advanced/pinker.md)
#### [Сервисы приложения (Component + Portal)](./advanced/services.md)
#### [Глобальные хелперы (Global Helpers)](./advanced/helpers.md)
#### [Отправка электронной почты (Email)](./advanced/mail.md)
#### [HTTP-клиент (HTTP Client)](./advanced/http-client.md)
#### [Управление пользователями](./advanced/user-management.md)
#### [Управление файлами (File Management)](./advanced/file-management.md)
#### [Протокол Pinion](./advanced/pinion.md)
#### [Управление токенами (Token Management)](./advanced/token-management.md)
#### [Доступ и разрешения (Access & permissions)](./advanced/access-permissions.md)
#### [Transport (общие ресурсы)](./advanced/transport.md)
#### [boot.php и события (events)](./advanced/boot-and-events.md)
#### [Планирование задач (cron)](./advanced/schedule.md)

### База данных

#### [Начало работы с базой данных](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Пагинация](./database/pagination.md)
#### [Миграции](./database/migrations.md)
#### [Патчи (обновление данных)](./database/patches.md)

### Eloquent ORM

#### [Eloquent ORM — начало работы](./eloquent-orm/getting-started.md)
#### [Eloquent Relationships](./eloquent-orm/relationships.md)
#### [Eloquent Collections](./eloquent-orm/collections.md)
#### [Mutators и Casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Сериализация модели](./eloquent-orm/serialization.md)
#### [Тестовые данные — Seeders](./eloquent-orm/factories.md)

### Тестирование

#### [Начало работы с тестированием в Pinoox](./test/getting-started.md)
#### [HTTP-тестирование в Pinoox](./test/http-tests.md)
#### [Тестирование консоли в Pinoox](./test/console-tests.md)
#### [Браузерное (HTML) тестирование в Pinoox](./test/browser-tests.md)
#### [Тестирование базы данных в Pinoox](./test/database.md)
#### [Тестирование сериализации в Pinoox](./test/serialization.md)
#### [Mocking в Pinoox](./test/mocking.md)

### Частые вопросы

#### [Частые проблемы](./faq/common-issues.md)
#### [Связаться с поддержкой](./faq/contact-support.md)

---

### Исходный код
**Примеры исходного кода:** [docs/source/](../source/) — полный код для каждого руководства

Пошаговые руководства для реальных приложений — после основ, когда нужен практический код.

---

### Как читать эту документацию

1. Начните с **Введения** и **Начала работы**, если вы новичок в Pinoox.
2. Следуйте **Практическим руководствам** — создайте JSON API и простой сайт шаг за шагом.
3. Читайте **Основные концепции** при создании маршрутов, контроллеров и представлений.
4. Используйте **Базу данных** и **Eloquent ORM** при добавлении хранения данных.
5. Смотрите **Продвинутые темы** для auth, файлов, Pinker и общих сервисов.
6. Используйте **Тестирование** перед выкладкой в production.

Весь код приложений находится в `apps/{package}/`. Ядро фреймворка — `vendor/pinoox/pincore/`; не размещайте там бизнес-логику приложения.

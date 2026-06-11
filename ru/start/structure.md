# Структура проекта

[← Вернуться к оглавлению](../README.md)

Pinoox использует архитектуру HMVC: каждое приложение в `apps/{package}/` — это полноценный независимый MVC-модуль. Ядро фреймворка находится в `vendor/pinoox/pincore/` и изменяется только при работе над самой платформой.

---

## Структура проекта

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← ядро (пакет Composer)
├── apps/                    ← все приложения
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← загруженные файлы и хранилище приложений
```

---

## Структура приложения

```
apps/com_acme_shop/
├── app.php                  ← манифест (обязателен)
├── boot.php                 ← программные маршруты/события (опционально)
├── schedule.php             ← cron-задачи (опционально)
├── Controller/              ← HTTP-обработчики
├── Model/                   ← модели Eloquent
├── Flow/                    ← middleware
├── Component/               ← бизнес-логика
├── Portal/                  ← фасады приложения (опционально)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← константы имён действий (опционально)
├── theme/default/           ← Twig + ассеты
├── lang/en/                 ← переводы
├── config/                  ← конфигурация приложения
├── database/migrations/
└── pinker/                  ← зеркало сборки
```

Представления не лежат в отдельной папке `View/` — шаблоны находятся в `theme/{themeName}/`.

---

## app.php — ключевые поля

```php
<?php

return [
    'package' => 'com_acme_shop',   // = имя папки
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Пространства имён

PSR-4: `App\` → `apps/`

| Файл | Пространство имён |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Правила именования

- Пакет: `com_{vendor}_{name}` — например, `com_acme_shop`
- Имя папки = `package` в `app.php` = сегмент пространства имён
- Префикс таблиц БД: `{package}_` (например, `com_acme_shop_orders`)

---

## Граница между приложением и ядром

| Изменение | Расположение |
|--------|----------|
| Новый endpoint | `apps/{package}/Controller/` + `routes/` |
| Миграция | `apps/{package}/database/migrations/` |
| Ошибка фреймворка | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

Сохраняйте независимость приложений — используйте фасады `Pinoox\Portal\*`, а не жёсткую связку приложений друг с другом.

---

## Связанные документы

- [Ваше первое приложение](./your-first-app.md)
- [Роутер (Router)](../basic/routers.md)
- [Контроллеры (Controllers)](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Вернуться к оглавлению](../README.md)

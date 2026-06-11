# Возможности Pinoox

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x создан для модульной PHP-экосистемы: несколько независимых приложений на одном общем ядре, генерация кода через CLI и встроенные инструменты для HTTP, базы данных, тем и аутентификации.

---

## Архитектура HMVC и независимые приложения

Каждое приложение в `apps/{package}/` имеет полную MVC-структуру:

| Слой | Пример пути |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

Добавление или отключение одного приложения не влияет на остальные.

---

## CLI и быстрая разработка

Из корня проекта:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI генерирует стандартную структуру папок, `app.php` и начальные файлы маршрутов.

---

## Маршрутизация и именованные действия (Named Actions)

URL-пути и логические обработчики хранятся раздельно:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

Этот паттерн упрощает рефакторинг и тестирование.

---

## Flow (middleware)

Прежде чем запрос достигнет контроллера, выполняются Flows — для аутентификации, авторизации, логирования и многого другого:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Регистрируйте псевдонимы Flow в `app.php`.

---

## Представления (View) и темы

- Twig-шаблоны в `theme/{themeName}/`
- Рендеринг через **`View::render()`**
- Поддержка SPA с Vite внутри темы (Vue/React)

---

## База данных и Eloquent

- Query Builder и Eloquent через Portal `DB`
- Миграции и сидеры в `database/migrations/` каждого приложения
- Префикс таблиц на основе имени пакета (например, `com_acme_blog_posts`)

---

## API и JSON-ответы

Наследуйтесь от **`ApiController`** и используйте стандартный конверт ответа:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Интернационализация

Файлы переводов в `lang/{locale}/*.lang.php` — подходит для многоязычных приложений.

---

## Связанные документы

- [Что такое Pinoox?](./what-is-pinoox.md)
- [Установка Pinoox](../start/installing-pinoox.md)
- [Роутер (Router)](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Вернуться к оглавлению](../README.md)

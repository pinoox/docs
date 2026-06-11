# HTTP-запрос (Request)

[← Вернуться к оглавлению](../README.md)

Класс `Pinoox\Component\Http\Request` обрабатывает входные данные HTTP: строку запроса, POST-формы, JSON-тело, параметры маршрута и загрузку файлов. В контроллерах и Flows объект `Request` доступен через **внедрение зависимостей** в параметры методов.

> Глобального хелпера **`request()`** нет. Внедряйте `Request` или используйте `$this->getRequest()` в контроллерах.

---

## Доступ в контроллере

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` возвращает объединённые данные из атрибутов, POST, query, JSON и файлов.

---

## Чтение из конкретного источника

| Источник | Метод | Пример |
|--------|--------|---------|
| Строка запроса | `queryOne()` | `$request->queryOne('page', 1)` |
| POST-форма | `requestOne()` | `$request->requestOne('email')` |
| JSON-тело | `jsonOne()` | `$request->jsonOne('items')` |
| Параметр маршрута | `parametersOne()` | `$request->parametersOne('id')` |
| Все входные данные | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST-поле: email
$email = $request->requestOne('email');

// Маршрут: /product/{id}
$id = $request->parametersOne('id');
```

---

## Валидация

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

Или получите экземпляр Validator:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

Базовый контроллер также предоставляет **`$this->validate()`** и **`$this->validation()`**.

---

## Загрузка файлов

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/apps/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Определение типа запроса

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Текущий маршрут и коллекция

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Полный пример API-контроллера

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:200',
            'price' => 'required|numeric|min:0',
        ]);

        $product = ProductModel::create($data);

        return $this->ok($product, status: 201);
    }
}
```

---

## Рекомендации

- Всегда валидируйте пользовательский ввод
- Для API читайте JSON через `jsonOne()` или `get()`
- `Request` можно внедрять и во Flows

---

## Связанные документы

- [Контроллеры (Controllers)](./controllers.md)
- [HTTP-ответ (Response)](./responses.md)
- [Валидация](./validation.md)
- [Роутер (Router)](./routers.md)

---

[← Вернуться к оглавлению](../README.md)

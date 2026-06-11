# Router

[← Voltar ao índice](../README.md)

O roteamento do Pinoox 3.x tem duas camadas: **Named Actions** (handlers lógicos) e **Routes** (caminhos de URL e métodos HTTP). Cada app define suas rotas na pasta **`routes/`** e as registra em **`app.php`**.

> Não use **`Pinoox\Portal\Router::get`**. Importe funções de router do namespace **`Pinoox\Router`**.

---

## Registrar arquivos de rota em app.php

```php
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## Importar funções de router

```php
use function Pinoox\Router\{
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

A função **`collection()`** não existe no pincore 3.x.

---

## Named Actions

Defina um handler uma vez em `routes/actions.php`:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## Mapear URLs em web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — referência a uma action registrada
- `{id}` — parâmetro dinâmico (passado ao controller ou via `$request->parametersOne('id')`)

---

## Métodos HTTP

```php
use function Pinoox\Router\{get, post, put, patch, delete};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
```

---

## Grupos de rotas

Use `group()` para prefixo compartilhado e Flow em várias rotas:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

Caminhos resultantes: `/admin/dashboard`, `/admin/orders`

---

## Flow em uma rota única

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

O alias correspondente deve estar registrado em `app.php` em `'alias'`.

---

## Arquivo de rotas de API — `routes()` e `collect()`

```php
<?php
// routes/api.php

use App\com_acme_shop\Controller\ProductApiController;
use function Pinoox\Router\{collect, get, post, routes};

return routes([
    'version' => 'v1',
    'prefix' => '',
    'routes' => collect(function () {
        get('/products', [ProductApiController::class, 'index'])->name('products.index');
        post('/products', [ProductApiController::class, 'store'])->name('products.store');
        get('/products/{id}', [ProductApiController::class, 'show'])->name('products.show');
    }),
]);
```

`collect()` reúne rotas dentro de um manifesto de API. Retorne o manifesto final com **`routes([...])`**.

---

## URL a partir do nome da rota

```php
use function Pinoox\Router\route;

echo route('home');                    // URL da rota ativa
echo route('product.show', ['id' => 5]);
```

---

## Fallback (404)

```php
use Pinoox\Portal\View;
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
```

---

## Seleção do app ativo (nível do projeto)

Qual app atende uma requisição é configurado em `config/app-router.config.php` (prefixo de URL) ou `config/domain.config.php` (domínio):

```php
// config/app-router.config.php
return [
    '/' => 'com_pinoox_welcome',
    '/shop' => 'com_acme_shop',
];
```

CLI:

```bash
php pinoox app:router set /shop com_acme_shop
```

---

## Documentação relacionada

- [Flow](./flows.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [Seu primeiro app](../start/your-first-app.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

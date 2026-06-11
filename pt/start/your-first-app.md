# Seu primeiro app

[← Voltar ao índice](../README.md)

A forma mais rápida de criar um app no Pinoox 3.x é o comando da CLI `app:create`. Ele gera a estrutura MVC padrão em `apps/{package}/`: `routes/`, `Controller/`, `theme/`, `config/`.

---

## Crie o app

A partir da raiz do projeto:

```bash
php pinoox app:create com_acme_blog
```

| Pergunta da CLI | Exemplo |
|------------|---------|
| Nome do pacote | `com_acme_blog` (formato: `com_{vendor}_{name}`) |
| Nome de exibição | `Blog` |
| Caminho da URL | `/blog` (opcional — registrado em `config/app-router.config.php`) |

Modo simples (apenas Twig, sem assistente):

```bash
php pinoox app:create com_acme_blog --simple
```

---

## Estrutura gerada

```
apps/com_acme_blog/
├── app.php
├── Controller/
│   └── MainController.php
├── routes/
│   ├── actions.php
│   └── web.php
├── Router/
│   └── Actions.php
├── theme/
│   └── default/
│       └── hello.twig
└── config/
```

---

## app.php — registre as rotas

O manifesto `app.php` lista os arquivos de rota do app:

```php
<?php

return [
    'package' => 'com_acme_blog',
    'name' => 'Blog',
    'enable' => true,
    'theme' => 'default',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Named Actions e rotas

**actions.php** — defina o handler:

```php
<?php

use App\com_acme_blog\Controller\MainController;
use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\action;

action(Actions::HOME, [MainController::class, 'index']);
```

**web.php** — mapeie a URL:

```php
<?php

use App\com_acme_blog\Router\Actions;
use function Pinoox\Router\get;

get('/', '@' . Actions::HOME)->name('home');
```

---

## Controller

```php
<?php

namespace App\com_acme_blog\Controller;

use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class MainController extends Controller
{
    public function index()
    {
        return View::render('hello', [
            'title' => 'My first app',
        ]);
    }
}
```

Namespace: `App\{package}\Controller` — a pasta é `Controller/` (não `Controllers/`).

---

## Registre a URL do app (nível do projeto)

Se você registrou `/blog` durante o assistente, uma entrada é adicionada a `config/app-router.config.php`:

```php
return [
    '/' => 'com_pinoox_installer',
    '/blog' => 'com_acme_blog',
];
```

Manualmente ou via CLI:

```bash
php pinoox app:router set /blog com_acme_blog
```

---

## Veja no navegador

```
http://localhost/blog
```

---

## Próximos comandos úteis

```bash
php pinoox controller:create PostController com_acme_blog
php pinoox migrate -p com_acme_blog
php pinoox route:actions com_acme_blog
```

---

## Documentos relacionados

- [Estrutura do projeto](./structure.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Tutorial da API de notas](../examples/simple-api-app.md)
- [Tutorial web da agenda telefônica](../examples/phonebook-app.md)
- [Tutorial do formulário de contato](../examples/contact-form-app.md)
- [Tutorial do blog simples](../examples/blog-app.md)
- [Tutorial do quadro de tarefas](../examples/task-board-app.md)
- [Tutorial da galeria de imagens](../examples/gallery-app.md)

---

[← Voltar ao índice](../README.md)

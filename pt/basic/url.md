# URL e construção de links

[← Voltar ao índice](../README.md)

No Pinoox 3.x use **`url()`** para construir URLs internas. Este helper usa **`Url::link()`** e considera domínio, caminho de instalação (subpasta) e segmento do app atual.

> Não use **`Url::get()`** ou **`Url::app()`**. Use **`url()`**, **`Url::link()`** e **`Url::forApp()`** em vez disso.

---

## PHP — helper `url()`

```php
// Link relativo dentro do app ativo
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// Acessor sem argumentos
$accessor = url();
echo $accessor->app;               // URL base do app
echo $accessor->site;              // origem + caminho do projeto
echo $accessor->api;               // prefixo da API

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // igual a url('products')
echo Url::forApp('com_acme_shop'); // URL base de um app específico
echo Url::current();               // URL da página atual
echo Url::origin();                // https://example.com/pinoox
```

Prefixo `^` ou `~` para links fora da base do app:

```php
echo url('^about');                // a partir da raiz do projeto
echo Url::link('^config/app.php');
```

---

## Twig — acessor `url()`

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| Método do acessor | Propósito |
|-----------------|---------|
| `url().site` | origem + caminho do projeto |
| `url().app` | origem + segmento do app |
| `url().api` | prefixo da API (padrão `api/v1/`) |
| `url().resource('resources/logo.png')` | arquivo estático em `apps/{package}/` |
| `url('profile')` | link de rota dentro do app |

---

## Nome da rota — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Assets do tema — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL do arquivo no tema ativo
```

---

## Exemplo de menu em controller

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## Informações da requisição

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Dicas

- Não fixe links no código; sempre use `url()` ou `Url::link()`
- Arquivos em `apps/{package}/resources/` usam `url().resource()` ou `asset()`; arquivos do tema usam **`assets()`**
- A URL base não é definida manualmente na config; é detectada da requisição HTTP

---

## Documentação relacionada

- [Caminho de arquivo](./path.md)
- [Views](./views.md)
- [Templates Twig](./templates.md)
- [Router](./routers.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

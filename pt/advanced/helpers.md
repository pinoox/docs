# Helpers Globais

[← Voltar ao índice](../README.md)

O Pinoox 3.x carrega helpers globais a partir de `pincore/functions/`. Para o desenvolvimento diário de aplicativos, esses helpers (junto com os Portals) são suficientes — não instancie Components do core diretamente.

---

## Helpers principais

| Helper | Finalidade | Exemplo |
|--------|---------|---------|
| `render()` | HTML como string | `$html = render('email', $data);` |
| `response()` | Resposta HTTP | `return response()->json($data);` |
| `redirect()` | Redirecionamento | `return redirect(url('login'));` |
| `url()` | URL do app/site | `url('products')` |
| `path()` | Caminho do arquivo no disco | `path('storage/logs/app.log')` |
| `assets()` | URL de arquivo do tema | `assets('dist/app.css')` |
| `config()` | Ler/gravar configuração | `config('app.name')` |
| `t()` | Tradução (retorna) | `t('welcome.title')` |
| `lang()` | Tradução (imprime) | `lang('welcome.title')` |
| `app()` | App ativo | `app()->get('package')` |
| `auth()` | Usuário logado | `auth()` → `Auth::user()` |
| `user()` | Campo do usuário | `user('email')` |
| `isLogin()` | Status de login | `if (isLogin()) { … }` |
| `session()` | Sessão | `session('token')` |
| `runtime()` | Kernel HTTP ativo | `runtime()->getRequest()` |
| `_env()` | Variável de ambiente | `_env('APP_DEBUG', false)` |
| `alias()` | Alias de Flow/classe | `alias('auth')` |

Para HTML em controllers use **`View::render()`** (igual aos apps do sistema). O helper `view()` existe, mas prefira o Portal nos controllers.

---

## Request — injeção ou `runtime()`

Não existe um helper global `request()` no pincore. Em controllers e components use injeção por type-hint:

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

Em um Flow ou em outro lugar onde a assinatura não permite injeção:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// Usuário atual (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) é o mesmo que user($key)
$email = auth('email');

// Proteja rotas com alias de Flow
// app.php → 'alias' => ['auth' => AuthFlow::class]
// rotas → ->flows(['auth']) ou grupo com flows
```

---

## View e Response

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## Config

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## Lang

```php
$label = t('product.title');
// No Twig: {{ t('product.title') }}
```

---

## URL e Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Helpers personalizados do app

No `app.php`:

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return '$' . number_format($amount, 2);
}
```

---

## Helpers do Twig (em templates)

Além dos helpers PHP, estes estão disponíveis no Twig:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## Dicas

- Use `View::render()` nos controllers para HTML; helpers como `url()`, `t()` e `config()` para tarefas do dia a dia
- Os helpers só funcionam após o bootstrap do Pinoox — não os carregue em scripts PHP puros fora do `index.php` / `pinoox`
- Para lógica complexa, prefira `Component/` + Portal em vez de helpers personalizados

---

## Documentação relacionada

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Idioma](../basic/language.md)
- [Services](./services.md)

---

[← Voltar ao índice](../README.md)

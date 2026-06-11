# Config

[← Voltar ao índice](../README.md)

As configurações do Pinoox 3.x ficam em arquivos PHP em `config/` (núcleo e app). A abordagem padrão: **`config('key')`** para ler e **`config('name')->set(...)->save()`** para gravar.

---

## Leitura

```php
// Chave simples
$siteName = config('app.name');

// Chave aninhada (notação com ponto)
$merchant = config('payment.merchant_id');

// Valor padrão
$timeout = config('api.timeout', 30);

// Objeto de config para encadeamento
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## Gravação e salvamento

**Sempre chame `save()` após alterações:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## Dados aninhados — `setLinear` / `getLinear`

```php
// Leitura
$themeName = config('theme.panel.name');

// Gravação
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Localização dos arquivos de config

| Local | Conteúdo |
|----------|----------|
| `pincore/config/*.config.php` | Configurações do núcleo (DB, domínio, …) |
| `apps/{package}/config/*.config.php` | Configurações do app |
| `pinker/config/` | Versão consolidada (produção) |
| `pinker/state/config/` | Sobrescritas pós-instalação (ex.: DB) |

Em desenvolvimento, valores sensíveis são lidos de `.env` via `env()` / `_env()`.

---

## Exemplo: configurações de gateway de pagamento

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'stripe',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller ou Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## Exemplo: menu dinâmico

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'Reports', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Portal — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

Na prática, `config()` encapsula o mesmo Portal — um estilo basta.

---

## Dicas

- Não faça commit de segredos (chaves de API, senhas de DB) no git; use `.env` ou `pinker/state`.
- Nome do arquivo: `{name}.config.php` → `config('{name}.key')`.
- Após deploy em produção, execute `php pinoox pinker:rebuild` para consolidar a config.

---

## Documentação relacionada

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [Caminho de arquivo](./path.md)
- [Manifesto app.php](../start/app-manifest.md)

---

[← Voltar ao índice](../README.md)

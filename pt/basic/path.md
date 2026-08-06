# Caminho de arquivo

[← Voltar ao índice](../README.md)

Use **`path()`** e o Portal **`Pinoox\Portal\Path`** para acessar arquivos e pastas no disco. Isso mantém o código independente de onde o projeto está instalado e de como a pasta `apps/` se chama.

---

## Abordagem padrão — `path()`

```php
// Caminho relativo ao app ativo
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Arquivo de config em outro app
$configFile = path('config/payment.php', 'com_acme_shop');

// Raiz do app
$appRoot = path('', 'com_acme_shop');
// ou
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## Usos comuns

### Ler / gravar arquivos

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Caminho de arquivo de tradução

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Caminho do tema

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

Mesmo comportamento que `path()` com API explícita:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // app atual
Path::app('com_pinoox_manager'); // app específico
```

---

## `path()` vs `url()`

| Helper | Saída | Exemplo |
|--------|--------|---------|
| `path()` | Caminho físico no servidor | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | URL HTTP para o navegador | `https://site.com/pinoox/shop/products` |

---

## Exemplo: serviço de upload

Não grave uploads manualmente com `path()` + `move_uploaded_file()` — use o portal **`File`** para que os arquivos fiquem na pasta `storage/` do projeto:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // armazenado em storage/local/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

Veja [Gerenciamento de arquivos](../advanced/file-management.md) para a API completa de upload.

---

## Dicas

- Para caminhos acessíveis no navegador use `url()` ou `assets()`, não `path()`.
- Passe o nome do pacote apenas quando precisar de um app que não está ativo.
- Una segmentos de caminho com `/`; Path trata a barra correta do SO.

---

## Documentação relacionada

- [URL e links](./url.md)
- [Config](./config.md)
- [Serviços do app](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← Voltar ao índice](../README.md)

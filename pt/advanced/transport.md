# Transport (recursos compartilhados)

[← Voltar ao índice](../README.md)

Na arquitetura HMVC, os apps podem compartilhar usuários, auth, arquivos e permissões entre si através do bloco **`transport`** no `app.php`. Sem o transport, cada app mantém todos os recursos **locais** ao seu próprio pacote.

| Termo | Significado |
|------|---------|
| **`platform`** | Escopo lógico compartilhado — linhas compartilhadas no BD usam `app = platform` |
| **`pincore/`** | Apenas a pasta física do framework — **nunca** é um valor de escopo do transport |

---

## Como funciona

O transport tem duas camadas:

1. **Cenário (scenario)** — um preset de uma única palavra que se expande em várias chaves granulares.
2. **Chave granular** — um nome composto para um recurso compartilhado específico.

```php
// app.php
'transport' => [
    'full' => 'platform',           // preset de cenário
    'file_storage' => 'local',      // override granular
],
```

**Ordem de resolução:** chave granular explícita → cenário correspondente.

Chaves granulares sempre vencem a expansão do cenário. Se uma chave não estiver definida e nenhum cenário a cobrir, o app mantém esse recurso **local** (pacote atual).

---

## Valores de escopo

Cada cenário ou chave granular recebe um escopo:

| Escopo | Significado |
|-------|---------|
| `local` | Pacote do app atual (padrão quando omitido) |
| `platform` | Escopo compartilhado da plataforma (`app = platform`, tabelas `pinx_*`) |
| `host` | App que abriu este (preview / `App::meeting()`) |
| `{package}` | App explícito, ex.: `com_pinoox_manager` |

Para **`auth_config`** e **`auth_cookie`**, `platform` e `{package}` resolvem para o app que **fornece as configurações de auth** (normalmente o `com_pinoox_manager`, quando instalado).

---

## Referência de cenários

Presets de uma única palavra. Use no `app.php` como `'transport' => ['{scenario}' => '{scope}']`.

| Cenário | Descrição | Chaves granulares incluídas |
|----------|-------------|------------------------|
| `full` | Todos os recursos compartilhados | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Sistema de login: contas, auth, tokens de sessão | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | Uploads de arquivos e metadados | `file_storage` |
| `access` | Papéis (roles) e permissões | `access_table` |

---

## Referência de chaves granulares

Nomes compostos de recursos. Use para compartilhar ou sobrescrever um único recurso.

| Chave granular | Controla | Usado por |
|--------------|----------|---------|
| `user_table` | Coluna `app` do `UserModel` / escopo global | Contas de usuários |
| `auth_config` | Modo de auth, secret do JWT, lifetimes (origem do bloco `auth`) | `AuthConfig`, fluxo de login |
| `auth_cookie` | Chave do cliente / nome do cookie (`auth.key`) | Armazenamento de token em cookie e SPA |
| `session_token` | Coluna `app` do `TokenModel` / linhas de sessão no BD | Persistência de sessão |
| `file_storage` | Coluna `app` do `FileModel` / caminhos de upload | Uploads e metadados de arquivos |
| `access_table` | Escopo `app` do model de papéis e permissões | `RoleModel`, `PermissionModel`, `can()` |

---

## Configurações comuns

**Provedor de auth para a plataforma (ex.: manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**App consumidor — tudo compartilhado, sem bloco auth local:**

```php
'transport' => ['full' => 'platform'],
```

**Apenas login compartilhado:**

```php
'transport' => ['user' => 'platform'],
```

**App independente** — omita `transport`, ou fixe tudo localmente:

```php
'transport' => ['user' => 'local'],
```

**Sobrescrever um recurso dentro de um cenário:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## API de código

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // pacote resolvido para uma chave granular
Transport::authSource();                       // app dono das configurações de auth, ou null
Transport::sharesAuthWith($guest, $host);      // verificação de auth entre apps
Transport::resolved();                         // todas as chaves granulares → escopo
Transport::activeScenarios();                  // ex.: ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Banco de dados

Tabelas com escopo de plataforma usam a conexão **`platform`** e o prefixo **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## Documentação relacionada

- [Manifest app.php](../start/app-manifest.md)
- [Gerenciamento de usuários](./user-management.md)
- [Acesso e permissões](./access-permissions.md)
- [Gerenciamento de arquivos](./file-management.md)

---

[← Voltar ao índice](../README.md)

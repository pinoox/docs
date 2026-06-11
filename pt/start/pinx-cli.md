# Pinx CLI (projetos de app único)

[← Voltar ao índice](../README.md)

A **[Pinx CLI](https://github.com/pinoox/pinx-cli)** é a CLI de desenvolvimento para projetos Pinoox de **app único** — scaffold, execução, migrate, build e envio de pacotes `.pinx` sem usar um manager multi-app.

Ela é construída sobre `pinoox/pincore` e o template `pinoox/app`. A raiz do projeto **é** o app: um `app.php`, um pacote, um fluxo de trabalho.

> Para instalações clássicas de plataforma multi-app, use [`php pinoox`](./cli-reference.md) em vez disso.

---

## Início rápido

Instale o Pinx uma vez, crie um novo app e execute:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # sugere com_my_shop — confirme ou edite no wizard
cd my-shop
cp .env.example .env          # defina DB_* se usar banco de dados
pinx setup                    # migrate platform + app, executa seeders
pinx dev                      # http://127.0.0.1:8000
```

Adicione o `bin` global do Composer ao `PATH` se `pinx` não for encontrado:

- Linux / macOS: `~/.composer/vendor/bin` ou `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| Etapa | O que faz |
|------|--------------|
| `composer global require` | Instala o comando `pinx` na sua máquina |
| `pinx new my-shop` | Scaffold a partir de `pinoox/app`; wizard sugere pacote de 3 partes (ex.: `com_my_shop`) |
| `.env` | Banco de dados e caminhos do projeto — copie de `.env.example` |
| `pinx setup` | One-shot: migrations da plataforma → migrations do app → seeders |
| `pinx dev` | Servidor de dev PHP; inicia Vite também quando stack de frontend está configurada |

Nomes de pacote seguem `com_{vendor}_{name}` — ex.: `com_acme_shop`, `ir_yekdo_app`. Já está dentro de uma pasta vazia? Use `pinx init` em vez de `pinx new`.

**Verificação opcional antes de `setup`:** `pinx doctor` reporta PHP, layout, env, DB e prontidão para build.

---

## Alternativa: `composer create-project`

Sem instalação global — o template inclui `bin/pinx` dentro do projeto:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## O que torna single-app diferente

Instalações clássicas do Pinoox mantêm vários apps em `apps/` e escolhem um em runtime. **Single-app** achata isso:

- `app.php` na raiz do projeto guarda identidade do pacote e configurações pinx
- `Controller/`, `Model/`, `routes/`, `theme/` ficam na raiz — não dentro de `apps/{package}/`
- `platform/` guarda roteamento local e config do launcher (excluído de builds `.pinx`)
- Pinx sempre mira **seu** app — sem seletor de pacote, sem UI de manager

```
my-shop/                    ← raiz do projeto = raiz do app
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← host de dev + camada de deploy (só local)
├── bin/pinx                ← entrada CLI local do projeto
└── vendor/pinoox/pincore   ← framework
```

---

## Opções de instalação

| Onde | Como | Quando usar |
|-------|-----|-------------|
| **Global** | `composer global require pinoox/pinx-cli` | Recomendado — `pinx new` e `pinx init` de qualquer lugar |
| **Por projeto** | Incluído como `bin/pinx` em `pinoox/app` | Após `composer create-project` — sem instalação global |

```bash
pinx -v          # versão da CLI (ex.: pinx-cli 1.1.7)
pinx list        # visão geral de comandos agrupados
pinx help setup  # detalhe de um comando
```

---

## Fluxo do dia a dia

```bash
pinx dev                    # servidor local (+ Vite quando app.php → frontend.stack está definido)
pinx dev --open             # abre o navegador após iniciar
pinx dev --no-frontend      # só PHP

pinx migrate                # executa migrations do app (--platform executa platform primeiro)
pinx migrate:st             # status das migrations
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # listar named actions (--validate, --json)
pinx test                   # executar testes do app (Pest)
```

**Frontend** (quando `theme/` usa Vue/React + Vite):

```bash
pinx fe:info                # stack, scripts npm, caminhos
pinx fe:i                   # npm install
pinx fe:d                   # servidor dev Vite
pinx fe:b                   # build de produção
pinx fe:sc --stack=vue      # scaffold de arquivos iniciais
```

**Dependências:**

```bash
pinx deps:st                # status Composer + npm
pinx deps:i                 # instalar tudo
pinx deps:up                # atualizar tudo
```

**Pinker** (cache de build):

```bash
pinx pinker:st              # cache vs source
pinx pinker:rb              # rebuild
pinx pinker:df              # diff
```

---

## Enviar para produção

Build de pacote `.pinx` para instalação em plataforma Pinoox completa (Manager → Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # incrementa versão em app.php + build
pinx release --sign         # assina quando chave está configurada em app.php → pinx.sign
```

`pinx build` aplica padrões sensatos (exclui `vendor/`, `bin/`, `.env`, `platform/`, ferramentas de dev). Sobrescreva em `app.php` apenas quando necessário:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor executa diagnóstico estruturado e sugere comandos de correção quando algo falha:

| Grupo | Verificações |
|-------|--------|
| **Project** | `app.php`, identidade do pacote, layout de `platform/` |
| **Runtime** | Versão PHP (≥ 8.1), extensões, caminhos graváveis |
| **Dependencies** | Vendor Composer, Node/npm opcional |
| **Environment** | Presença de `.env` e variáveis-chave |
| **Database** | Conexão (pulável com `--skip-db`) |
| **Frontend** | Stack do tema, `package.json` (pulável com `--skip-frontend`) |
| **Build** | Prontidão para export, ícone, campos de versão |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # relatório amigável para CI
pinx doctor --no-fixes      # oculta comandos sugeridos
```

---

## Referência de comandos

Execute `pinx list` para visão geral por seção. Aliases curtos aparecem entre colchetes.

### Projeto

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `new` | — | Scaffold a partir de `pinoox/app` (wizard ou flags) |
| `init` | — | Inicializar o diretório atual (`--force` para sobrescrever) |
| `setup` | — | DB: migrate platform + app, depois seed |
| `doctor` | `dr` | Health check — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | Mostrar metadados de `app.php` |

### Desenvolvimento

| Comando | Descrição |
|---------|-------------|
| `dev` | Servidor de dev; Vite quando `frontend.stack` é vue/react |

### Banco de dados

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `migrate:run` | `migrate` | Executar migrations do app (`--platform` executa platform primeiro) |
| `migrate:status` | `migrate:st` | Status das migrations |
| `migrate:rollback` | `migrate:rb` | Rollback do último batch (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Criar arquivo de migration |
| `migrate:platform` | `migrate:pl` | Apenas migrations da plataforma |
| `seeder:run` | `seed` | Executar seeders (`-c` class) |

### Patches

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `patch:run` | `patch` | Executar patches pendentes |
| `patch:status` | `patch:st` | Status dos patches |
| `patch:rollback` | `patch:rb` | Rollback do último batch de patches |

### Build e release

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `build` | `bld` | Build de pacote `.pinx` |
| `release` | `rel` | Bump de versão + build (`--bump`, `--sign`) |

### Scaffolding

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Rotas

| Comando | Descrição |
|---------|-------------|
| `route:actions` / `routes` | Listar named actions (`--validate`, `--json`) |

### Dependências

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Status Composer + npm |
| `deps:install` | `deps:i` | Instalar dependências |
| `deps:update` | `deps:up` | Atualizar dependências |

### Frontend

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Stack do tema e scripts npm |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Build de produção |
| `fe:dev` | `fe:d` | Servidor dev Vite |
| `fe:scaffold` | `fe:sc` | Arquivos iniciais (`--stack=vue\|react\|twig`) |

### Schedule

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | Listar tarefas cron de `schedule.php` |
| `schedule:run` | `sched:run` | Executar tarefas devidas (`--dry-run`) |

### Pinker

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Cache vs source |
| `pinker:rebuild` | `pinker:rb` | Rebuild do cache |
| `pinker:diff` | `pinker:df` | Mostrar diferenças |
| `pinker:clear` | `pinker:cl` | Limpar cache |
| `pinker:overrides` | `pinker:ov` | Listar overrides |

### Qualidade e docs

| Comando | Descrição |
|---------|-------------|
| `test` / `pest` | Executar testes do app (`--unit`, `--feature`) |
| `api:docs` | Documentação REST API |
| `graphql:docs` | Documentação do schema GraphQL |

### Meta

| Comando | Aliases | Descrição |
|---------|---------|-------------|
| `list` | — | Visão geral de comandos agrupados |
| `version` | `ver` | Versão da CLI |

---

## Detecção de app

Pinx sobe a partir do diretório de trabalho atual até encontrar um projeto single-app válido:

1. `app.php` existe e retorna array com chave `package` não vazia
2. `pinoox/pincore` é requerido em `composer.json`, ou `vendor/pinoox/pincore` está presente

Sobrescreva o pacote detectado com variáveis de ambiente:

| Variável | Propósito |
|----------|---------|
| `PINX_PACKAGE` | Forçar pacote alvo da CLI |
| `PINOOX_DEV_APP` | Alias para `PINX_PACKAGE` |
| `PINX_DEV=1` | Modo dev (definido automaticamente pelo pinx ao delegar ao pincore) |

---

## Requisitos

- **PHP** ≥ 8.1 com extensões exigidas por `pinoox/pincore`
- **Composer** 2.x
- **Node.js** + npm — apenas ao usar frontends Vite/Vue/React
- **Banco de dados** — MySQL/MariaDB ou o que seu `.env` configurar (opcional para apps estáticos/só Twig)

---

## Documentação relacionada

- [Instalando o Pinoox](./installing-pinoox.md)
- [Referência da CLI Pinoox (multi-app)](./cli-reference.md)
- [Seu primeiro app](./your-first-app.md)
- [Manifesto app.php](./app-manifest.md)

---

[← Voltar ao índice](../README.md)

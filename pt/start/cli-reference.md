# Referência da CLI Pinoox

[← Voltar ao índice](../README.md)

Execute todo comando a partir da **raiz do projeto**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Quando um pacote é obrigatório e omitido, o Pinoox exibe um seletor interativo.

> Para projetos de **app único**, use a [Pinx CLI](./pinx-cli.md) standalone (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Aliases comuns

| Alias | Comando |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## Apps

| Comando | Propósito |
|---------|---------|
| `app:create {package}` | Scaffold de app (`--simple`, `--stack`, `--profile`) |
| `app:list` | Listar apps |
| `app:delete` | Remover app |
| `app:router set /path {package}` | Mapeamento de URL |
| `app:domain` | Mapa host → app |
| `app:resolve` | Depurar app ativo |

---

## Scaffolding

| Comando | Saída |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | Classe FormRequest |
| `seeder:create` | `database/seed/` |
| `test:create` | Arquivo Pest |
| `theme:frontend` | Ferramentas de frontend (Vue/React/Twig) |

---

## Banco de dados

| Comando | Propósito |
|---------|---------|
| `migrate {package}` | Executar migrations (app, `platform`, `pincore`) |
| `migrate:create` | Novo arquivo de migration |
| `migrate:status` / `migrate:rollback` | Status / rollback |
| `seeder:run` | Executar seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | SQL bruto (debug) |

---

## Cache e Pinker

| Comando | Propósito |
|---------|---------|
| `cache:build` / `cache:clear` | Cache de runtime |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Reset Pinker + config |

---

## Schedule

| Comando | Propósito |
|---------|---------|
| `schedule:list` | Listar tarefas cron |
| `schedule:run` | Executar tarefas devidas |

Veja [Schedule](../advanced/schedule.md).

---

## Router

| Comando | Propósito |
|---------|---------|
| `route:actions {package}` | Listar Named Actions |

---

## Empacotamento Pinx

| Comando | Propósito |
|---------|---------|
| `pinx:build` | Build de pacote `.pinx` |
| `pinx:install` | Instalar pacote |
| `pinx:info` | Metadados |
| `wizard:list` / `wizard:install` | Wizard de instalação |

---

## Desenvolvimento

| Comando | Propósito |
|---------|---------|
| `test` | Testes Pest |
| `serve` | Servidor de dev embutido |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm entre apps |
| `version` / `mode:show` | Versão / modo de runtime |

---

## Argumento package

| Valor | Significado |
|-------|---------|
| `com_my_shop` | App específico |
| `platform` | Migrations/patches/seeders da plataforma |
| `pincore` | Núcleo do framework |
| `all` | Todos os apps (cache/pinker) |

---

## Documentação relacionada

- [Seu primeiro app](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← Voltar ao índice](../README.md)

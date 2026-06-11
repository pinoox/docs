# Estrutura do projeto

[← Voltar ao índice](../README.md)

O Pinoox usa a arquitetura HMVC: cada app em `apps/{package}/` é um módulo MVC completo e independente. O núcleo do framework fica em `vendor/pinoox/pincore/` e só é editado quando se altera a própria plataforma.

---

## Layout do projeto

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← core (pacote Composer)
├── apps/                    ← todos os apps
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← arquivos enviados e armazenamento dos apps
```

---

## Layout do app

```
apps/com_acme_shop/
├── app.php                  ← manifesto (obrigatório)
├── boot.php                 ← rotas/eventos programáticos (opcional)
├── schedule.php             ← tarefas de cron (opcional)
├── Controller/              ← handlers HTTP
├── Model/                   ← models Eloquent
├── Flow/                    ← middleware
├── Component/               ← lógica de negócio
├── Portal/                  ← facades do app (opcional)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← constantes de nomes de actions (opcional)
├── theme/default/           ← Twig + assets
├── lang/en/                 ← traduções
├── config/                  ← configuração do app
├── database/migrations/
└── pinker/                  ← espelho de build
```

As views não ficam em uma pasta `View/` separada — os templates ficam em `theme/{themeName}/`.

---

## app.php — campos principais

```php
<?php

return [
    'package' => 'com_acme_shop',   // = nome da pasta
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespaces

PSR-4: `App\` → `apps/`

| Arquivo | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Regras de nomenclatura

- Pacote: `com_{vendor}_{name}` — ex.: `com_acme_shop`
- Nome da pasta = `package` no `app.php` = segmento do namespace
- Prefixo das tabelas do banco: `{package}_` (ex.: `com_acme_shop_orders`)

---

## Fronteira entre app e core

| Mudança | Local |
|--------|----------|
| Novo endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| Bug do framework | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

Mantenha os apps independentes — use as facades `Pinoox\Portal\*` em vez de acoplar apps uns aos outros.

---

## Documentos relacionados

- [Seu primeiro app](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Voltar ao índice](../README.md)

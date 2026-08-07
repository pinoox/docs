# Referência do manifesto app.php

[← Voltar ao índice](../README.md)

`app.php` é o manifesto do seu app. Os padrões ficam em `vendor/pinoox/pincore/Component/Package/data/source.php` — sobrescreva apenas o que precisar.

---

## Identidade e ativação

| Chave | Propósito |
|-----|---------|
| `package` | Nome da pasta = namespace (`com_acme_shop`) |
| `name` | Nome de exibição |
| `enable` | Habilitar / desabilitar app |
| `description`, `developer`, `icon` | Metadados |
| `version-name`, `version-code` | Versão do app |
| `sys-app`, `hidden`, `dock` | App de sistema / oculto / dock do manager |
| `minpin` | Versão mínima da plataforma |

---

## Router e boot

| Chave | Propósito |
|-----|---------|
| `router.routes` | Arquivos `routes/*.php` |
| `boot` | Executar `boot.php` (padrão true) |
| `boot-global` | Boot em toda requisição HTTP |
| `extends` | Boot quando o app host faz boot |
| `loader` | Arquivos extras (`func.php`) |
| `depends` | Apps obrigatórios |

Veja [boot.php e eventos](../advanced/boot-and-events.md).

---

## Flow e segurança

| Chave | Propósito |
|-----|---------|
| `flow` | Flows globais (BootFlow) |
| `alias` | Nome → classe Flow |
| `auth` | mode, lifetime, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Compartilhar user/file/access com a plataforma |

Veja [Flows](../basic/flows.md), [Gerenciamento de usuários](../advanced/user-management.md), [Acesso](../advanced/access-permissions.md).

---

## UI e tema

| Chave | Propósito |
|-----|---------|
| `theme` | Pasta do tema ativo |
| `theme-context`, `theme-contexts`, `theme-extends` | Multi-contexto / herança |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Locale padrão |
| `open` | Comportamento de abertura no manager |

---

## Banco de dados e armazenamento

| Chave | Propósito |
|-----|---------|
| `database` | Sobrescrita de conexão DB |
| `table.prefix` | Prefixo de tabela |
| `transport.user` / `file_storage` / `access` | Presets ou chaves granulares |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## Runtime

| Chave | Propósito |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Sobrescritas de modo |
| `cache` | Consolidar routes/api/boot/twig |
| `log`, `redis`, `date` | Sobrescritas por app |
| `container` | Bindings DI |

---

## Pinker / Pinx

| Chave | Propósito |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | exclude/include para pacotes |

---

## Exemplo combinado

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## Documentação relacionada

- [Estrutura do projeto](./structure.md)
- [Config](../basic/config.md)

---

[← Voltar ao índice](../README.md)

# Pinker e Cache

[← Voltar ao índice](../README.md)

**Pinker** é a camada de bake/runtime no Pinoox 3.x: configuração e cache são compilados da origem para arquivos PHP que podem ser incluídos via `include` para um boot mais rápido. Caminho padrão por app: **`pinker/apps/{package}/`**.

---

## Estrutura de pastas

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← app.php compilado (baked)
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← templates compilados
```

No nível do projeto:

```
pinker/config/          ← configuração compilada (não sensível ao env)
pinker/state/config/    ← overrides pós-instalação (ex.: database)
```

---

## Comandos CLI

```bash
# Recompilar o Pinker de um app
php pinoox pinker:rebuild com_acme_shop

# Alias curto
php pinoox bake com_acme_shop

# Status: comparar origem vs saída compilada
php pinoox pinker:status com_acme_shop

# Construir o cache (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Somente Twig
php pinoox cache:build com_acme_shop --only=twig

# Somente Pinker
php pinoox cache:build com_acme_shop --only=pinker

# Limpar o cache
php pinoox cache:clear com_acme_shop
```

---

## Quando recompilar

| Evento | Comando |
|-------|---------|
| Alterar `app.php` ou configuração | `pinker:rebuild` |
| Alterar route / api | `cache:build` |
| Alterar `.twig` em produção | `cache:build --only=twig` |
| Após instalação no servidor | `cache:build` + `pinker:rebuild` |
| Antes de construir o `.pinx` | `cache:build` (cache dentro do pacote) |

---

## Habilitar o cache em runtime

Em `apps/{package}/app.php`:

```php
'cache' => [
    'enabled' => false,   // padrão — defina true em produção se necessário
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## Espelho do app — `pinker/app.php`

Cada app pode ter um espelho compilado:

```
apps/com_acme_shop/pinker/app.php   ← origem/referência no repositório
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## Helper `pinker()`

Para bake manual de dados:

```php
pinker($data, ['lifetime' => 3600]);
```

Normalmente você usa a CLI; raramente é necessário no código do app.

---

## Fluxo de deploy recomendado

```bash
# 1. construir o frontend
php pinoox theme:frontend build com_acme_shop

# 2. cache
php pinoox cache:build com_acme_shop

# 3. pinker (específico do ambiente)
php pinoox pinker:rebuild com_acme_shop
```

---

## Dicas

- Não edite `pinker/state/` manualmente — o instalador grava nesse local.
- Em desenvolvimento o cache de runtime geralmente fica desligado; recompile apenas após mudanças pesadas.
- O `.pinx` pode incluir cache pré-construído; no servidor de destino execute `cache:build --only=pinker` uma vez.

---

## Documentação relacionada

- [Config](../basic/config.md)
- [Templates Twig](../basic/templates.md)
- [Referência da CLI](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← Voltar ao índice](../README.md)

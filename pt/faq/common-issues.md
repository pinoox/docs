# Problemas comuns

[← Voltar ao índice](../README.md)

Correções práticas para erros frequentes durante instalação, execução e desenvolvimento no Pinoox. Cada seção recomenda **uma abordagem**.

---

## `composer install` falha

**Sintomas:** extensão ausente, versão baixa do PHP ou timeout de rede.

**Correção:**

1. Habilite PHP 8.2+ e extensões `mysqli`, `zip`, `mbstring`, `json`.
2. Execute a verificação da plataforma antes da instalação:

```bash
php launcher/check.php
```

3. Instale novamente:

```bash
composer install --no-interaction
```

Em hospedagem compartilhada, se `composer` não estiver no PATH, monte vendor localmente e faça upload.

---

## Erros de permissão (acesso a arquivos)

**Sintomas:** Não é possível gravar em `cache/`, `storage/`, `pinker/`.

**Correção (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

O usuário do servidor web (ex.: `www-data` ou `apache`) deve poder gravar nas pastas graváveis. No Windows/MAMP, mantenha a pasta do projeto fora de `Program Files`.

---

## `.htaccess` / rewrite não funciona

**Sintomas:** 404 em todas as URLs exceto `index.php`; API não retorna JSON no navegador.

**Correção:**

1. Habilite Apache `mod_rewrite`.
2. Defina `AllowOverride All` para o DocumentRoot.
3. Garanta que `.htaccess` exista na raiz do projeto.
4. Teste rápido: `http://localhost/pinoox/api/v1/ping` — se vir JSON, o rewrite funciona.

No nginx, escreva regras `try_files` e `index.php` na config do servidor em vez de `.htaccess`.

---

## Falha na conexão com banco de dados

**Sintomas:** `SQLSTATE[HY000] [2002] Connection refused` ou acesso negado.

**Correção:**

1. Garanta que MySQL/MariaDB está em execução.
2. Verifique valores em `config/database.config.php` ou `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Crie o banco antes (`CREATE DATABASE ... utf8mb4`).
4. No cPanel, o host pode não ser `localhost` — use o hostname do painel.

---

## Rebuild do Pinker necessário

**Sintomas:** config ou rotas desatualizadas; alterações em `app.php` não são aplicadas.

**Correção:**

```bash
php pinoox pinker:rebuild com_my_shop
# ou alias:
php pinoox bake com_my_shop

# todos os apps:
php pinoox pinker:rebuild all
```

Após alterar rotas, config ou deploy em produção, um rebuild costuma ser necessário.

---

## Rota não encontrada (404 no endpoint)

**Sintomas:** rota definida no código, mas retorna 404.

**Correção:**

1. Garanta que o arquivo de rota está em `apps/{package}/routes/` e listado em `app.php` → `router.routes`.
2. Confira a URL com o prefixo do app (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Execute rebuild do Pinker (veja acima).
4. Use o método HTTP correto (`GET` vs `POST`).

---

## 404 — app não resolvido

**Sintomas:** página padrão ou 404; app errado carrega.

**Correção:**

1. Verifique mapeamento de path/host:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. Configure host e path corretamente em `config/domain.config.php` (ou mapa relevante).
3. Garanta `'enable' => true` no `app.php` do app.
4. O nome da pasta do app deve ser igual a `'package'` em `app.php` (ex.: `com_my_shop`).

---

## Testes falham

```bash
php pinoox test com_my_shop
```

- `.env.testing` com DB separado
- migrations executadas: `php pinoox migrate com_my_shop`
- após `fakeApp()` → `deleteFakeApp()`

Detalhes: [Primeiros passos com testes](../test/getting-started.md)

---

## Documentação relacionada

- [Instalando o Pinoox](../start/installing-pinoox.md)
- [Estrutura do projeto](../start/structure.md)
- [Routers](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Primeiros passos com banco de dados](../database/getting-started.md)
- [Contato e suporte](./contact-support.md)

---

[← Voltar ao índice](../README.md)

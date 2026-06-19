# Instalando o Pinoox

[← Voltar ao índice](../README.md)

Este guia cobre a instalação do Pinoox 3.x. Há duas formas de começar:

| Caminho | Ideal para |
|-------|----------|
| **A. App único com a [Pinx CLI](./pinx-cli.md)** | Construir um único app — início mais rápido, sem UI de gerenciamento |
| **B. Plataforma completa (clássica)** | Hospedar vários apps com o instalador gráfico e o gerenciador |

---

## Requisitos

| Ferramenta | Versão |
|------|---------|
| PHP | 8.2 ou superior (com ext-mysqli, ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (opcional) | 18+ — apenas para builds de tema no frontend |

---

## Caminho A — App único com a Pinx CLI

Instale a [Pinx CLI](./pinx-cli.md) uma vez, crie um novo app e execute-o:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # sugere com_my_shop — confirme ou edite no assistente
cd my-shop
cp .env.example .env          # defina DB_* se você usar banco de dados
pinx setup                    # migra plataforma + app, executa os seeders
pinx dev                      # http://127.0.0.1:8000
```

Ou, sem instalação global, via template de projeto:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

Execute `pinx doctor` a qualquer momento para verificar PHP, env, banco de dados e prontidão do build. Veja o guia completo da [Pinx CLI](./pinx-cli.md) para o fluxo de trabalho do dia a dia e a referência de comandos.

---

## Caminho B — Plataforma completa (clássica)

### 1. Obtenha o projeto

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Como alternativa, baixe a release mais recente no [GitHub](https://github.com/pinoox/pinoox), extraia e execute `composer install`.

---

### 2. Coloque-o no seu servidor web

Coloque a pasta do projeto no seu document root:

| Ambiente | Caminho de exemplo |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Defina o document root como a **raiz do projeto** (a pasta que contém `index.php`) — não uma subpasta `public`.

---

### 3. Crie o banco de dados

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Execute o instalador

Abra o navegador:

```
http://localhost/pinoox
```

O app de sistema `com_pinoox_installer` é executado. As etapas da interface gráfica são:

1. Verificar os requisitos do PHP
2. Aceitar o contrato de licença
3. Informar as credenciais do banco de dados
4. Criar a conta de administrador
5. Concluir a instalação

---

### 5. Após a instalação

Layout principal:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← apps
├── vendor/pinoox/pincore/  ← core
└── config/             ← configuração do projeto
```

Crie seu primeiro app:

```bash
php pinoox app:create com_acme_blog
```

---

## Solução rápida de problemas

| Problema | Correção |
|---------|-----|
| Página em branco | Execute `composer install` e verifique os logs de erro do PHP |
| 404 em sub-rotas | Ative o mod_rewrite / `.htaccess` |
| Erro de extensão ausente | Ative ext-mysqli e ext-zip no php.ini |
| Instalador não abre | Verifique o document root e as permissões de escrita nas pastas de runtime |

---

## Documentos relacionados

- [Pinx CLI (app único)](./pinx-cli.md)
- [Seu primeiro app](./your-first-app.md)
- [Estrutura do projeto](./structure.md)
- [O que é o Pinoox?](../introduction/what-is-pinoox.md)

---

[← Voltar ao índice](../README.md)

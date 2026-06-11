# Recursos do Pinoox

[← Voltar ao índice](../README.md)

O Pinoox 3.x foi projetado para um ecossistema PHP modular: vários apps independentes sobre um núcleo compartilhado, scaffolding via CLI e ferramentas integradas para HTTP, banco de dados, temas e autenticação.

---

## Arquitetura HMVC e apps independentes

Cada app em `apps/{package}/` tem uma estrutura MVC completa:

| Camada | Caminho de exemplo |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

Adicionar ou desativar um app não afeta os demais.

---

## CLI e desenvolvimento rápido

A partir da raiz do projeto:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

A CLI gera o layout de pastas padrão, o `app.php` e os arquivos de rota iniciais.

---

## Roteamento e Named Actions

Os caminhos de URL e os handlers lógicos ficam separados:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

Esse padrão facilita refatoração e testes.

---

## Flow (middleware)

Antes de uma requisição chegar ao controller, os Flows são executados — para autenticação, autorização, logging e mais:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Registre os aliases de Flow no `app.php`.

---

## Views e temas

- Templates Twig em `theme/{themeName}/`
- Renderize com **`View::render()`**
- Suporte a SPA com Vite no tema (Vue/React)

---

## Banco de dados e Eloquent

- Query Builder e Eloquent via o Portal `DB`
- Migrations e seeders em `database/migrations/` de cada app
- Prefixo de tabela baseado no nome do pacote (ex.: `com_acme_blog_posts`)

---

## API e respostas JSON

Estenda **`ApiController`** e use o envelope padrão:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Internacionalização

Arquivos de tradução em `lang/{locale}/*.lang.php` — ideais para apps multilíngues.

---

## Documentos relacionados

- [O que é o Pinoox?](./what-is-pinoox.md)
- [Instalando o Pinoox](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Voltar ao índice](../README.md)

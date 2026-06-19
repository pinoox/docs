# Documentação Pinoox

Documentação oficial para desenvolver aplicativos na plataforma Pinoox (PHP 8.2+, arquitetura HMVC).

Cada guia descreve **uma abordagem recomendada** com exemplos práticos. Escolha uma seção abaixo ou navegue por tópico.

**Idiomas:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](./README.md) · [Deutsch](../de/README.md)

---

### Introdução

#### [O que é o Pinoox?](./introduction/what-is-pinoox.md)
#### [Recursos do Pinoox](./introduction/features-pinoox.md)
#### [Contribuindo com o Pinoox](./introduction/contributions.md)

### Primeiros passos

#### [Instalando o Pinoox](./start/installing-pinoox.md)
#### [Seu primeiro app](./start/your-first-app.md)
#### [Estrutura do projeto](./start/structure.md)
#### [Referência da CLI Pinoox](./start/cli-reference.md)
#### [Pinx CLI (projetos de app único)](./start/pinx-cli.md)
#### [Referência do manifesto app.php](./start/app-manifest.md)

### Guias práticos

#### [Passo a passo: App de API de notas](./examples/simple-api-app.md)
#### [Tutorial: app web de agenda telefônica](./examples/phonebook-app.md)
#### [Passo a passo: App de formulário de contato](./examples/contact-form-app.md)
#### [Passo a passo: App de blog simples](./examples/blog-app.md)
#### [Passo a passo: Quadro de tarefas (Todo)](./examples/task-board-app.md)
#### [Passo a passo: App de galeria de imagens](./examples/gallery-app.md)
#### [Tutorial: painel SPA Vue](./examples/vue-spa-app.md)
#### [Tutorial: painel SPA React](./examples/react-spa-app.md)
#### [Tutorial: Vite híbrido (Twig + widget JS)](./examples/vite-hybrid-app.md)

### Conceitos básicos

#### [Router](./basic/routers.md)
#### [Controllers](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [Requisição HTTP](./basic/requests.md)
#### [Resposta HTTP](./basic/responses.md)
#### [URL e construção de links](./basic/url.md)
#### [Caminho de arquivo](./basic/path.md)
#### [Validação](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Templates Twig](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Idioma e tradução](./basic/language.md)

### Tópicos avançados

#### [Pinker e Cache](./advanced/pinker.md)
#### [Serviços do App (Component + Portal)](./advanced/services.md)
#### [Helpers Globais](./advanced/helpers.md)
#### [Envio de E-mail](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [Gerenciamento de Usuários](./advanced/user-management.md)
#### [Gerenciamento de Arquivos](./advanced/file-management.md)
#### [Protocolo Pinion](./advanced/pinion.md)
#### [Gerenciamento de Tokens](./advanced/token-management.md)
#### [Acesso e Permissões](./advanced/access-permissions.md)
#### [Transport (recursos compartilhados)](./advanced/transport.md)
#### [boot.php e Events](./advanced/boot-and-events.md)
#### [Agendamento (cron)](./advanced/schedule.md)

### Banco de dados

#### [Primeiros passos com banco de dados](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Paginação](./database/pagination.md)
#### [Migrations](./database/migrations.md)
#### [Patches (atualizações de dados)](./database/patches.md)

### Eloquent ORM

#### [Primeiros passos com Eloquent ORM](./eloquent-orm/getting-started.md)
#### [Relacionamentos Eloquent](./eloquent-orm/relationships.md)
#### [Coleções Eloquent](./eloquent-orm/collections.md)
#### [Mutators e Casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Serialização de Model](./eloquent-orm/serialization.md)
#### [Dados de teste — Seeders](./eloquent-orm/factories.md)

### Testes

#### [Primeiros passos com testes no Pinoox](./test/getting-started.md)
#### [Testes HTTP no Pinoox](./test/http-tests.md)
#### [Testes de console no Pinoox](./test/console-tests.md)
#### [Testes de browser (HTML) no Pinoox](./test/browser-tests.md)
#### [Testes de banco de dados no Pinoox](./test/database.md)
#### [Testes de serialização no Pinoox](./test/serialization.md)
#### [Mocking no Pinoox](./test/mocking.md)

### Perguntas frequentes

#### [Problemas comuns](./faq/common-issues.md)
#### [Contato e suporte](./faq/contact-support.md)

---

### Código-fonte
**Código de exemplo:** [docs/source/](../source/) — código completo de cada guia

Guias passo a passo para apps reais — use após ler o básico, quando quiser código prático.

---

### Como ler esta documentação

1. Comece por **Introdução** e **Primeiros passos** se você é novo no Pinoox.
2. Siga os **Guias práticos** — construa uma API JSON e um site simples passo a passo.
3. Leia os **Conceitos básicos** ao criar rotas, controllers e views.
4. Use **Banco de dados** e **Eloquent ORM** ao adicionar persistência.
5. Consulte **Tópicos avançados** para auth, arquivos, Pinker e serviços compartilhados.
6. Use **Testes** antes de publicar em produção.

Todo o código do app fica em `apps/{package}/`. O núcleo do framework é `vendor/pinoox/pincore/` — não coloque lógica de negócio do app lá.

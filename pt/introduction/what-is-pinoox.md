# O que é o Pinoox?

[← Voltar ao índice](../README.md)

O Pinoox é um framework PHP moderno e de código aberto (3.x), construído sobre a arquitetura HMVC e o conceito de **app**. Ele torna o desenvolvimento web modular simples e direto: cada app é uma unidade MVC independente em `apps/{package}/`, enquanto o núcleo compartilhado do framework fica em `vendor/pinoox/pincore/`.

---

## Arquitetura centrada em apps

Em uma única instalação do Pinoox, vários apps independentes rodam lado a lado:

```
{project_root}/
├── index.php              ← ponto de entrada web
├── pinoox                 ← ponto de entrada da CLI
├── composer.json
├── vendor/pinoox/pincore/ ← núcleo do framework (edite apenas para mudanças no núcleo)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← seu app
```

- **Projeto** — a pasta que contém `index.php` e `apps/` (o nome da pasta não importa).
- **App** — um módulo completo com seus próprios controladores, modelos, rotas, tema e configuração.
- **Core** — o motor compartilhado (router, HTTP, banco de dados, Twig, CLI e mais).

Escreva a lógica de negócio em `apps/`, não em `vendor/pinoox/pincore/`.

---

## Ciclo de vida da requisição HTTP

```
Navegador → index.php → inicialização (bootstrap)
       → resolve o app ativo (domínio ou prefixo de URL)
       → carrega app.php e routes/
       → Flows → Controller → Model (opcional) → View ou JSON
```

---

## Nomenclatura de apps

Formato de pacote recomendado:

```
com_{vendor}_{name}
```

Exemplo: `com_acme_shop` — o nome da pasta, o valor de `package` em `app.php` e o segmento do namespace devem ser todos iguais.

---

## Ideal para

- Sites com múltiplas seções e painéis administrativos, onde cada seção pode ser um app separado
- Equipes que querem desenvolver, testar e manter módulos de forma independente
- Projetos PHP 8.1+ com Composer e a CLI integrada (`php pinoox …`)

---

## Documentos relacionados

- [Recursos do Pinoox](./features-pinoox.md)
- [Instalando o Pinoox](../start/installing-pinoox.md)
- [Seu primeiro app](../start/your-first-app.md)
- [Tutorial da API de notas](../examples/simple-api-app.md)
- [Tutorial da agenda telefônica](../examples/phonebook-app.md)
- [Tutorial do formulário de contato](../examples/contact-form-app.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)

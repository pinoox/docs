# Contribuindo com o Pinoox

[← Voltar ao índice](../README.md)

O Pinoox é um projeto de código aberto. Suas contribuições — de relatórios de bugs a pull requests — ajudam a melhorar o framework e sua documentação.

---

## Formas de contribuir

| Tipo | Descrição |
|------|-------------|
| Relatório de bug | Issue no GitHub com os passos para reproduzir |
| Solicitação de recurso | Issue descrevendo o caso de uso |
| Pull Request | Correção de bug ou recurso no repositório apropriado |
| Documentação | Melhorar os arquivos em `docs/` (persa ou inglês) |
| App de código aberto | Publicar um app Pinoox para a comunidade |

---

## Relatando bugs

Ao abrir uma Issue, inclua:

1. **Título** — um resumo curto do problema
2. **Passos para reproduzir** — passo a passo
3. **Comportamento esperado** vs **comportamento real**
4. **Ambiente** — versão do PHP, versão do Pinoox/pincore, sistema operacional
5. **Código de exemplo** — quando possível

[Issues do Pinoox no GitHub](https://github.com/pinoox/pinoox/issues)

---

## Pull requests

### Repositórios

- **pinoox/pinoox** — projeto de exemplo, apps do sistema, launcher
- **pinoox/pincore** — núcleo do framework (`vendor/pinoox/pincore/`)

Envie mudanças no core para o pincore, e não apenas para a cópia local em `vendor/` do seu projeto.

### Estratégia de branches (3.x)

- **Correções de bugs** → branch estável atual (ex.: `3.x`)
- **Recursos pequenos e compatíveis** → mesma branch estável
- **Mudanças incompatíveis ou de grande porte** → `master` / branch da próxima versão

### Padrões de código

- [PSR-12](https://www.php-fig.org/psr/psr-12/) para estilo de código
- [PSR-4](https://www.php-fig.org/psr/psr-4/) para autoloading
- PHP 8.1+
- Mensagens de commit claras e no imperativo (ex.: `Fix route validation for missing actions`)

---

## Segurança

Reporte vulnerabilidades de segurança **de forma privada**:

`security@pinoox.com`

---

## Contato

- Suporte: `support@pinoox.com`
- [Repositório no GitHub](https://github.com/pinoox/pinoox)

---

## Documentos relacionados

- [O que é o Pinoox?](./what-is-pinoox.md)
- [Recursos do Pinoox](./features-pinoox.md)

---

[← Voltar ao índice](../README.md)

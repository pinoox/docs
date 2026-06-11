# Contato e suporte

[← Voltar ao índice](../README.md)

Se ainda houver um bloqueio após revisar [Problemas comuns](./common-issues.md), use os canais oficiais abaixo. Antes de contatar o suporte, prepare a versão do Pinoox, versão do PHP, mensagem de erro e passos para reproduzir.

---

## Suporte geral

**E-mail:** [support@pinoox.com](mailto:support@pinoox.com)

Indicado para:

- Dúvidas de instalação e deploy
- Comportamento inesperado do framework
- Orientação sobre HMVC e arquitetura de apps

Inclua no e-mail:

1. Versão do Pinoox (`composer.json` → `version` ou tag git)
2. Versão do PHP (`php -v`)
3. SO e servidor web (Apache/nginx, MAMP, cPanel, …)
4. Texto completo do erro ou screenshot
5. Passos mínimos para reproduzir

---

## GitHub Issues

Para bugs confirmados, pedidos de recurso e discussão técnica pública:

**Repositório:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Antes de abrir uma nova issue:

- Busque issues duplicadas
- Teste na release estável/beta mais recente
- Se relacionado ao `pincore`, verifique também o pacote `pinoox/pincore`

Modelo sugerido de issue:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.1.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## Relatos de segurança

**E-mail:** [security@pinoox.com](mailto:security@pinoox.com)

**Somente** para vulnerabilidades de segurança — SQL injection, bypass de auth, RCE, exposição de segredos.

- Não publique detalhes publicamente (issue no GitHub) até haver patch
- Quando possível, inclua PoC mínimo e descrição de impacto

---

## Contribuir com código

Para PRs e desenvolvimento do framework:

- [Contribuindo](../introduction/contributions.md)
- Fork → branch → teste (`php pinoox test`) → Pull Request

---

## Recursos de autoajuda

| Tópico | Doc |
|-------|-----|
| Instalação | [installing-pinoox.md](../start/installing-pinoox.md) |
| Primeiro app | [your-first-app.md](../start/your-first-app.md) |
| Problemas comuns | [common-issues.md](./common-issues.md) |
| Testes | [getting-started.md](../test/getting-started.md) |

**Site:** [pinoox.com](https://www.pinoox.com/)

---

## Documentação relacionada

- [Problemas comuns](./common-issues.md)
- [O que é Pinoox?](../introduction/what-is-pinoox.md)
- [Contribuindo](../introduction/contributions.md)
- [Instalando o Pinoox](../start/installing-pinoox.md)

---

[← Voltar ao índice](../README.md)

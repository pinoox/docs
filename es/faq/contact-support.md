# Contactar soporte

[← Volver al índice](../README.md)

Si sigues bloqueado tras revisar [Problemas frecuentes](./common-issues.md), usa los canales oficiales siguientes. Antes de contactar soporte, prepara tu versión de Pinoox, versión PHP, mensaje de error y pasos de reproducción.

---

## Soporte general

**Email:** [support@pinoox.com](mailto:support@pinoox.com)

Adecuado para:

- Preguntas de instalación y despliegue
- Comportamiento inesperado del framework
- Orientación sobre arquitectura HMVC y apps

Incluye en tu email:

1. Versión de Pinoox (`composer.json` → `version` o git tag)
2. Versión PHP (`php -v`)
3. SO y servidor web (Apache/nginx, MAMP, cPanel, …)
4. Texto completo del error o captura de pantalla
5. Pasos mínimos de reproducción

---

## GitHub Issues

Para bugs confirmados, solicitudes de funciones y discusión técnica pública:

**Repositorio:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Antes de abrir un issue nuevo:

- Busca issues duplicados
- Prueba en la última versión estable/beta
- Si está relacionado con `pincore`, revisa también el paquete `pinoox/pincore`

Plantilla de issue sugerida:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.2.x
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

## Informes de seguridad

**Email:** [security@pinoox.com](mailto:security@pinoox.com)

**Solo** para vulnerabilidades de seguridad — inyección SQL, bypass de auth, RCE, exposición de secretos.

- No publiques detalles públicamente (issue de GitHub) hasta que haya un parche
- Cuando sea posible, incluye un PoC mínimo y descripción del impacto

---

## Contribuir código

Para PRs y desarrollo del framework:

- [Contribuciones](../introduction/contributions.md)
- Fork → rama → test (`php pinoox test`) → Pull Request

---

## Recursos de autoayuda

| Tema | Doc |
|-------|-----|
| Instalación | [installing-pinoox.md](../start/installing-pinoox.md) |
| Primera app | [your-first-app.md](../start/your-first-app.md) |
| Problemas frecuentes | [common-issues.md](./common-issues.md) |
| Testing | [getting-started.md](../test/getting-started.md) |

**Sitio web:** [pinoox.com](https://www.pinoox.com/)

---

## Documentación relacionada

- [Problemas frecuentes](./common-issues.md)
- [¿Qué es Pinoox?](../introduction/what-is-pinoox.md)
- [Contribuciones](../introduction/contributions.md)
- [Instalación de Pinoox](../start/installing-pinoox.md)

---

[← Volver al índice](../README.md)

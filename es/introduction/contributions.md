# Contribuir a Pinoox

[← Volver al índice](../README.md)

Pinoox es un proyecto de código abierto. Tus contribuciones — desde reportes de errores hasta pull requests — ayudan a mejorar el framework y su documentación.

---

## Formas de contribuir

| Tipo | Descripción |
|------|-------------|
| Reporte de error | Issue de GitHub con pasos para reproducirlo |
| Solicitud de funcionalidad | Issue describiendo el caso de uso |
| Pull Request | Corrección de error o funcionalidad en el repositorio apropiado |
| Documentación | Mejorar los archivos bajo `docs/` (persa o inglés) |
| App de código abierto | Publicar una app de Pinoox para la comunidad |

---

## Reportar errores

Al abrir un Issue, incluye:

1. **Título** — un resumen breve del problema
2. **Pasos para reproducir** — paso a paso
3. **Comportamiento esperado** vs **comportamiento real**
4. **Entorno** — versión de PHP, versión de Pinoox/pincore, sistema operativo
5. **Código de ejemplo** — cuando sea posible

[Issues de GitHub de Pinoox](https://github.com/pinoox/pinoox/issues)

---

## Pull requests

### Repositorios

- **pinoox/pinoox** — proyecto de ejemplo, apps del sistema, launcher
- **pinoox/pincore** — núcleo del framework (`vendor/pinoox/pincore/`)

Envía los cambios del núcleo a pincore, no solo a la copia local de `vendor/` en tu proyecto.

### Estrategia de ramas (3.x)

- **Correcciones de errores** → rama estable actual (p. ej. `3.x`)
- **Funcionalidades pequeñas y compatibles** → la misma rama estable
- **Cambios mayores o incompatibles** → `master` / rama de la próxima versión

### Estándares de código

- [PSR-12](https://www.php-fig.org/psr/psr-12/) para el estilo de código
- [PSR-4](https://www.php-fig.org/psr/psr-4/) para el autoloading
- PHP 8.2+
- Mensajes de commit claros e imperativos (p. ej. `Fix route validation for missing actions`)

---

## Seguridad

Reporta las vulnerabilidades de seguridad **de forma privada**:

`security@pinoox.com`

---

## Contacto

- Soporte: `support@pinoox.com`
- [Repositorio de GitHub](https://github.com/pinoox/pinoox)

---

## Documentación relacionada

- [¿Qué es Pinoox?](./what-is-pinoox.md)
- [Características de Pinoox](./features-pinoox.md)

---

[← Volver al índice](../README.md)

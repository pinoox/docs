# Instalación de Pinoox

[← Volver al índice](../README.md)

Esta guía cubre la instalación de Pinoox 3.x. Hay dos formas de empezar:

| Vía | Ideal para |
|-------|----------|
| **A. Una sola app con [Pinx CLI](./pinx-cli.md)** | Construir una sola app — el inicio más rápido, sin interfaz de manager |
| **B. Plataforma completa (clásica)** | Alojar múltiples apps con el instalador gráfico y el manager |

---

## Requisitos

| Herramienta | Versión |
|------|---------|
| PHP | 8.2 o superior (con ext-mysqli, ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (opcional) | 18+ — solo para builds de temas frontend |

---

## Vía A — Una sola app con Pinx CLI

Instala la [Pinx CLI](./pinx-cli.md) una vez, crea una nueva app y ejecútala:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # sugiere com_my_shop — confirma o edita en el asistente
cd my-shop
cp .env.example .env          # configura DB_* si usas una base de datos
pinx setup                    # migra la plataforma + la app, ejecuta los seeders
pinx dev                      # http://127.0.0.1:8000
```

O sin instalación global, mediante la plantilla de proyecto:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

Ejecuta `pinx doctor` en cualquier momento para comprobar PHP, el entorno (env), la base de datos y la preparación del build. Consulta la [guía completa de Pinx CLI](./pinx-cli.md) para el flujo de trabajo diario y la referencia de comandos.

---

## Vía B — Plataforma completa (clásica)

### 1. Obtén el proyecto

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Alternativamente, descarga la última versión desde [GitHub](https://github.com/pinoox/pinoox), extráela y luego ejecuta `composer install`.

---

### 2. Colócalo en tu servidor web

Pon la carpeta del proyecto en tu document root:

| Entorno | Ruta de ejemplo |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Configura el document root en la **raíz del proyecto** (la carpeta que contiene `index.php`) — no en una subcarpeta `public`.

---

### 3. Crea la base de datos

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Ejecuta el instalador

Abre tu navegador:

```
http://localhost/pinoox
```

Se ejecuta la app del sistema `com_pinoox_installer`. Los pasos de la interfaz gráfica son:

1. Comprobar los requisitos de PHP
2. Aceptar el acuerdo de licencia
3. Introducir las credenciales de la base de datos
4. Crear la cuenta de administrador
5. Finalizar la instalación

---

### 5. Después de la instalación

Estructura principal:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← apps
├── vendor/pinoox/pincore/  ← núcleo
└── config/             ← configuración del proyecto
```

Crea tu primera app:

```bash
php pinoox app:create com_acme_blog
```

---

## Solución rápida de problemas

| Problema | Solución |
|---------|-----|
| Página en blanco | Ejecuta `composer install` y revisa los logs de errores de PHP |
| 404 en sub-rutas | Habilita mod_rewrite / `.htaccess` |
| Error de extensión faltante | Habilita ext-mysqli y ext-zip en php.ini |
| El instalador no se abre | Verifica el document root y los permisos de escritura en las carpetas de runtime |

---

## Documentación relacionada

- [Pinx CLI (una sola app)](./pinx-cli.md)
- [Tu primera app](./your-first-app.md)
- [Estructura del proyecto](./structure.md)
- [¿Qué es Pinoox?](../introduction/what-is-pinoox.md)

---

[← Volver al índice](../README.md)

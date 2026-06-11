# Problemas frecuentes

[← Volver al índice](../README.md)

Soluciones prácticas para errores habituales durante la instalación, el runtime y el desarrollo en Pinoox. Cada sección recomienda **un enfoque**.

---

## Falla `composer install`

**Síntomas:** extensión faltante, versión PHP baja o timeout de red.

**Solución:**

1. Habilita PHP 8.1+ y las extensiones `mysqli`, `zip`, `mbstring`, `json`.
2. Ejecuta la comprobación de plataforma antes de instalar:

```bash
php launcher/check.php
```

3. Instala de nuevo:

```bash
composer install --no-interaction
```

En hosting compartido, si `composer` no está en PATH, construye vendor localmente y súbelo.

---

## Errores de permisos (acceso a archivos)

**Síntomas:** No se puede escribir en `cache/`, `storage/`, `pinker/`.

**Solución (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

El usuario del servidor web (p. ej. `www-data` o `apache`) debe poder escribir en las carpetas con permisos de escritura. En Windows/MAMP, mantén la carpeta del proyecto fuera de `Program Files`.

---

## `.htaccess` / rewrite no funciona

**Síntomas:** 404 en todas las URLs excepto `index.php`; la API no devuelve JSON en el navegador.

**Solución:**

1. Habilita Apache `mod_rewrite`.
2. Establece `AllowOverride All` para el DocumentRoot.
3. Asegúrate de que `.htaccess` existe en la raíz del proyecto.
4. Prueba rápida: `http://localhost/pinoox/api/v1/ping` — si ves JSON, el rewrite funciona.

En nginx, escribe reglas `try_files` e `index.php` en la config del servidor en lugar de `.htaccess`.

---

## Falla la conexión a la base de datos

**Síntomas:** `SQLSTATE[HY000] [2002] Connection refused` o acceso denegado.

**Solución:**

1. Asegúrate de que MySQL/MariaDB está en ejecución.
2. Comprueba valores en `config/database.config.php` o `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Crea la base de datos de antemano (`CREATE DATABASE ... utf8mb4`).
4. En cPanel, el host puede no ser `localhost` — usa el hostname del panel.

---

## Requiere reconstrucción Pinker

**Síntomas:** config o rutas obsoletas; los cambios en `app.php` no se aplican.

**Solución:**

```bash
php pinoox pinker:rebuild com_my_shop
# o alias:
php pinoox bake com_my_shop

# todas las apps:
php pinoox pinker:rebuild all
```

Tras cambiar rutas, config o desplegar en producción, suele hacer falta una reconstrucción.

---

## Ruta no encontrada (404 en endpoint)

**Síntomas:** la ruta está definida en código pero obtienes 404.

**Solución:**

1. Asegúrate de que el archivo de rutas está en `apps/{package}/routes/` y listado en `app.php` → `router.routes`.
2. Coincide la URL con el prefijo de app (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Ejecuta una reconstrucción Pinker (ver arriba).
4. Usa el método HTTP correcto (`GET` vs `POST`).

---

## 404 — app no resuelta

**Síntomas:** página por defecto o 404; se carga la app incorrecta.

**Solución:**

1. Comprueba el mapeo host/ruta:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. Establece host y ruta correctamente en `config/domain.config.php` (o el mapa relevante).
3. Asegúrate de `'enable' => true` en el `app.php` de la app.
4. El nombre de carpeta de la app debe coincidir con `'package'` en `app.php` (p. ej. `com_my_shop`).

---

## Fallan los tests

```bash
php pinoox test com_my_shop
```

- `.env.testing` con una DB separada
- migraciones ejecutadas: `php pinoox migrate com_my_shop`
- tras `fakeApp()` → `deleteFakeApp()`

Detalles: [Primeros pasos con testing](../test/getting-started.md)

---

## Documentación relacionada

- [Instalación de Pinoox](../start/installing-pinoox.md)
- [Estructura del proyecto](../start/structure.md)
- [Routers](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Primeros pasos con base de datos](../database/getting-started.md)
- [Contactar soporte](./contact-support.md)

---

[← Volver al índice](../README.md)

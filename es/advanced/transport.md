# Transport (recursos compartidos)

[← Volver al índice](../README.md)

En la arquitectura HMVC, las apps pueden compartir usuarios, auth, archivos y permisos entre sí mediante el bloque **`transport`** en `app.php`. Sin transport, cada app mantiene cada recurso **local** a su propio paquete.

| Término | Significado |
|------|---------|
| **`platform`** | Ámbito lógico compartido — las filas DB compartidas usan `app = platform` |
| **`pincore/`** | Solo carpeta física del framework — **nunca** un valor de ámbito transport |

---

## Cómo funciona

Transport tiene dos capas:

1. **Escenario** — un preset de una palabra que se expande a varias claves granulares.
2. **Clave granular** — un nombre de varias palabras para un recurso compartido concreto.

```php
// app.php
'transport' => [
    'full' => 'platform',           // preset de escenario
    'file_storage' => 'local',      // sobrescritura granular
],
```

**Orden de resolución:** clave granular explícita → escenario coincidente.

Las claves granulares siempre ganan sobre la expansión del escenario. Si una clave no está definida y ningún escenario la cubre, la app mantiene ese recurso **local** (paquete actual).

---

## Valores de ámbito

Cada escenario o clave granular recibe un ámbito:

| Ámbito | Significado |
|-------|---------|
| `local` | Paquete de la app actual (por defecto si se omite) |
| `platform` | Ámbito de plataforma compartido (`app = platform`, tablas `pinx_*`) |
| `host` | App que abrió esta (preview / `App::meeting()`) |
| `{package}` | App explícita, p. ej. `com_pinoox_manager` |

Para **`auth_config`** y **`auth_cookie`**, `platform` y `{package}` se resuelven a la app que **proporciona la configuración de auth** (típicamente `com_pinoox_manager` si está instalado).

---

## Referencia de escenarios

Presets de una palabra. Úsalos en `app.php` como `'transport' => ['{scenario}' => '{scope}']`.

| Escenario | Descripción | Claves granulares incluidas |
|----------|-------------|------------------------|
| `full` | Todos los recursos compartidos | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Sistema de login: cuentas, auth, tokens de sesión | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | Subidas de archivos y metadatos | `file_storage` |
| `access` | Roles y permisos | `access_table` |

---

## Referencia de claves granulares

Nombres de recurso de varias palabras. Úsalas para compartir o sobrescribir un solo recurso.

| Clave granular | Controla | Usado por |
|--------------|----------|---------|
| `user_table` | Columna `app` de `UserModel` / ámbito global | Cuentas de usuario |
| `auth_config` | Modo auth, secreto JWT, vidas (`auth` block source) | `AuthConfig`, flujo de login |
| `auth_cookie` | Clave cliente / nombre cookie (`auth.key`) | Cookie y almacenamiento de token SPA |
| `session_token` | Columna `app` de `TokenModel` / filas de sesión DB | Persistencia de sesión |
| `file_storage` | Columna `app` de `FileModel` / rutas de subida | Subidas y metadatos de archivos |
| `access_table` | Ámbito `app` del modelo de roles y permisos | `RoleModel`, `PermissionModel`, `can()` |

---

## Configuraciones comunes

**Proveedor de auth para la plataforma (p. ej. manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**App consumidora — todo compartido, sin bloque auth local:**

```php
'transport' => ['full' => 'platform'],
```

**Solo login compartido:**

```php
'transport' => ['user' => 'platform'],
```

**App independiente** — omite `transport`, o fija todo en local:

```php
'transport' => ['user' => 'local'],
```

**Sobrescribir un recurso dentro de un escenario:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## API de código

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // paquete resuelto para una clave granular
Transport::authSource();                       // app que posee la config auth, o null
Transport::sharesAuthWith($guest, $host);      // comprobación auth entre apps
Transport::resolved();                         // todas las claves granulares → ámbito
Transport::activeScenarios();                  // p. ej. ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Base de datos

Las tablas con ámbito platform usan conexión **`platform`** y prefijo **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## Documentación relacionada

- [Manifiesto app.php](../start/app-manifest.md)
- [Gestión de usuarios](./user-management.md)
- [Acceso y permisos](./access-permissions.md)
- [Gestión de archivos](./file-management.md)

---

[← Volver al índice](../README.md)

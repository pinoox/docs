# Config

[← Volver al índice](../README.md)

La configuración de Pinoox 3.x se almacena en archivos PHP bajo `config/` (núcleo y app). El enfoque estándar: **`config('key')`** para leer y **`config('name')->set(...)->save()`** para escribir.

---

## Lectura

```php
// Clave simple
$siteName = config('app.name');

// Clave anidada (notación de puntos)
$merchant = config('payment.merchant_id');

// Valor por defecto
$timeout = config('api.timeout', 30);

// Objeto config para encadenar
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## Escritura y guardado

**Llama siempre a `save()` tras los cambios:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## Datos anidados — `setLinear` / `getLinear`

```php
// Lectura
$themeName = config('theme.panel.name');

// Escritura
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Ubicaciones de archivos de config

| Ubicación | Contenido |
|----------|----------|
| `pincore/config/*.config.php` | Config del núcleo (DB, dominio, …) |
| `apps/{package}/config/*.config.php` | Config de la app |
| `pinker/config/` | Versión horneada (producción) |
| `pinker/state/config/` | Sobrescrituras post-instalación (p. ej. DB) |

En desarrollo, los valores sensibles se leen desde `.env` vía `env()` / `_env()`.

---

## Ejemplo: configuración de pasarela de pago

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'stripe',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller o Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## Ejemplo: menú dinámico

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'Reports', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Portal — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

En la práctica `config()` envuelve el mismo Portal — basta un estilo.

---

## Consejos

- No subas secretos (claves API, contraseñas DB) a git; usa `.env` o `pinker/state`.
- Nombre de archivo: `{name}.config.php` → `config('{name}.key')`.
- Tras desplegar en producción, ejecuta `php pinoox pinker:rebuild` para hornear la config.

---

## Documentación relacionada

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [Ruta de archivos](./path.md)
- [Manifiesto app.php](../start/app-manifest.md)

---

[← Volver al índice](../README.md)

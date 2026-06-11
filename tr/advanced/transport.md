# Transport (paylaşılan kaynaklar)

[← Dizine dön](../README.md)

HMVC mimarisinde uygulamalar `app.php` içindeki **`transport`** bloğu üzerinden kullanıcıları, kimlik doğrulamayı, dosyaları ve izinleri birbirleriyle paylaşabilir. Transport olmadan her uygulama tüm kaynakları kendi paketine özel **local** tutar.

| Terim | Anlam |
|------|---------|
| **`platform`** | Mantıksal paylaşılan kapsam — paylaşılan DB satırları `app = platform` kullanır |
| **`pincore/`** | Yalnızca fiziksel framework klasörü — transport kapsam değeri **asla** olamaz |

---

## Nasıl çalışır

Transport iki katmana sahiptir:

1. **Senaryo** — birden fazla ayrıntılı anahtara genişleyen tek kelimelik ön ayar.
2. **Ayrıntılı anahtar** — tek bir paylaşılan kaynak için çok kelimelik ad.

```php
// app.php
'transport' => [
    'full' => 'platform',           // scenario preset
    'file_storage' => 'local',      // granular override
],
```

**Çözümleme sırası:** açık ayrıntılı anahtar → eşleşen senaryo.

Ayrıntılı anahtarlar senaryo genişlemesine her zaman üstün gelir. Bir anahtar ayarlanmamışsa ve hiçbir senaryo kapsamıyorsa uygulama o kaynağı **local** (mevcut paket) tutar.

---

## Kapsam değerleri

Her senaryo veya ayrıntılı anahtara bir kapsam atanır:

| Kapsam | Anlam |
|-------|---------|
| `local` | Mevcut uygulama paketi (belirtilmediğinde varsayılan) |
| `platform` | Paylaşılan platform kapsamı (`app = platform`, `pinx_*` tabloları) |
| `host` | Bu uygulamayı açan uygulama (önizleme / `App::meeting()`) |
| `{package}` | Açık uygulama, ör. `com_pinoox_manager` |

**`auth_config`** ve **`auth_cookie`** için `platform` ve `{package}`, **kimlik doğrulama ayarlarını sağlayan** uygulamaya çözümlenir (kurulu olduğunda genellikle `com_pinoox_manager`).

---

## Senaryo referansı

Tek kelimelik ön ayarlar. `app.php` içinde `'transport' => ['{scenario}' => '{scope}']` olarak kullanın.

| Senaryo | Açıklama | Dahil ayrıntılı anahtarlar |
|----------|-------------|------------------------|
| `full` | Tüm paylaşılan kaynaklar | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Giriş sistemi: hesaplar, auth, session token'ları | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | Dosya yüklemeleri ve meta veri | `file_storage` |
| `access` | Roller ve izinler | `access_table` |

---

## Ayrıntılı anahtar referansı

Çok kelimelik kaynak adları. Tek bir kaynağı paylaşmak veya geçersiz kılmak için kullanın.

| Ayrıntılı anahtar | Kontrol ettiği | Kullanan |
|--------------|----------|---------|
| `user_table` | `UserModel` `app` sütunu / global kapsam | Kullanıcı hesapları |
| `auth_config` | Auth modu, JWT secret, süreler (`auth` bloğu kaynağı) | `AuthConfig`, giriş akışı |
| `auth_cookie` | İstemci anahtarı / cookie adı (`auth.key`) | Cookie ve SPA token depolama |
| `session_token` | `TokenModel` `app` sütunu / DB session satırları | Session kalıcılığı |
| `file_storage` | `FileModel` `app` sütunu / yükleme yolları | Yüklemeler ve dosya meta verisi |
| `access_table` | Rol ve izin modeli `app` kapsamı | `RoleModel`, `PermissionModel`, `can()` |

---

## Yaygın kurulumlar

**Platform için kimlik doğrulama sağlayıcısı (ör. manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Tüketici uygulama — her şey paylaşımlı, yerel auth bloğu yok:**

```php
'transport' => ['full' => 'platform'],
```

**Yalnızca paylaşımlı giriş:**

```php
'transport' => ['user' => 'platform'],
```

**Bağımsız uygulama** — `transport`'u atlayın veya her şeyi local'e sabitleyin:

```php
'transport' => ['user' => 'local'],
```

**Senaryo içinde tek kaynağı geçersiz kılma:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## Kod API'si

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // resolved package for a granular key
Transport::authSource();                       // app that owns auth settings, or null
Transport::sharesAuthWith($guest, $host);      // cross-app auth check
Transport::resolved();                         // all granular keys → scope
Transport::activeScenarios();                  // e.g. ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Veritabanı

Platform kapsamlı tablolar **`platform`** bağlantısı ve **`pinx_`** öneki kullanır.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## İlgili dokümantasyon

- [app.php manifest](../start/app-manifest.md)
- [Kullanıcı yönetimi](./user-management.md)
- [Erişim ve izinler](./access-permissions.md)
- [Dosya yönetimi](./file-management.md)

---

[← Dizine dön](../README.md)

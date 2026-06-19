# Sık karşılaşılan sorunlar

[← Dizine dön](../README.md)

Pinoox'ta kurulum, çalışma zamanı ve geliştirme sırasında sık görülen hatalar için pratik çözümler. Her bölüm **tek bir yaklaşım** önerir.

---

## `composer install` başarısız

**Belirtiler:** eksik extension, düşük PHP sürümü veya ağ zaman aşımı.

**Çözüm:**

1. PHP 8.2+ ve `mysqli`, `zip`, `mbstring`, `json` extension'larını etkinleştirin.
2. Kurulumdan önce platform kontrolünü çalıştırın:

```bash
php launcher/check.php
```

3. Tekrar kurun:

```bash
composer install --no-interaction
```

Paylaşımlı hosting'de `composer` PATH'te değilse vendor'ı yerelde oluşturup yükleyin.

---

## İzin hataları (dosya erişimi)

**Belirtiler:** `cache/`, `storage/`, `pinker/` yazılamıyor.

**Çözüm (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

Web sunucusu kullanıcısı (ör. `www-data` veya `apache`) yazılabilir klasörlere yazabilmelidir. Windows/MAMP'ta projeyi `Program Files` dışında tutun.

---

## `.htaccess` / rewrite çalışmıyor

**Belirtiler:** `index.php` dışındaki tüm URL'lerde 404; tarayıcıda API JSON döndürmüyor.

**Çözüm:**

1. Apache `mod_rewrite` etkinleştirin.
2. DocumentRoot için `AllowOverride All` ayarlayın.
3. Proje kökünde `.htaccess` olduğundan emin olun.
4. Hızlı test: `http://localhost/pinoox/api/v1/ping` — JSON görürseniz rewrite çalışıyordur.

nginx'te `.htaccess` yerine sunucu config'inde `try_files` ve `index.php` kuralları yazın.

---

## Veritabanı bağlantısı başarısız

**Belirtiler:** `SQLSTATE[HY000] [2002] Connection refused` veya erişim reddedildi.

**Çözüm:**

1. MySQL/MariaDB'nin çalıştığından emin olun.
2. `config/database.config.php` veya `.env` değerlerini kontrol edin:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Veritabanını önceden oluşturun (`CREATE DATABASE ... utf8mb4`).
4. cPanel'de host `localhost` olmayabilir — paneldeki hostname'i kullanın.

---

## Pinker yeniden oluşturma gerekli

**Belirtiler:** bayat config veya route'lar; `app.php` değişiklikleri uygulanmıyor.

**Çözüm:**

```bash
php pinoox pinker:rebuild com_my_shop
# or alias:
php pinoox bake com_my_shop

# all apps:
php pinoox pinker:rebuild all
```

Route, config değişikliğinden veya üretime dağıtımdan sonra genellikle yeniden oluşturma gerekir.

---

## Route bulunamadı (endpoint'te 404)

**Belirtiler:** route kodda tanımlı ama 404 alıyorsunuz.

**Çözüm:**

1. Route dosyasının `apps/{package}/routes/` içinde ve `app.php` → `router.routes` listesinde olduğundan emin olun.
2. URL'yi uygulama önekiyle eşleştirin (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Pinker yeniden oluşturma çalıştırın (yukarıya bakın).
4. Doğru HTTP metodunu kullanın (`GET` ve `POST`).

---

## 404 — uygulama çözümlenmedi

**Belirtiler:** varsayılan sayfa veya 404; yanlış uygulama yükleniyor.

**Çözüm:**

1. Yol/host eşlemesini kontrol edin:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. `config/domain.config.php` (veya ilgili harita) içinde host ve yolu doğru ayarlayın.
3. Uygulamanın `app.php` dosyasında `'enable' => true` olduğundan emin olun.
4. Uygulama klasör adı `app.php` içindeki `'package'` ile eşleşmeli (ör. `com_my_shop`).

---

## Testler başarısız

```bash
php pinoox test com_my_shop
```

- Üretimden ayrı DB ile `.env.testing`
- migration'lar çalıştırıldı: `php pinoox migrate com_my_shop`
- `fakeApp()` sonrası → `deleteFakeApp()`

Ayrıntılar: [Teste başlarken](../test/getting-started.md)

---

## İlgili dokümantasyon

- [Pinoox kurulumu](../start/installing-pinoox.md)
- [Proje yapısı](../start/structure.md)
- [Router](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Veritabanına başlarken](../database/getting-started.md)
- [Destek ile iletişim](./contact-support.md)

---

[← Dizine dön](../README.md)

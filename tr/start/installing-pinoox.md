# Pinoox kurulumu

[← Dizine dön](../README.md)

Bu rehber Pinoox 3.x kurulumunu kapsar. Başlamanın iki yolu vardır:

| Yol | En uygun olduğu durum |
|-------|----------|
| **A. [Pinx CLI](./pinx-cli.md) ile tek uygulama** | Tek uygulama geliştirme — en hızlı başlangıç, manager arayüzü yok |
| **B. Tam platform (klasik)** | Grafiksel yükleyici ve manager ile birden fazla uygulama barındırma |

---

## Gereksinimler

| Araç | Sürüm |
|------|---------|
| PHP | 8.1 veya üzeri (ext-mysqli, ext-zip ile) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (isteğe bağlı) | 18+ — yalnızca frontend tema derlemeleri için |

---

## Yol A — Pinx CLI ile tek uygulama

[Pinx CLI](./pinx-cli.md)'yi bir kez kurun, yeni uygulama oluşturun ve çalıştırın:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggests com_my_shop — confirm or edit in the wizard
cd my-shop
cp .env.example .env          # set DB_* if you use a database
pinx setup                    # migrate platform + app, run seeders
pinx dev                      # http://127.0.0.1:8000
```

Veya global kurulum olmadan, proje şablonu üzerinden:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

PHP, env, DB ve derleme hazırlığını kontrol etmek için istediğiniz zaman `pinx doctor` çalıştırın. Günlük iş akışı ve komut referansı için tam [Pinx CLI rehberine](./pinx-cli.md) bakın.

---

## Yol B — Tam platform (klasik)

### 1. Projeyi edinin

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Alternatif olarak [GitHub](https://github.com/pinoox/pinoox)'dan en son sürümü indirin, çıkarın ve `composer install` çalıştırın.

---

### 2. Web sunucunuza yerleştirin

Proje klasörünü document root'unuza koyun:

| Ortam | Örnek yol |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Document root'u **proje köküne** ( `index.php` içeren klasör) ayarlayın — `public` alt klasörüne değil.

---

### 3. Veritabanını oluşturun

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Yükleyiciyi çalıştırın

Tarayıcınızı açın:

```
http://localhost/pinoox
```

Sistem uygulaması `com_pinoox_installer` çalışır. GUI adımları:

1. PHP gereksinimlerini kontrol edin
2. Lisans sözleşmesini kabul edin
3. Veritabanı bilgilerini girin
4. Yönetici hesabını oluşturun
5. Kurulumu tamamlayın

---

### 5. Kurulum sonrası

Ana düzen:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← uygulamalar
├── vendor/pinoox/pincore/  ← çekirdek
└── config/             ← proje config'i
```

İlk uygulamanızı oluşturun:

```bash
php pinoox app:create com_acme_blog
```

---

## Hızlı sorun giderme

| Sorun | Çözüm |
|---------|-----|
| Boş sayfa | `composer install` çalıştırın ve PHP hata loglarını kontrol edin |
| Alt route'larda 404 | mod_rewrite / `.htaccess` etkinleştirin |
| Eksik extension hatası | php.ini'de ext-mysqli ve ext-zip etkinleştirin |
| Yükleyici açılmıyor | Document root ve runtime klasörlerinde yazma izinlerini doğrulayın |

---

## İlgili dokümantasyon

- [Pinx CLI (tek uygulama)](./pinx-cli.md)
- [İlk uygulamanız](./your-first-app.md)
- [Proje yapısı](./structure.md)
- [Pinoox nedir?](../introduction/what-is-pinoox.md)

---

[← Dizine dön](../README.md)

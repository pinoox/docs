# Pinoox nedir?

[← Dizine dön](../README.md)

Pinoox, HMVC mimarisi ve **app** kavramı üzerine kurulu modern, açık kaynaklı bir PHP framework'üdür (3.x). Modüler web geliştirmeyi kolaylaştırır: her uygulama `apps/{package}/` altında bağımsız bir MVC birimidir; paylaşılan framework çekirdeği ise `vendor/pinoox/pincore/` içinde yer alır.

---

## Uygulama merkezli mimari

Tek bir Pinoox kurulumunda birden fazla bağımsız uygulama yan yana çalışır:

```
{project_root}/
├── index.php              ← web giriş noktası
├── pinoox                 ← CLI giriş noktası
├── composer.json
├── vendor/pinoox/pincore/ ← framework çekirdeği (yalnızca çekirdek değişiklikleri için düzenleyin)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← uygulamanız
```

- **Proje** — `index.php` ve `apps/` içeren klasör (klasör adı önemli değildir).
- **Uygulama (App)** — kendi denetleyicileri (Controller), modelleri (Model), yönlendirmeleri (routes), teması ve yapılandırması (config) olan tam bir modül.
- **Core** — paylaşılan motor (router, HTTP, veritabanı, Twig, CLI ve daha fazlası).

İş mantığını `apps/` içinde yazın, `vendor/pinoox/pincore/` içinde değil.

---

## HTTP istek yaşam döngüsü

```
Tarayıcı → index.php → önyükleme (bootstrap)
        → aktif uygulamayı çözümle (domain veya URL öneki)
        → app.php ve routes/ yükle
        → Flow'lar → Controller → Model (isteğe bağlı) → View veya JSON
```

---

## Uygulama adlandırma

Önerilen paket formatı:

```
com_{vendor}_{name}
```

Örnek: `com_acme_shop` — klasör adı, `app.php` içindeki `package` değeri ve namespace segmenti birbiriyle eşleşmelidir.

---

## Uygun olduğu durumlar

- Her bölümün ayrı bir uygulama olabildiği çok bölümlü siteler ve yönetim panelleri
- Modülleri bağımsız geliştirmek, test etmek ve sürdürmek isteyen ekipler
- Composer ve entegre CLI (`php pinoox …`) ile PHP 8.2+ projeleri

---

## İlgili dokümantasyon

- [Pinoox özellikleri](./features-pinoox.md)
- [Pinoox kurulumu](../start/installing-pinoox.md)
- [İlk uygulamanız](../start/your-first-app.md)
- [Notlar API uygulamalı rehber](../examples/simple-api-app.md)
- [Telefon rehberi uygulamalı rehber](../examples/phonebook-app.md)
- [İletişim formu uygulamalı rehber](../examples/contact-form-app.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)

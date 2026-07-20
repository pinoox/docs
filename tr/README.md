# Pinoox Dokümantasyonu

Pinoox platformunda uygulama geliştirmek için resmi geliştirici dokümantasyonu (PHP 8.2+, HMVC mimarisi).

Her rehber, pratik örneklerle **tek bir önerilen yaklaşımı** açıklar. Aşağıdan bir bölüm seçin veya konuya göre göz atın.

**Diller:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](./README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### Giriş

#### [Pinoox nedir?](./introduction/what-is-pinoox.md)
#### [Pinoox özellikleri](./introduction/features-pinoox.md)
#### [Pinoox'a katkıda bulunma](./introduction/contributions.md)

### Başlarken

#### [Pinoox kurulumu](./start/installing-pinoox.md)
#### [İlk uygulamanız](./start/your-first-app.md)
#### [Proje yapısı](./start/structure.md)
#### [Pinoox CLI referansı](./start/cli-reference.md)
#### [Pinx CLI (tek uygulamalı projeler)](./start/pinx-cli.md)
#### [app.php manifest referansı](./start/app-manifest.md)

### Pratik uygulamalı rehberler

#### [Uygulamalı rehber: Notes API uygulaması](./examples/simple-api-app.md)
#### [Uygulamalı rehber: Telefon rehberi web uygulaması](./examples/phonebook-app.md)
#### [Uygulamalı rehber: İletişim formu uygulaması](./examples/contact-form-app.md)
#### [Uygulamalı rehber: Basit blog uygulaması](./examples/blog-app.md)
#### [Uygulamalı rehber: Görev panosu (Todo)](./examples/task-board-app.md)
#### [Uygulamalı rehber: Resim galerisi uygulaması](./examples/gallery-app.md)
#### [Uygulamalı rehber: Vue SPA paneli](./examples/vue-spa-app.md)
#### [Uygulamalı rehber: React SPA paneli](./examples/react-spa-app.md)
#### [Uygulamalı rehber: Vite hibrit (Twig + JS widget)](./examples/vite-hybrid-app.md)

### Temel kavramlar

#### [Router](./basic/routers.md)
#### [Controller](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [HTTP Request](./basic/requests.md)
#### [HTTP Response](./basic/responses.md)
#### [URL ve bağlantı oluşturma](./basic/url.md)
#### [Dosya yolu](./basic/path.md)
#### [Validasyon](./basic/validation.md)
#### [View](./basic/views.md)
#### [Twig şablonları](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Dil ve çeviri](./basic/language.md)

### İleri düzey konular

#### [Pinker ve önbellek](./advanced/pinker.md)
#### [Patch'ler (veri güncellemeleri)](./advanced/patches.md)

#### [Uygulama servisleri (Component + Portal)](./advanced/services.md)
#### [Global helper'lar](./advanced/helpers.md)
#### [E-posta gönderme](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [Kullanıcı yönetimi](./advanced/user-management.md)
#### [Dosya yönetimi](./advanced/file-management.md)
#### [Pinion Protokol�](./advanced/pinion.md)
#### [Token yönetimi](./advanced/token-management.md)
#### [Erişim ve izinler](./advanced/access-permissions.md)
#### [Transport (paylaşılan kaynaklar)](./advanced/transport.md)
#### [boot.php ve event'ler](./advanced/boot-and-events.md)
#### [Zamanlama (cron)](./advanced/schedule.md)

### Veritabanı

#### [Veritabanına başlarken](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Sayfalama](./database/pagination.md)
#### [Migration'lar](./database/migrations.md)

### Eloquent ORM

#### [Eloquent ORM'ye başlarken](./eloquent-orm/getting-started.md)
#### [Eloquent ilişkileri](./eloquent-orm/relationships.md)
#### [Eloquent Collection'lar](./eloquent-orm/collections.md)
#### [Mutator'lar ve cast'ler](./eloquent-orm/mutators-casts.md)
#### [API Resource'lar](./eloquent-orm/api-resources.md)
#### [Model serileştirme](./eloquent-orm/serialization.md)
#### [Test verisi — Seeder'lar](./eloquent-orm/factories.md)

### Test

#### [Pinoox'ta teste başlarken](./test/getting-started.md)
#### [Pinoox'ta HTTP testleri](./test/http-tests.md)
#### [Pinoox'ta konsol testleri](./test/console-tests.md)
#### [Pinoox'ta tarayıcı (HTML) testleri](./test/browser-tests.md)
#### [Pinoox'ta veritabanı testleri](./test/database.md)
#### [Pinoox'ta serileştirme testleri](./test/serialization.md)
#### [Pinoox'ta mocking](./test/mocking.md)

### SSS

#### [Sık karşılaşılan sorunlar](./faq/common-issues.md)
#### [Destek ile iletişim](./faq/contact-support.md)

---

### Kaynak kod
**Örnek kaynak kod:** [docs/source/](../source/) — her uygulamalı rehber için tam kod

Gerçek uygulamalar için adım adım rehberler — temelleri okuduktan sonra ve uygulamalı kod istediğinizde kullanın.

---

### Bu dokümantasyonu nasıl okumalı

1. Pinoox'a yeniyseniz **Giriş** ve **Başlarken** ile başlayın.
2. **Pratik uygulamalı rehberleri** takip edin — adım adım bir JSON API ve basit bir web sitesi oluşturun.
3. Route, Controller ve View oluştururken **Temel kavramları** okuyun.
4. Kalıcılık eklerken **Veritabanı** ve **Eloquent ORM** bölümlerini kullanın.
5. Kimlik doğrulama, dosyalar, Pinker ve paylaşılan servisler için **İleri düzey konulara** başvurun.
6. Özellikleri üretime almadan önce **Test** bölümünü kullanın.

Tüm uygulama kodu `apps/{package}/` altında yer alır. Framework çekirdeği `vendor/pinoox/pincore/` — uygulama iş mantığını oraya koymayın.

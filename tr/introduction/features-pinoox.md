# Pinoox özellikleri

[← Dizine dön](../README.md)

Pinoox 3.x, modüler bir PHP ekosistemi için tasarlanmıştır: tek paylaşılan çekirdek üzerinde birden fazla bağımsız uygulama, CLI iskelet oluşturma ve HTTP, veritabanı, tema ile kimlik doğrulama için yerleşik araçlar.

---

## HMVC mimarisi ve bağımsız uygulamalar

`apps/{package}/` altındaki her uygulama tam bir MVC yapısına sahiptir:

| Katman | Örnek yol |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

Bir uygulama eklemek veya devre dışı bırakmak diğerlerini etkilemez.

---

## CLI ve hızlı geliştirme

Proje kökünden:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI standart klasör düzenini, `app.php` dosyasını ve başlangıç route dosyalarını oluşturur.

---

## Routing ve Named Action'lar

URL yolları ile mantıksal handler'lar ayrı tutulur:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

Bu desen yeniden düzenlemeyi ve test etmeyi kolaylaştırır.

---

## Flow (middleware)

İstek Controller'a ulaşmadan önce Flow'lar çalışır — kimlik doğrulama, yetkilendirme, loglama ve daha fazlası için:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Flow takma adlarını `app.php` içinde kaydedin.

---

## View'lar ve temalar

- Twig şablonları `theme/{themeName}/` içinde
- **`View::render()`** ile render edin
- Temada Vite ile SPA desteği (Vue/React)

---

## Veritabanı ve Eloquent

- `DB` Portal üzerinden Query Builder ve Eloquent
- Her uygulamanın `database/migrations/` klasöründe migration'lar ve seeder'lar
- Paket adına dayalı tablo öneki (ör. `com_acme_blog_posts`)

---

## API ve JSON yanıtları

**`ApiController`**'ı genişletin ve standart zarfı kullanın:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Uluslararasılaştırma

`lang/{locale}/*.lang.php` içinde çeviri dosyaları — çok dilli uygulamalar için uygundur.

---

## İlgili dokümantasyon

- [Pinoox nedir?](./what-is-pinoox.md)
- [Pinoox kurulumu](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Dizine dön](../README.md)

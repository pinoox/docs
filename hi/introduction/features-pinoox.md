# Pinoox की विशेषताएँ

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x एक मॉड्यूलर PHP इकोसिस्टम के लिए डिज़ाइन किया गया है: एक साझा कोर पर कई स्वतंत्र ऐप्स, CLI स्कैफ़ोल्डिंग, और HTTP, डेटाबेस, थीम तथा प्रमाणीकरण (authentication) के लिए बिल्ट-इन टूल्स।

---

## HMVC आर्किटेक्चर और स्वतंत्र ऐप्स

`apps/{package}/` के अंतर्गत प्रत्येक ऐप की एक पूर्ण MVC संरचना होती है:

| परत (Layer) | उदाहरण पाथ |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

एक ऐप को जोड़ने या निष्क्रिय (disable) करने से दूसरों पर कोई असर नहीं पड़ता।

---

## CLI और तेज़ डेवलपमेंट

प्रोजेक्ट रूट से:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

CLI मानक फ़ोल्डर लेआउट, `app.php`, और प्रारंभिक route फ़ाइलें जनरेट करता है।

---

## Routing और Named Actions

URL पाथ और लॉजिकल हैंडलर अलग-अलग रखे जाते हैं:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

यह पैटर्न रीफ़ैक्टरिंग और टेस्टिंग को आसान बनाता है।

---

## Flow (middleware)

किसी request के controller तक पहुँचने से पहले Flows चलते हैं — प्रमाणीकरण (authentication), प्राधिकरण (authorization), लॉगिंग और बहुत कुछ के लिए:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Flow उपनाम (aliases) `app.php` में पंजीकृत करें।

---

## Views और थीम

- Twig टेम्पलेट `theme/{themeName}/` में
- **`View::render()`** के साथ रेंडर करें
- थीम में Vite के साथ SPA समर्थन (Vue/React)

---

## डेटाबेस और Eloquent

- `DB` Portal के माध्यम से Query Builder और Eloquent
- प्रत्येक ऐप की `database/migrations/` में Migrations और seeders
- पैकेज नाम पर आधारित टेबल प्रीफ़िक्स (जैसे `com_acme_blog_posts`)

---

## API और JSON responses

**`ApiController`** को extend करें और मानक envelope का उपयोग करें:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## अंतर्राष्ट्रीयकरण (Internationalization)

अनुवाद फ़ाइलें `lang/{locale}/*.lang.php` में — बहुभाषी ऐप्स के लिए उपयुक्त।

---

## संबंधित दस्तावेज़

- [Pinoox क्या है?](./what-is-pinoox.md)
- [Pinoox इंस्टॉल करना](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

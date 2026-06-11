# प्रोजेक्ट संरचना

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox HMVC आर्किटेक्चर का उपयोग करता है: `apps/{package}/` के अंतर्गत प्रत्येक ऐप एक पूर्ण, स्वतंत्र MVC मॉड्यूल है। फ्रेमवर्क कोर `vendor/pinoox/pincore/` में रहता है और इसे केवल तभी संपादित किया जाता है जब प्लेटफ़ॉर्म को ही बदलना हो।

---

## प्रोजेक्ट लेआउट

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← कोर (Composer पैकेज)
├── apps/                    ← सभी ऐप्स
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← अपलोड की गई फ़ाइलें और ऐप स्टोरेज
```

---

## ऐप लेआउट

```
apps/com_acme_shop/
├── app.php                  ← मैनिफ़ेस्ट (आवश्यक)
├── boot.php                 ← प्रोग्रामेटिक routes/events (वैकल्पिक)
├── schedule.php             ← cron कार्य (वैकल्पिक)
├── Controller/              ← HTTP हैंडलर
├── Model/                   ← Eloquent models
├── Flow/                    ← middleware
├── Component/               ← बिज़नेस लॉजिक
├── Portal/                  ← ऐप facades (वैकल्पिक)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← action नाम constants (वैकल्पिक)
├── theme/default/           ← Twig + assets
├── lang/en/                 ← अनुवाद
├── config/                  ← ऐप config
├── database/migrations/
└── pinker/                  ← बिल्ड मिरर
```

Views किसी अलग `View/` फ़ोल्डर में नहीं हैं — टेम्पलेट `theme/{themeName}/` में रहते हैं।

---

## app.php — मुख्य फ़ील्ड

```php
<?php

return [
    'package' => 'com_acme_shop',   // = फ़ोल्डर का नाम
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespaces

PSR-4: `App\` → `apps/`

| फ़ाइल | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## नामकरण नियम

- Package: `com_{vendor}_{name}` — जैसे `com_acme_shop`
- फ़ोल्डर का नाम = `app.php` में `package` = namespace खंड
- DB टेबल प्रीफ़िक्स: `{package}_` (जैसे `com_acme_shop_orders`)

---

## ऐप बनाम कोर की सीमा

| बदलाव | स्थान |
|--------|----------|
| नया endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| फ्रेमवर्क बग | `pinoox/pincore` (अपस्ट्रीम) |
| UI | `apps/{package}/theme/` |

ऐप्स को स्वतंत्र रखें — ऐप्स को आपस में जोड़ने (couple) के बजाय `Pinoox\Portal\*` facades का उपयोग करें।

---

## संबंधित दस्तावेज़

- [आपका पहला ऐप](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

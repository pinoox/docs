# Pinker और कैश

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

**Pinker** Pinoox 3.x में bake/runtime परत है: config और cache को source से PHP फ़ाइलों में compile किया जाता है जिन्हें तेज़ boot के लिए `include` किया जा सकता है। प्रत्येक ऐप के लिए मानक पथ: **`pinker/apps/{package}/`**।

---

## फ़ोल्डर संरचना

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← baked app.php
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← compiled templates
```

प्रोजेक्ट स्तर पर:

```
pinker/config/          ← baked config (non env-sensitive)
pinker/state/config/    ← post-install overrides (e.g. database)
```

---

## CLI कमांड्स

```bash
# एक ऐप के लिए Pinker को फिर से बनाएँ
php pinoox pinker:rebuild com_acme_shop

# छोटा alias
php pinoox bake com_acme_shop

# स्थिति: source और baked आउटपुट की तुलना
php pinoox pinker:status com_acme_shop

# कैश बनाएँ (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# केवल Twig
php pinoox cache:build com_acme_shop --only=twig

# केवल Pinker
php pinoox cache:build com_acme_shop --only=pinker

# कैश साफ़ करें
php pinoox cache:clear com_acme_shop
```

---

## कब rebuild करें

| घटना | कमांड |
|-------|---------|
| `app.php` या config बदलने पर | `pinker:rebuild` |
| Route / api बदलने पर | `cache:build` |
| Production में `.twig` बदलने पर | `cache:build --only=twig` |
| सर्वर पर इंस्टॉल के बाद | `cache:build` + `pinker:rebuild` |
| `.pinx` बनाने से पहले | `cache:build` (cache पैकेज के अंदर) |

---

## Runtime पर कैश सक्षम करना

`apps/{package}/app.php` में:

```php
'cache' => [
    'enabled' => false,   // डिफ़ॉल्ट — आवश्यकता होने पर production में true करें
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## ऐप mirror — `pinker/app.php`

प्रत्येक ऐप का एक baked mirror हो सकता है:

```
apps/com_acme_shop/pinker/app.php   ← source/reference in repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## `pinker()` helper

मैनुअल डेटा baking के लिए:

```php
pinker($data, ['lifetime' => 3600]);
```

आमतौर पर आप इसके बजाय CLI का उपयोग करते हैं; ऐप कोड में शायद ही कभी ज़रूरत पड़ती है।

---

## अनुशंसित deploy workflow

```bash
# 1. frontend बनाएँ
php pinoox theme:frontend build com_acme_shop

# 2. कैश
php pinoox cache:build com_acme_shop

# 3. pinker (env-specific)
php pinoox pinker:rebuild com_acme_shop
```

---

## सुझाव

- `pinker/state/` को मैन्युअल रूप से संपादित न करें — installer वहाँ लिखता है।
- Development में runtime cache आमतौर पर बंद रहता है; केवल बड़े बदलावों के बाद rebuild करें।
- `.pinx` पहले से बना हुआ cache साथ ले जा सकता है; लक्षित सर्वर पर एक बार `cache:build --only=pinker` चलाएँ।

---

## संबंधित दस्तावेज़

- [Config](../basic/config.md)
- [Twig Templates](../basic/templates.md)
- [CLI Reference](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

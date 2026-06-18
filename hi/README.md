# Pinoox दस्तावेज़

Pinoox प्लेटफ़ॉर्म (PHP 8.1+, HMVC आर्किटेक्चर) पर ऐप बनाने के लिए आधिकारिक डेवलपर दस्तावेज़।

प्रत्येक गाइड व्यावहारिक उदाहरणों के साथ **एक अनुशंसित तरीका** बताती है। नीचे से अनुभाग चुनें या विषय के अनुसार ब्राउज़ करें।

**भाषाएँ:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](./README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### परिचय

#### [Pinoox क्या है?](./introduction/what-is-pinoox.md)
#### [Pinoox की विशेषताएँ](./introduction/features-pinoox.md)
#### [Pinoox में योगदान देना](./introduction/contributions.md)

### शुरुआत

#### [Pinoox इंस्टॉल करना](./start/installing-pinoox.md)
#### [आपका पहला ऐप](./start/your-first-app.md)
#### [प्रोजेक्ट संरचना](./start/structure.md)
#### [Pinoox CLI संदर्भ](./start/cli-reference.md)
#### [Pinx CLI (सिंगल-ऐप प्रोजेक्ट्स)](./start/pinx-cli.md)
#### [app.php मैनिफ़ेस्ट संदर्भ](./start/app-manifest.md)

### व्यावहारिक वॉकथ्रू

#### [Walkthrough: Notes API app](./examples/simple-api-app.md)
#### [वॉकथ्रू: फ़ोनबुक वेब ऐप](./examples/phonebook-app.md)
#### [वॉकथ्रू: कॉन्टैक्ट फ़ॉर्म ऐप](./examples/contact-form-app.md)
#### [वॉकथ्रू: सरल ब्लॉग ऐप](./examples/blog-app.md)
#### [Walkthrough: Task board (Todo)](./examples/task-board-app.md)
#### [वॉकथ्रू: इमेज गैलरी ऐप](./examples/gallery-app.md)
#### [Walkthrough: Vue SPA panel](./examples/vue-spa-app.md)
#### [वॉकथ्रू: React SPA पैनल](./examples/react-spa-app.md)
#### [Walkthrough: Vite hybrid (Twig + JS widget)](./examples/vite-hybrid-app.md)

### मूल अवधारणाएँ

#### [Router](./basic/routers.md)
#### [Controllers](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [HTTP Request](./basic/requests.md)
#### [HTTP Response](./basic/responses.md)
#### [URL and Link Building](./basic/url.md)
#### [File Path](./basic/path.md)
#### [Validation](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Twig Templates](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [भाषा और अनुवाद](./basic/language.md)

### उन्नत विषय

#### [Pinker और कैश](./advanced/pinker.md)
#### [ऐप Services (Component + Portal)](./advanced/services.md)
#### [ग्लोबल Helpers](./advanced/helpers.md)
#### [ईमेल भेजना (Sending Email)](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [User Management](./advanced/user-management.md)
#### [फ़ाइल प्रबंधन (File Management)](./advanced/file-management.md)
#### [Pinion प्रोटोकॉल](./advanced/pinion.md)
#### [टोकन प्रबंधन (Token Management)](./advanced/token-management.md)
#### [एक्सेस और अनुमतियाँ (Access & permissions)](./advanced/access-permissions.md)
#### [Transport (साझा संसाधन)](./advanced/transport.md)
#### [boot.php और Events](./advanced/boot-and-events.md)
#### [शेड्यूलिंग (cron)](./advanced/schedule.md)

### डेटाबेस

#### [Database Getting Started](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Pagination](./database/pagination.md)
#### [Migrations](./database/migrations.md)
#### [Patches (data updates)](./database/patches.md)

### Eloquent ORM

#### [Eloquent ORM Getting Started](./eloquent-orm/getting-started.md)
#### [Eloquent Relationships](./eloquent-orm/relationships.md)
#### [Eloquent Collections](./eloquent-orm/collections.md)
#### [Mutators and Casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Model Serialization](./eloquent-orm/serialization.md)
#### [Test Data — Seeders](./eloquent-orm/factories.md)

### परीक्षण

#### [Getting Started with Testing in Pinoox](./test/getting-started.md)
#### [HTTP Testing in Pinoox](./test/http-tests.md)
#### [Console Testing in Pinoox](./test/console-tests.md)
#### [Browser (HTML) Testing in Pinoox](./test/browser-tests.md)
#### [Database Testing in Pinoox](./test/database.md)
#### [Serialization Testing in Pinoox](./test/serialization.md)
#### [Mocking in Pinoox](./test/mocking.md)

### अक्सर पूछे जाने वाले प्रश्न

#### [Common Issues](./faq/common-issues.md)
#### [Contact Support](./faq/contact-support.md)

---

### सोर्स कोड
**उदाहरण सोर्स:** [docs/source/](../source/) — हर वॉकथ्रू के लिए पूरा कोड

वास्तविक ऐप के लिए चरण-दर-चरण गाइड — बेसिक पढ़ने के बाद, जब हाथों-हाथ कोड चाहिए।

---

### इन दस्तावेज़ों को कैसे पढ़ें

1. यदि Pinoox में नए हैं तो **परिचय** और **शुरुआत** से शुरू करें।
2. **व्यावहारिक वॉकथ्रू** का पालन करें — JSON API और सरल वेबसाइट बनाएँ।
3. रूट, कंट्रोलर और व्यू बनाते समय **मूल अवधारणाएँ** पढ़ें।
4. पर्सिस्टेंस जोड़ते समय **डेटाबेस** और **Eloquent ORM** उपयोग करें।
5. ऑथ, फ़ाइल, Pinker और साझा सेवाओं के लिए **उन्नत विषय** देखें।
6. प्रोडक्शन से पहले **परीक्षण** उपयोग करें।

सारा ऐप कोड `apps/{package}/` के अंतर्गत है। फ्रेमवर्क कोर `vendor/pinoox/pincore/` है — वहाँ ऐप बिज़नेस लॉजिक न लिखें।

# التواصل مع الدعم

[← العودة إلى الفهرس](../README.md)

إذا بقي لديك عائق بعد مراجعة [المشكلات الشائعة](./common-issues.md)، استخدم القنوات الرسمية أدناه. قبل التواصل مع الدعم، جهّز إصدار Pinoox وإصدار PHP ورسالة الخطأ وخطوات إعادة الإنتاج.

---

## الدعم العام

**البريد:** [support@pinoox.com](mailto:support@pinoox.com)

مناسب لـ:

- أسئلة التثبيت والنشر
- سلوك الإطار غير المتوقع
- إرشاد HMVC وبنية التطبيق

ضمّن في بريدك:

1. إصدار Pinoox (`composer.json` → `version` أو git tag)
2. إصدار PHP (`php -v`)
3. نظام التشغيل وخادم الويب (Apache/nginx، MAMP، cPanel، …)
4. نص الخطأ الكامل أو لقطة شاشة
5. خطوات إعادة إنتاج minimal

---

## GitHub Issues

للأخطاء المؤكدة وطلبات الميزات والنقاش التقني العام:

**المستودع:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

قبل فتح issue جديد:

- ابحث عن issues مكررة
- اختبر على أحدث إصدار stable/beta
- إذا كان متعلقًا بـ `pincore`، تحقق أيضًا من حزمة `pinoox/pincore`

قالب issue مقترح:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.1.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## تقارير الأمان

**البريد:** [security@pinoox.com](mailto:security@pinoox.com)

**فقط** لثغرات الأمان — SQL injection، تجاوز المصادقة، RCE، تسريب أسرار.

- لا تنشر التفاصيل علنًا (GitHub issue) حتى يكون patch جاهزًا
- عند الإمكان، ضمّن PoC minimal ووصف التأثير

---

## المساهمة بالكود

لـ PRs وتطوير الإطار:

- [المساهمة](../introduction/contributions.md)
- Fork → branch → test (`php pinoox test`) → Pull Request

---

## موارد المساعدة الذاتية

| الموضوع | الوثيقة |
|-------|-----|
| التثبيت | [installing-pinoox.md](../start/installing-pinoox.md) |
| التطبيق الأول | [your-first-app.md](../start/your-first-app.md) |
| المشكلات الشائعة | [common-issues.md](./common-issues.md) |
| الاختبار | [getting-started.md](../test/getting-started.md) |

**الموقع:** [pinoox.com](https://www.pinoox.com/)

---

## وثائق ذات صلة

- [المشكلات الشائعة](./common-issues.md)
- [ما هو Pinoox؟](../introduction/what-is-pinoox.md)
- [المساهمة](../introduction/contributions.md)
- [تثبيت Pinoox](../start/installing-pinoox.md)

---

[← العودة إلى الفهرس](../README.md)

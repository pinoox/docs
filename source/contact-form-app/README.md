# Contact form app / فرم تماس

Source code for the Pinoox walkthrough: [Contact form app](../../en/examples/contact-form-app.md) · [فرم تماس](../../fa/examples/contact-form-app.md)

**Package:** `com_acme_contact`
**URL path:** `/contact`

---

## English — Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/contact-form-app/com_acme_contact apps/com_acme_contact
   ```
   On Windows, copy `docs/source/contact-form-app/com_acme_contact` to `apps/com_acme_contact`.

2. Register the app route:
   ```bash
   php pinoox app:router set /contact com_acme_contact
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_contact
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/contact
   ```



---

## فارسی — نصب

۱. پوشه اپ را در پروژه کپی کنید:
   ```bash
   cp -r docs/source/contact-form-app/com_acme_contact apps/com_acme_contact
   ```
   در ویندوز: `docs/source/contact-form-app/com_acme_contact` را به `apps/com_acme_contact` کپی کنید.

۲. ثبت مسیر اپ:
   ```bash
   php pinoox app:router set /contact com_acme_contact
   ```
۳. اجرای migration:
   ```bash
   php pinoox migrate com_acme_contact
   ```
۴. در مرورگر باز کنید:
   ```
   http://localhost/pinoox/contact
   ```

# Notes API app / اپ API یادداشت

Source code for the Pinoox walkthrough: [Notes API app](../../en/examples/simple-api-app.md) · [اپ API یادداشت](../../fa/examples/simple-api-app.md)

**Package:** `com_acme_notes`
**URL path:** `/notes`

---

## English — Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/simple-api-app/com_acme_notes apps/com_acme_notes
   ```
   On Windows, copy `docs/source/simple-api-app/com_acme_notes` to `apps/com_acme_notes`.

2. Register the app route:
   ```bash
   php pinoox app:router set /notes com_acme_notes
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_notes
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/notes
   ```



---

## فارسی — نصب

۱. پوشه اپ را در پروژه کپی کنید:
   ```bash
   cp -r docs/source/simple-api-app/com_acme_notes apps/com_acme_notes
   ```
   در ویندوز: `docs/source/simple-api-app/com_acme_notes` را به `apps/com_acme_notes` کپی کنید.

۲. ثبت مسیر اپ:
   ```bash
   php pinoox app:router set /notes com_acme_notes
   ```
۳. اجرای migration:
   ```bash
   php pinoox migrate com_acme_notes
   ```
۴. در مرورگر باز کنید:
   ```
   http://localhost/pinoox/notes
   ```

# Phonebook app / اپ دفترچه تلفن

Source code for the Pinoox walkthrough: [Phonebook app](../../en/examples/phonebook-app.md) · [اپ دفترچه تلفن](../../fa/examples/phonebook-app.md)

**Package:** `com_acme_phonebook`
**URL path:** `/phonebook`

---

## English — Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/phonebook-app/com_acme_phonebook apps/com_acme_phonebook
   ```
   On Windows, copy `docs/source/phonebook-app/com_acme_phonebook` to `apps/com_acme_phonebook`.

2. Register the app route:
   ```bash
   php pinoox app:router set /phonebook com_acme_phonebook
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_phonebook
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/phonebook
   ```



---

## فارسی — نصب

۱. پوشه اپ را در پروژه کپی کنید:
   ```bash
   cp -r docs/source/phonebook-app/com_acme_phonebook apps/com_acme_phonebook
   ```
   در ویندوز: `docs/source/phonebook-app/com_acme_phonebook` را به `apps/com_acme_phonebook` کپی کنید.

۲. ثبت مسیر اپ:
   ```bash
   php pinoox app:router set /phonebook com_acme_phonebook
   ```
۳. اجرای migration:
   ```bash
   php pinoox migrate com_acme_phonebook
   ```
۴. در مرورگر باز کنید:
   ```
   http://localhost/pinoox/phonebook
   ```

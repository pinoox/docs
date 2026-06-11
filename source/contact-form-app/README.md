# Contact form app

Source code for the Pinoox walkthrough: [Contact form app](../../en/examples/contact-form-app.md)

**Package:** `com_acme_contact`
**URL path:** `/contact`

---

## Install

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

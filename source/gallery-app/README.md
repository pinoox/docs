# Gallery app

Source code for the Pinoox walkthrough: [Gallery app](../../en/examples/gallery-app.md)

**Package:** `com_acme_gallery`
**URL path:** `/gallery`

---

## Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/gallery-app/com_acme_gallery apps/com_acme_gallery
   ```
   On Windows, copy `docs/source/gallery-app/com_acme_gallery` to `apps/com_acme_gallery`.

2. Register the app route:
   ```bash
   php pinoox app:router set /gallery com_acme_gallery
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_gallery
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/gallery
   ```

# Notes API app

Source code for the Pinoox walkthrough: [Notes API app](../../en/examples/simple-api-app.md)

**Package:** `com_acme_notes`
**URL path:** `/notes`

---

## Install

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

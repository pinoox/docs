# Vue SPA app

Source code for the Pinoox walkthrough: [Vue SPA app](../../en/examples/vue-spa-app.md)

**Package:** `com_acme_vue_notes`
**URL path:** `/vue-notes`

---

## Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/vue-spa-app/com_acme_vue_notes apps/com_acme_vue_notes
   ```
   On Windows, copy `docs/source/vue-spa-app/com_acme_vue_notes` to `apps/com_acme_vue_notes`.

2. Register the app route:
   ```bash
   php pinoox app:router set /vue-notes com_acme_vue_notes
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_vue_notes
   ```
4. (Optional) Install and build frontend:
   ```bash
   cd apps/com_acme_vue_notes/theme/default
   npm install
   php pinoox fe com_acme_vue_notes build
   ```
5. Open in browser:
   ```
   http://localhost/pinoox/vue-notes
   ```

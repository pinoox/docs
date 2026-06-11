# React SPA app

Source code for the Pinoox walkthrough: [React SPA app](../../en/examples/react-spa-app.md)

**Package:** `com_acme_react_tasks`
**URL path:** `/react-tasks`

---

## Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/react-spa-app/com_acme_react_tasks apps/com_acme_react_tasks
   ```
   On Windows, copy `docs/source/react-spa-app/com_acme_react_tasks` to `apps/com_acme_react_tasks`.

2. Register the app route:
   ```bash
   php pinoox app:router set /react-tasks com_acme_react_tasks
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_react_tasks
   ```
4. (Optional) Install and build frontend:
   ```bash
   cd apps/com_acme_react_tasks/theme/default
   npm install
   php pinoox fe com_acme_react_tasks build
   ```
5. Open in browser:
   ```
   http://localhost/pinoox/react-tasks
   ```

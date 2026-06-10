# Task board app / تابلوی کار

Source code for the Pinoox walkthrough: [Task board app](../../en/examples/task-board-app.md) · [تابلوی کار](../../fa/examples/task-board-app.md)

**Package:** `com_acme_tasks`
**URL path:** `/tasks`

---

## English — Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/task-board-app/com_acme_tasks apps/com_acme_tasks
   ```
   On Windows, copy `docs/source/task-board-app/com_acme_tasks` to `apps/com_acme_tasks`.

2. Register the app route:
   ```bash
   php pinoox app:router set /tasks com_acme_tasks
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_tasks
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/tasks
   ```



---

## فارسی — نصب

۱. پوشه اپ را در پروژه کپی کنید:
   ```bash
   cp -r docs/source/task-board-app/com_acme_tasks apps/com_acme_tasks
   ```
   در ویندوز: `docs/source/task-board-app/com_acme_tasks` را به `apps/com_acme_tasks` کپی کنید.

۲. ثبت مسیر اپ:
   ```bash
   php pinoox app:router set /tasks com_acme_tasks
   ```
۳. اجرای migration:
   ```bash
   php pinoox migrate com_acme_tasks
   ```
۴. در مرورگر باز کنید:
   ```
   http://localhost/pinoox/tasks
   ```

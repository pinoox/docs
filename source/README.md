# Pinoox example source code

Ready-to-copy HMVC apps for all nine practical walkthroughs in the docs.

| # | Folder | Package | URL | Docs |
|---|--------|---------|-----|------|
| 1 | [simple-api-app](./simple-api-app/) | `com_acme_notes` | `/notes` | [EN](../en/examples/simple-api-app.md) · [FA](../fa/examples/simple-api-app.md) |
| 2 | [phonebook-app](./phonebook-app/) | `com_acme_phonebook` | `/phonebook` | [EN](../en/examples/phonebook-app.md) · [FA](../fa/examples/phonebook-app.md) |
| 3 | [contact-form-app](./contact-form-app/) | `com_acme_contact` | `/contact` | [EN](../en/examples/contact-form-app.md) · [FA](../fa/examples/contact-form-app.md) |
| 4 | [blog-app](./blog-app/) | `com_acme_blog` | `/blog` | [EN](../en/examples/blog-app.md) · [FA](../fa/examples/blog-app.md) |
| 5 | [task-board-app](./task-board-app/) | `com_acme_tasks` | `/tasks` | [EN](../en/examples/task-board-app.md) · [FA](../fa/examples/task-board-app.md) |
| 6 | [gallery-app](./gallery-app/) | `com_acme_gallery` | `/gallery` | [EN](../en/examples/gallery-app.md) · [FA](../fa/examples/gallery-app.md) |
| 7 | [vue-spa-app](./vue-spa-app/) | `com_acme_vue_notes` | `/vue-notes` | [EN](../en/examples/vue-spa-app.md) · [FA](../fa/examples/vue-spa-app.md) |
| 8 | [react-spa-app](./react-spa-app/) | `com_acme_react_tasks` | `/react-tasks` | [EN](../en/examples/react-spa-app.md) · [FA](../fa/examples/react-spa-app.md) |
| 9 | [vite-hybrid-app](./vite-hybrid-app/) | `com_acme_vite_shop` | `/shop` | [EN](../en/examples/vite-hybrid-app.md) · [FA](../fa/examples/vite-hybrid-app.md) |

## Quick install (any example)

1. Copy `docs/source/<folder>/<package>/` to `apps/<package>/`
2. `php pinoox app:router set <url> <package>`
3. `php pinoox migrate <package>` (except vite-hybrid-app)
4. For SPA/hybrid examples: `npm install` in `theme/default/`, then `php pinoox fe <package> build`

See each folder's `README.md` for FA + EN instructions.

---

## فارسی

کد منبع نه نمونه عملی پینوکس — هر پوشه شامل اپ کامل HMVC و راهنمای نصب فارسی/انگلیسی است.

همان مراحل بالا: کپی به `apps/`، ثبت router، migrate، و در صورت نیاز build فرانت.

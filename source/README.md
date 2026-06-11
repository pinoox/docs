# Pinoox example source code

Ready-to-copy HMVC apps for all nine practical walkthroughs in the docs.

| # | Folder | Package | URL | Docs |
|---|--------|---------|-----|------|
| 1 | [simple-api-app](./simple-api-app/) | `com_acme_notes` | `/notes` | [Notes API app](../en/examples/simple-api-app.md) |
| 2 | [phonebook-app](./phonebook-app/) | `com_acme_phonebook` | `/phonebook` | [Phonebook app](../en/examples/phonebook-app.md) |
| 3 | [contact-form-app](./contact-form-app/) | `com_acme_contact` | `/contact` | [Contact form app](../en/examples/contact-form-app.md) |
| 4 | [blog-app](./blog-app/) | `com_acme_blog` | `/blog` | [Blog app](../en/examples/blog-app.md) |
| 5 | [task-board-app](./task-board-app/) | `com_acme_tasks` | `/tasks` | [Task board app](../en/examples/task-board-app.md) |
| 6 | [gallery-app](./gallery-app/) | `com_acme_gallery` | `/gallery` | [Gallery app](../en/examples/gallery-app.md) |
| 7 | [vue-spa-app](./vue-spa-app/) | `com_acme_vue_notes` | `/vue-notes` | [Vue SPA app](../en/examples/vue-spa-app.md) |
| 8 | [react-spa-app](./react-spa-app/) | `com_acme_react_tasks` | `/react-tasks` | [React SPA app](../en/examples/react-spa-app.md) |
| 9 | [vite-hybrid-app](./vite-hybrid-app/) | `com_acme_vite_shop` | `/shop` | [Vite hybrid app](../en/examples/vite-hybrid-app.md) |

## Quick install (any example)

1. Copy `docs/source/<folder>/<package>/` to `apps/<package>/`
2. `php pinoox app:router set <url> <package>`
3. `php pinoox migrate <package>` (except vite-hybrid-app)
4. For SPA/hybrid examples: `npm install` in `theme/default/`, then `php pinoox fe <package> build`

See each folder's `README.md` for step-by-step instructions.

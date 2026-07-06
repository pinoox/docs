# Walkthrough: Vue SPA panel

[← Back to index](../README.md)

Build a **Vue 3 single-page panel** that reads notes from a Pinoox API and performs simple CRUD. The pattern matches `com_pinoox_manager` / `spark`: Twig is only the **shell**; UI lives in Vue components.

**Package:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Profile:** `spa` · **stack:** `vue`  
**Full source:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — copy to `apps/`
---

## Prerequisites

- Pinoox 3.x with PHP 8.2+
- Node.js 18+ and npm
- Familiarity with the [Notes API walkthrough](./simple-api-app.md) and [Twig templates](../basic/templates.md)

---

## Step 1 — Create the app and route

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Step 2 — Notes API (backend)

Create the table, Model, `NoteApiController`, and `routes/api.php` as in [simple-api-app](./simple-api-app.md) (same `notes` migration with `title` and `body`).

Register the API route file in `app.php`:

```php
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## Step 3 — Scaffold Vue frontend

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Theme files land in `theme/default/`: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js`, etc.

---

## Step 4 — `frontend.config.php`

```php
<?php

return [
    'profile' => 'spa',
    'stack' => 'vue',
    'entry' => 'src/main.js',
    'manifest' => 'dist/.vite/manifest.json',
    'mount' => '#app',
    'dev' => [
        'enabled' => (bool) _env('VITE_DEV', false),
        'url' => rtrim((string) _env('VITE_DEV_SERVER', 'http://127.0.0.1:5173'), '/'),
    ],
];
```

You can mirror these values in `app.php`:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## Step 5 — SPA route (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

Every app URL (except `/api/v1/…`) serves `main.twig`; Vue Router handles client-side paths.

---

## Step 6 — Twig shell

`theme/default/main.twig` (summary):

```twig
<!doctype html>
<html lang="en" dir="ltr">
<head>
    {% include 'partials/head.twig' %}
    {% include 'partials/scripts.twig' %}
</head>
<body>
    <div id="app"></div>
</body>
</html>
```

`theme/default/partials/scripts.twig`:

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite('src/main.js') }}
```

`pinoox_bootstrap()` injects `window.__PINOOX__`, including `url.API` for axios/fetch calls.

---

## Step 7 — Theme `.env` (development)

`theme/default/.env` (copy from `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

For MAMP subdirectory installs, `VITE_SERVER_URL` must be the **full PHP origin** (including the path prefix).

---

## Step 8 — Vue client (minimal)

`src/api/notes.js`:

```js
import axios from 'axios';
import { getUrl } from '../boot.js';

const api = axios.create({
    baseURL: getUrl().API,
    headers: { Accept: 'application/json' },
});

export async function fetchNotes() {
    const { data } = await api.get('/notes');
    return data.data ?? [];
}

export async function createNote(payload) {
    const { data } = await api.post('/notes', payload);
    return data.data;
}

export async function deleteNote(id) {
    await api.delete(`/notes/${id}`);
}
```

`src/App.vue` (CSS in the same file):

```vue
<script setup>
import { onMounted, ref } from 'vue';
import { createNote, deleteNote, fetchNotes } from './api/notes.js';

const notes = ref([]);
const title = ref('');
const body = ref('');
const loading = ref(true);

async function load() {
    loading.value = true;
    notes.value = await fetchNotes();
    loading.value = false;
}

async function add() {
    if (!title.value.trim()) return;
    await createNote({ title: title.value, body: body.value });
    title.value = '';
    body.value = '';
    await load();
}

async function remove(id) {
    await deleteNote(id);
    await load();
}

onMounted(load);
</script>

<template>
    <main class="page">
        <h1 class="page-title">Notes (Vue SPA)</h1>
        <div class="panel">
            <form class="form" @submit.prevent="add">
                <div class="field">
                    <label>Title</label>
                    <input v-model="title" required />
                </div>
                <div class="field">
                    <label>Body</label>
                    <textarea v-model="body" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
        <div class="panel">
            <p v-if="loading" class="empty">Loading…</p>
            <ul v-else class="note-list">
                <li v-for="n in notes" :key="n.id">
                    <div>
                        <strong>{{ n.title }}</strong>
                        <p v-if="n.body" class="note-body">{{ n.body }}</p>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" @click="remove(n.id)">Delete</button>
                </li>
            </ul>
            <p v-if="!loading && !notes.length" class="empty">No notes yet.</p>
        </div>
    </main>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }
.page { max-width: 560px; margin: 0 auto; padding: 2rem 1rem; font-family: system-ui, sans-serif; line-height: 1.5; color: #0f172a; }
.page-title { margin: 0 0 1.25rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; font-size: 1.5rem; }
.panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; }
.field { margin-bottom: 1rem; }
.field label { display: block; font-weight: 600; margin-bottom: .35rem; font-size: .9rem; }
.field input, .field textarea { width: 100%; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
.btn { padding: .45rem 1rem; font: inherit; font-weight: 600; border-radius: 6px; cursor: pointer; background: transparent; }
.btn-primary { border: 2px solid #2563eb; color: #2563eb; }
.btn-primary:hover { background: #2563eb; color: #fff; }
.btn-danger { border: 2px solid #dc2626; color: #dc2626; }
.btn-danger:hover { background: #dc2626; color: #fff; }
.btn-sm { padding: .25rem .6rem; font-size: .85rem; }
.note-list { list-style: none; padding: 0; margin: 0; }
.note-list li { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; padding: .75rem 0; border-bottom: 1px solid #e2e8f0; }
.note-body { margin: .25rem 0 0; color: #475569; font-size: .95rem; }
.empty { color: #64748b; font-style: italic; margin: 0; }
</style>
```

The scaffold usually ships Pinia and Vue Router; for this walkthrough you can drop Router and mount `App.vue` directly from `src/main.js`.

---

## Step 9 — Development (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Use `--no-serve` when Apache/MAMP already serves PHP. To run PHP and Vite together:

```bash
php pinoox fe com_acme_vue_notes dev
```

Open `http://localhost/pinoox/vue-notes` — Vue hot-reloads; the Vite dev server proxies `/api` to PHP.

---

## Step 10 — Production build

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

Output goes to `theme/default/dist/` — Twig reads the manifest for production `<script>` / `<link>` tags.

---

## Test

1. Run migrations and verify the API with curl (see [simple-api-app](./simple-api-app.md)).
2. Open the SPA — empty or populated list should render.
3. Add and delete notes without a full page reload.

---

## Next steps

| Upgrade | Doc |
|---------|-----|
| Vue Router + multiple pages | Default scaffold `src/router/` |
| Auth and Flow middleware | [Flows](../basic/flows.md) |
| Hybrid profile (SEO + Vue island) | [Vite hybrid walkthrough](./vite-hybrid-app.md) |

---

## Related docs

- [Templates & `vite_tags()`](../basic/templates.md)
- [Frontend & Vite](../basic/frontend-vite.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend` key](../start/app-manifest.md)

---

[← Back to index](../README.md)

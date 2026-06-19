# Walkthrough: Vue SPA panel

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox API से notes read करने और simple CRUD करने वाला **Vue 3 single-page panel** बनाएँ। Pattern `com_pinoox_manager` / `spark` जैसा: Twig केवल **shell**; UI Vue components में।

**Package:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Profile:** `spa` · **stack:** `vue`  
**Full source:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — copy to `apps/`
---

## Prerequisites

- PHP 8.2+ के साथ Pinoox 3.x
- Node.js 18+ and npm
- [Notes API walkthrough](./simple-api-app.md) और [Twig templates](../basic/templates.md) से familiarity

---

## Step 1 — App और route बनाएँ

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Step 2 — Notes API (backend)

[simple-api-app](./simple-api-app.md) जैसा table, Model, `NoteApiController`, और `routes/api.php` बनाएँ (same `notes` migration with `title` and `body`)।

`app.php` में API route file register करें:

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

## Step 3 — Vue frontend scaffold

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Theme files `theme/default/` में: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js`, etc.

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

Values `app.php` में mirror कर सकते हैं:

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

हर app URL (`/api/v1/…` को छोड़कर) `main.twig` serve करता है; Vue Router client-side paths handle करता है।

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

`pinoox_bootstrap()` `window.__PINOOX__` inject करता है, including `url.API` for axios/fetch calls.

---

## Step 7 — Theme `.env` (development)

`theme/default/.env` (`.env.example` से copy):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

MAMP subdirectory installs के लिए `VITE_SERVER_URL` **full PHP origin** होना चाहिए (path prefix सहित)।

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

Scaffold आमतौर पर Pinia और Vue Router ship करता है; इस walkthrough के लिए Router drop करके `src/main.js` से directly `App.vue` mount कर सकते हैं।

---

## Step 9 — Development (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

PHP पहले से Apache/MAMP serve करे तो `--no-serve` उपयोग करें। PHP और Vite साथ चलाने के लिए:

```bash
php pinoox fe com_acme_vue_notes dev
```

`http://localhost/pinoox/vue-notes` खोलें — Vue hot-reloads; Vite dev server `/api` को PHP पर proxy करता है।

---

## Step 10 — Production build

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

Output `theme/default/dist/` में — production `<script>` / `<link>` tags के लिए Twig manifest read करता है।

---

## Test

1. Migrations चलाएँ और curl से API verify करें ([simple-api-app](./simple-api-app.md) देखें)।
2. SPA खोलें — empty या populated list render होनी चाहिए।
3. Full page reload के बिना notes add और delete करें।

---

## Next steps

| Upgrade | Doc |
|---------|-----|
| Vue Router + multiple pages | Default scaffold `src/router/` |
| Auth and Flow middleware | [Flows](../basic/flows.md) |
| Hybrid profile (SEO + Vue island) | [Vite hybrid walkthrough](./vite-hybrid-app.md) |

---

## संबंधित docs

- [Templates & `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend` key](../start/app-manifest.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)

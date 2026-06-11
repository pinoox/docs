# Walkthrough: Vue-SPA-Panel

[← Zurück zum Index](../README.md)

Ein **Vue-3-Single-Page-Panel** bauen, das Notizen aus einer Pinoox-API liest und einfaches CRUD ausführt. Das Muster entspricht `com_pinoox_manager` / `spark`: Twig ist nur die **Shell**; die UI lebt in Vue-Komponenten.

**Package:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Profile:** `spa` · **stack:** `vue`  
**Vollständiger Quellcode:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — nach `apps/` kopieren
---

## Voraussetzungen

- Pinoox 3.x mit PHP 8.1+
- Node.js 18+ und npm
- Vertrautheit mit dem [Notes-API-Walkthrough](./simple-api-app.md) und [Twig-Templates](../basic/templates.md)

---

## Schritt 1 — App und Route erstellen

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Schritt 2 — Notes-API (Backend)

Tabelle, Model, `NoteApiController` und `routes/api.php` wie in [simple-api-app](./simple-api-app.md) erstellen (gleiche `notes`-Migration mit `title` und `body`).

API-Routendatei in `app.php` registrieren:

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

## Schritt 3 — Vue-Frontend scaffolden

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Theme-Dateien landen in `theme/default/`: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js`, usw.

---

## Schritt 4 — `frontend.config.php`

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

Diese Werte können in `app.php` gespiegelt werden:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## Schritt 5 — SPA-Route (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

Jede App-URL (außer `/api/v1/…`) liefert `main.twig`; Vue Router verwaltet clientseitige Pfade.

---

## Schritt 6 — Twig-Shell

`theme/default/main.twig` (Zusammenfassung):

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

`pinoox_bootstrap()` injiziert `window.__PINOOX__`, einschließlich `url.API` für axios/fetch-Aufrufe.

---

## Schritt 7 — Theme-`.env` (Entwicklung)

`theme/default/.env` (aus `.env.example` kopieren):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

Bei MAMP-Unterverzeichnis-Installationen muss `VITE_SERVER_URL` der **vollständige PHP-Origin** sein (einschließlich Pfadpräfix).

---

## Schritt 8 — Vue-Client (minimal)

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

Das Scaffold liefert üblicherweise Pinia und Vue Router; für diesen Walkthrough können Sie Router weglassen und `App.vue` direkt aus `src/main.js` mounten.

---

## Schritt 9 — Entwicklung (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

`--no-serve` verwenden, wenn Apache/MAMP PHP bereits ausliefert. PHP und Vite zusammen starten:

```bash
php pinoox fe com_acme_vue_notes dev
```

`http://localhost/pinoox/vue-notes` öffnen — Vue hot-reloaded; der Vite-Dev-Server proxied `/api` zu PHP.

---

## Schritt 10 — Produktions-Build

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

Ausgabe nach `theme/default/dist/` — Twig liest das Manifest für Produktions-`<script>` / `<link>`-Tags.

---

## Testen

1. Migrationen ausführen und API mit curl prüfen (siehe [simple-api-app](./simple-api-app.md)).
2. SPA öffnen — leere oder gefüllte Liste sollte rendern.
3. Notizen hinzufügen und löschen ohne vollständigen Seitenreload.

---

## Nächste Schritte

| Upgrade | Dokumentation |
|---------|-----|
| Vue Router + mehrere Seiten | Standard-Scaffold `src/router/` |
| Auth und Flow-Middleware | [Flows](../basic/flows.md) |
| Hybrid-Profil (SEO + Vue-Insel) | [Vite-Hybrid-Walkthrough](./vite-hybrid-app.md) |

---

## Verwandte Dokumentation

- [Templates & `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend`-Schlüssel](../start/app-manifest.md)

---

[← Zurück zum Index](../README.md)

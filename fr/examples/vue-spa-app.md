# Guide pas à pas : panneau SPA Vue

[← Retour à l'index](../README.md)

Construisez un **panneau single-page Vue 3** qui lit les notes depuis une API Pinoox et effectue un CRUD simple. Le modèle correspond à `com_pinoox_manager` / `spark` : Twig n'est que le **shell** ; l'UI vit dans les composants Vue.

**Paquet :** `com_acme_vue_notes`  
**URL :** `http://localhost/pinoox/vue-notes`  
**Profil :** `spa` · **stack :** `vue`  
**Source complète :** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — copier vers `apps/`

---

## Prérequis

- Pinoox 3.x avec PHP 8.2+
- Node.js 18+ et npm
- Familiarité avec le [guide API Notes](./simple-api-app.md) et les [modèles Twig](../basic/templates.md)

---

## Étape 1 — Créer l'app et la route

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Étape 2 — API Notes (backend)

Créez la table, le Model, `NoteApiController` et `routes/api.php` comme dans [simple-api-app](./simple-api-app.md) (même migration `notes` avec `title` et `body`).

Enregistrez le fichier de routes API dans `app.php` :

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

## Étape 3 — Scaffolder le frontend Vue

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Les fichiers du thème arrivent dans `theme/default/` : `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js`, etc.

---

## Étape 4 — `frontend.config.php`

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

Vous pouvez refléter ces valeurs dans `app.php` :

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## Étape 5 — Route SPA (catch-all)

`routes/web.php` :

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

Chaque URL de l'app (sauf `/api/v1/…`) sert `main.twig` ; Vue Router gère les chemins côté client.

---

## Étape 6 — Shell Twig

`theme/default/main.twig` (résumé) :

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

`theme/default/partials/scripts.twig` :

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite('src/main.js') }}
```

`pinoox_bootstrap()` injecte `window.__PINOOX__`, y compris `url.API` pour les appels axios/fetch.

---

## Étape 7 — `.env` du thème (développement)

`theme/default/.env` (copier depuis `.env.example`) :

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

Pour les installations MAMP en sous-dossier, `VITE_SERVER_URL` doit être **l'origine PHP complète** (préfixe de chemin inclus).

---

## Étape 8 — Client Vue (minimal)

`src/api/notes.js` :

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

`src/App.vue` (CSS dans le même fichier) :

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

Le scaffold inclut généralement Pinia et Vue Router ; pour ce guide, vous pouvez retirer Router et monter `App.vue` directement depuis `src/main.js`.

---

## Étape 9 — Développement (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Utilisez `--no-serve` lorsqu'Apache/MAMP sert déjà PHP. Pour lancer PHP et Vite ensemble :

```bash
php pinoox fe com_acme_vue_notes dev
```

Ouvrez `http://localhost/pinoox/vue-notes` — Vue se recharge à chaud ; le serveur Vite de dev proxy `/api` vers PHP.

---

## Étape 10 — Build de production

```bash
php pinoox fe com_acme_vue_notes build
# ou : php pinoox theme:frontend build com_acme_vue_notes
```

La sortie va dans `theme/default/dist/` — Twig lit le manifeste pour les balises `<script>` / `<link>` en production.

---

## Test

1. Exécutez les migrations et vérifiez l'API avec curl (voir [simple-api-app](./simple-api-app.md)).
2. Ouvrez la SPA — liste vide ou peuplée doit s'afficher.
3. Ajoutez et supprimez des notes sans rechargement complet de page.

---

## Prochaines étapes

| Amélioration | Doc |
|---------|-----|
| Vue Router + pages multiples | Scaffold par défaut `src/router/` |
| Auth et middleware Flow | [Flows](../basic/flows.md) |
| Profil hybride (SEO + îlot Vue) | [Guide hybride Vite](./vite-hybrid-app.md) |

---

## Documentation associée

- [Modèles et `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — clé `frontend`](../start/app-manifest.md)

---

[← Retour à l'index](../README.md)

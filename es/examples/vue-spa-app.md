# Tutorial: panel SPA con Vue

[← Volver al índice](../README.md)

Construye un **panel de una sola página con Vue 3** que lee notas desde una API de Pinoox y realiza CRUD simple. El patrón coincide con `com_pinoox_manager` / `spark`: Twig es solo el **shell**; la UI vive en componentes Vue.

**Package:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Profile:** `spa` · **stack:** `vue`  
**Código fuente completo:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — copiar a `apps/`
---

## Requisitos previos

- Pinoox 3.x con PHP 8.1+
- Node.js 18+ y npm
- Familiaridad con el [tutorial de API de notas](./simple-api-app.md) y [plantillas Twig](../basic/templates.md)

---

## Paso 1 — Crear la app y la ruta

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Paso 2 — API de notas (backend)

Crea la tabla, el Model, `NoteApiController` y `routes/api.php` como en [simple-api-app](./simple-api-app.md) (la misma migración `notes` con `title` y `body`).

Registra el archivo de rutas API en `app.php`:

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

## Paso 3 — Scaffold del frontend Vue

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Los archivos del tema quedan en `theme/default/`: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js`, etc.

---

## Paso 4 — `frontend.config.php`

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

Puedes reflejar estos valores en `app.php`:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## Paso 5 — Ruta SPA (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

Cada URL de la app (excepto `/api/v1/…`) sirve `main.twig`; Vue Router gestiona las rutas del lado del cliente.

---

## Paso 6 — Shell Twig

`theme/default/main.twig` (resumen):

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

`pinoox_bootstrap()` inyecta `window.__PINOOX__`, incluido `url.API` para llamadas axios/fetch.

---

## Paso 7 — `.env` del tema (desarrollo)

`theme/default/.env` (copiar desde `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

Para instalaciones en subdirectorio con MAMP, `VITE_SERVER_URL` debe ser el **origen PHP completo** (incluido el prefijo de ruta).

---

## Paso 8 — Cliente Vue (mínimo)

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

`src/App.vue` (CSS en el mismo archivo):

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

El scaffold suele incluir Pinia y Vue Router; para este tutorial puedes omitir Router y montar `App.vue` directamente desde `src/main.js`.

---

## Paso 9 — Desarrollo (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Usa `--no-serve` cuando Apache/MAMP ya sirve PHP. Para ejecutar PHP y Vite juntos:

```bash
php pinoox fe com_acme_vue_notes dev
```

Abre `http://localhost/pinoox/vue-notes` — Vue recarga en caliente; el servidor de desarrollo de Vite hace proxy de `/api` a PHP.

---

## Paso 10 — Build de producción

```bash
php pinoox fe com_acme_vue_notes build
# o: php pinoox theme:frontend build com_acme_vue_notes
```

La salida va a `theme/default/dist/` — Twig lee el manifest para las etiquetas `<script>` / `<link>` de producción.

---

## Probar

1. Ejecuta las migraciones y verifica la API con curl (consulta [simple-api-app](./simple-api-app.md)).
2. Abre la SPA — debe mostrarse la lista vacía o con datos.
3. Añade y elimina notas sin recargar la página completa.

---

## Próximos pasos

| Mejora | Doc |
|---------|-----|
| Vue Router + varias páginas | Scaffold por defecto `src/router/` |
| Auth y middleware Flow | [Flows](../basic/flows.md) |
| Perfil híbrido (SEO + isla Vue) | [Tutorial híbrido Vite](./vite-hybrid-app.md) |

---

## Documentación relacionada

- [Templates & `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — clave `frontend`](../start/app-manifest.md)

---

[← Volver al índice](../README.md)

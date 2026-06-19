# Tutorial: painel SPA Vue

[← Voltar ao índice](../README.md)

Construa um **painel single-page Vue 3** que lê notas de uma API Pinoox e faz CRUD simples. O padrão segue `com_pinoox_manager` / `spark`: Twig é só o **shell**; a UI fica em componentes Vue.

**Pacote:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Perfil:** `spa` · **stack:** `vue`  
**Código completo:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — copie para `apps/`
---

## Pré-requisitos

- Pinoox 3.x com PHP 8.2+
- Node.js 18+ e npm
- Familiaridade com o [tutorial da API de notas](./simple-api-app.md) e [templates Twig](../basic/templates.md)

---

## Etapa 1 — Criar o app and route

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Etapa 2 — API de notas (backend)

Crie a tabela, Model, `NoteApiController` e `routes/api.php` como em [simple-api-app](./simple-api-app.md) (mesma migration `notes` com `title` e `body`).

Registre o arquivo de rotas da API em `app.php`:

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

## Etapa 3 — Scaffold do frontend Vue

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Arquivos do tema ficam em `theme/default/`: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js`, etc.

---

## Etapa 4 — `frontend.config.php`

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

Você pode espelhar esses valores em `app.php`:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## Etapa 5 — Rota SPA (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

Toda URL do app (exceto `/api/v1/…`) serve `main.twig`; o Vue Router trata caminhos no cliente.

---

## Etapa 6 — Shell Twig

`theme/default/main.twig` (resumo):

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

`pinoox_bootstrap()` injeta `window.__PINOOX__`, incluindo `url.API` para chamadas axios/fetch.

---

## Etapa 7 — `.env` do tema (desenvolvimento)

`theme/default/.env` (copie de `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

Em instalações MAMP em subpasta, `VITE_SERVER_URL` deve ser a **origem PHP completa** (incluindo o prefixo de caminho).

---

## Etapa 8 — Cliente Vue (mínimo)

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

O scaffold costuma incluir Pinia e Vue Router; neste tutorial você pode omitir o Router e montar `App.vue` diretamente de `src/main.js`.

---

## Etapa 9 — Desenvolvimento (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Use `--no-serve` quando Apache/MAMP já serve PHP. Para rodar PHP e Vite juntos:

```bash
php pinoox fe com_acme_vue_notes dev
```

Abra `http://localhost/pinoox/vue-notes` — Vue recarrega a quente; o servidor dev Vite faz proxy de `/api` para PHP.

---

## Etapa 10 — Build de produção

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

A saída vai para `theme/default/dist/` — o Twig lê o manifest para tags `<script>` / `<link>` em produção.

---

## Teste

1. Execute migrations e verifique a API com curl (veja [simple-api-app](./simple-api-app.md)).
2. Abra a SPA — lista vazia ou populada deve renderizar.
3. Adicione e exclua notas sem recarregar a página inteira.

---

## Próximos passos

| Melhoria | Doc |
|---------|-----|
| Vue Router + várias páginas | Scaffold padrão `src/router/` |
| Auth e middleware Flow | [Flows](../basic/flows.md) |
| Perfil hybrid (SEO + ilha Vue) | [Tutorial Vite híbrido](./vite-hybrid-app.md) |

---

## Documentação relacionada

- [Templates & `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend` key](../start/app-manifest.md)

---

[← Voltar ao índice](../README.md)

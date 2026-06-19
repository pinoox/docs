# 워크스루: Vue SPA 패널

[← 색인으로 돌아가기](../README.md)

Pinoox API에서 note를 읽고 simple CRUD를 수행하는 **Vue 3 single-page panel**을 만듭니다. 패턴은 `com_pinoox_manager` / `spark`와 같습니다: Twig는 **shell**만; UI는 Vue component에 있습니다.

**Package:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Profile:** `spa` · **stack:** `vue`  
**Full source:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — `apps/`에 copy
---

## 사전 요구 사항

- PHP 8.2+ 환경의 Pinoox 3.x
- Node.js 18+ 및 npm
- [Notes API 실습 가이드](./simple-api-app.md) 및 [Twig 템플릿](../basic/templates.md)에 대한 이해

---

## 단계 1 — 앱 생성 and route

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## 단계 2 — Notes API (backend)

[simple-api-app](./simple-api-app.md)와 같이 table, Model, `NoteApiController`, `routes/api.php` 생성 (동일 `notes` migration, `title`과 `body`).

`app.php`에 API route file 등록:

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

## 단계 3 — Scaffold Vue frontend

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

테마 파일은 `theme/default/`에 생성됩니다: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js` 등.

---

## 단계 4 — `frontend.config.php`

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

이 값들을 `app.php`에도 동일하게 설정할 수 있습니다:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## 단계 5 — SPA route (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

`/api/v1/…`를 제외한 모든 앱 URL은 `main.twig`를 제공하며, Vue Router가 클라이언트 측 경로를 처리합니다.

---

## 단계 6 — Twig shell

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

## 단계 7 — Theme `.env` (development)

`theme/default/.env` (copy from `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

MAMP 하위 디렉터리 설치에서는 `VITE_SERVER_URL`이 경로 접두사를 포함한 **전체 PHP origin**이어야 합니다.

---

## 단계 8 — Vue client (minimal)

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

scaffold는 보통 Pinia와 Vue Router 포함; 이 워크스루에서는 Router 제거하고 `src/main.js`에서 `App.vue` 직접 mount 가능.

---

## 단계 9 — Development (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Apache/MAMP가 이미 PHP를 제공할 때는 `--no-serve`를 사용하세요. PHP와 Vite를 함께 실행하려면:

```bash
php pinoox fe com_acme_vue_notes dev
```

`http://localhost/pinoox/vue-notes` 열기 — Vue hot-reload; Vite dev server가 `/api`를 PHP에 proxy.

---

## 단계 10 — Production build

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

출력은 `theme/default/dist/`에 저장됩니다 — Twig가 프로덕션용 `<script>` / `<link>` 태그를 위해 manifest를 읽습니다.

---

## Test

1. Run migrations and verify the API with curl (see [simple-api-app](./simple-api-app.md)).
2. Open the SPA — empty or populated list should render.
3. Add and delete notes without a full page reload.

---

## 다음 단계

| Upgrade | Doc |
|---------|-----|
| Vue Router + multiple pages | Default scaffold `src/router/` |
| Auth and Flow middleware | [Flows](../basic/flows.md) |
| 하이브리드 프로필 (SEO + Vue 아일랜드) | [Vite 하이브리드 실습 가이드](./vite-hybrid-app.md) |

---

## 관련 문서

- [Templates & `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend` key](../start/app-manifest.md)

---

[← 색인으로 돌아가기](../README.md)

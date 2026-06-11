# Пошаговое руководство: Vue SPA-панель

[← Назад к оглавлению](../README.md)

Создадим **одностраничную панель на Vue 3**, которая читает заметки из Pinoox API и выполняет простой CRUD. Паттерн повторяет `com_pinoox_manager` / `spark`: Twig — это только **оболочка**; UI живёт во Vue-компонентах.

**Пакет:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Профиль:** `spa` · **стек:** `vue`  
**Полный исходный код:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — скопируйте в `apps/`
---

## Предварительные требования

- Pinoox 3.x с PHP 8.1+
- Node.js 18+ и npm
- Знакомство с [руководством по Notes API](./simple-api-app.md) и [Twig-шаблонами](../basic/templates.md)

---

## Шаг 1 — Создание приложения и маршрута

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## Шаг 2 — Notes API (бэкенд)

Создайте таблицу, модель (Model), `NoteApiController` и `routes/api.php`, как в [simple-api-app](./simple-api-app.md) (та же миграция `notes` с `title` и `body`).

Зарегистрируйте файл API-маршрутов в `app.php`:

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

## Шаг 3 — Каркас Vue-фронтенда

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

Файлы темы попадают в `theme/default/`: `main.twig`, `vite.config.js`, `src/main.js`, `src/boot.js` и т.д.

---

## Шаг 4 — `frontend.config.php`

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

Вы можете продублировать эти значения в `app.php`:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## Шаг 5 — Маршрут SPA (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

Каждый URL приложения (кроме `/api/v1/…`) отдаёт `main.twig`; Vue Router обрабатывает клиентские пути.

---

## Шаг 6 — Twig-оболочка

`theme/default/main.twig` (кратко):

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

`pinoox_bootstrap()` внедряет `window.__PINOOX__`, включая `url.API` для вызовов через axios/fetch.

---

## Шаг 7 — `.env` темы (разработка)

`theme/default/.env` (скопируйте из `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

Для установок MAMP в подкаталоге `VITE_SERVER_URL` должен содержать **полный PHP-origin** (включая префикс пути).

---

## Шаг 8 — Vue-клиент (минимальный)

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

`src/App.vue` (CSS в том же файле):

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

Каркас обычно поставляется с Pinia и Vue Router; для этого руководства можно отказаться от Router и монтировать `App.vue` напрямую из `src/main.js`.

---

## Шаг 9 — Разработка (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Используйте `--no-serve`, когда Apache/MAMP уже обслуживает PHP. Чтобы запустить PHP и Vite вместе:

```bash
php pinoox fe com_acme_vue_notes dev
```

Откройте `http://localhost/pinoox/vue-notes` — Vue перезагружается «на лету»; dev-сервер Vite проксирует `/api` к PHP.

---

## Шаг 10 — Продакшен-сборка

```bash
php pinoox fe com_acme_vue_notes build
# или: php pinoox theme:frontend build com_acme_vue_notes
```

Результат попадает в `theme/default/dist/` — Twig читает манифест для продакшен-тегов `<script>` / `<link>`.

---

## Тестирование

1. Запустите миграции и проверьте API с помощью curl (см. [simple-api-app](./simple-api-app.md)).
2. Откройте SPA — должен отобразиться пустой или заполненный список.
3. Добавляйте и удаляйте заметки без полной перезагрузки страницы.

---

## Следующие шаги

| Улучшение | Документация |
|---------|-----|
| Vue Router + несколько страниц | Стандартный каркас `src/router/` |
| Аутентификация и Flow-middleware | [Flows](../basic/flows.md) |
| Профиль hybrid (SEO + Vue-остров) | [Руководство по гибриду Vite](./vite-hybrid-app.md) |

---

## Связанная документация

- [Шаблоны и `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — ключ `frontend`](../start/app-manifest.md)

---

[← Назад к оглавлению](../README.md)

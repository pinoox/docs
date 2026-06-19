# نمونه عملی: پنل Vue (SPA)

[← بازگشت به فهرست](../README.md)

یک **پنل تک‌صفحه‌ای Vue 3** می‌سازیم که یادداشت‌ها را از API پینوکس می‌خواند و CRUD ساده انجام می‌دهد. الگو مشابه `com_pinoox_manager` / `spark` است: Twig فقط **shell** است؛ UI در Vue Router و کامپوننت‌ها.

**پکیج:** `com_acme_vue_notes`  
**آدرس:** `http://localhost/pinoox/vue-notes`  
**پروفایل:** `spa` · **stack:** `vue`  
**سورس کامل:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — کپی در `apps/`
---

## پیش‌نیاز

- پینوکس ۳.x با PHP 8.2+
- Node.js 18+ و npm
- آشنایی با [نمونه API یادداشت](./simple-api-app.md) و [قالب Twig](../basic/templates.md)

---

## گام ۱ — ساخت اپ و مسیر

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## گام ۲ — API یادداشت (بک‌اند)

جدول، Model، `NoteApiController` و `routes/api.php` را مثل [simple-api-app](./simple-api-app.md) بسازید (همان migration `notes` با `title` و `body`).

در `app.php` مسیر API را ثبت کنید:

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

## گام ۳ — scaffold فرانت Vue

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

فایل‌های تم در `theme/default/` ساخته می‌شوند: `main.twig`، `vite.config.js`، `src/main.js`، `src/boot.js` و …

---

## گام ۴ — `frontend.config.php`

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

همین مقادیر را می‌توانید در `app.php` هم override کنید:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## گام ۵ — مسیر SPA (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

همه URLهای اپ (به‌جز `/api/v1/…`) به `main.twig` می‌روند؛ Vue Router مسیرهای داخلی را handle می‌کند.

---

## گام ۶ — shell Twig

`theme/default/main.twig` (خلاصه):

```twig
<!doctype html>
<html lang="fa" dir="rtl">
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

`pinoox_bootstrap()` آبجکت `window.__PINOOX__` را inject می‌کند — شامل `url.API` برای درخواست‌های axios/fetch.

---

## گام ۷ — `.env` تم (توسعه)

`theme/default/.env` (از `.env.example` کپی کنید):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=fa
```

برای MAMP، `VITE_SERVER_URL` باید **همان origin PHP** باشد (مسیر subdirectory شامل شود).

---

## گام ۸ — کلاینت Vue (ساده)

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

`src/App.vue` (با CSS در همان فایل):

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
        <h1 class="page-title">یادداشت‌ها (Vue SPA)</h1>
        <div class="panel">
            <form class="form" @submit.prevent="add">
                <div class="field">
                    <label>عنوان</label>
                    <input v-model="title" required />
                </div>
                <div class="field">
                    <label>متن</label>
                    <textarea v-model="body" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </form>
        </div>
        <div class="panel">
            <p v-if="loading" class="empty">در حال بارگذاری…</p>
            <ul v-else class="note-list">
                <li v-for="n in notes" :key="n.id">
                    <div>
                        <strong>{{ n.title }}</strong>
                        <p v-if="n.body" class="note-body">{{ n.body }}</p>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" @click="remove(n.id)">حذف</button>
                </li>
            </ul>
            <p v-if="!loading && !notes.length" class="empty">یادداشتی نیست.</p>
        </div>
    </main>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }
.page { max-width: 560px; margin: 0 auto; padding: 2rem 1rem; font-family: Tahoma, system-ui, sans-serif; line-height: 1.5; color: #0f172a; }
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

`src/main.js` — mount روی `#app` (scaffold معمولاً Pinia و Router دارد؛ برای این نمونه می‌توانید Router را حذف و فقط `App.vue` را mount کنید).

---

## گام ۹ — توسعه (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

اگر Apache/MAMP دارید، `--no-serve` کافی است. برای PHP و Vite با یک دستور:

```bash
php pinoox fe com_acme_vue_notes dev
```

مرورگر: `http://localhost/pinoox/vue-notes` — تغییرات Vue با HMR اعمال می‌شود؛ API از همان origin پروکسی می‌شود.

---

## گام ۱۰ — build تولید

```bash
php pinoox fe com_acme_vue_notes build
# یا: php pinoox theme:frontend build com_acme_vue_notes
```

خروجی در `theme/default/dist/` — Twig در production از manifest برای `<script>` / `<link>` استفاده می‌کند.

---

## تست

1. migration را اجرا کنید و API را با curl تست کنید (مثل [simple-api-app](./simple-api-app.md)).
2. صفحه SPA را باز کنید؛ لیست خالی یا پر نمایش داده شود.
3. یادداشت اضافه و حذف کنید — بدون reload کامل صفحه.

---

## ایده‌های بعدی

| ارتقا | مستند |
|-------|--------|
| Vue Router + چند صفحه | scaffold پیش‌فرض `src/router/` |
| Auth و Flow | [Flows](../basic/flows.md) |
| پروفایل hybrid (SEO + Vue island) | [نمونه Vite hybrid](./vite-hybrid-app.md) |

---

## مستندات مرتبط

- [قالب Twig و vite_tags() — Templates](../basic/templates.md)
- [CLI فرانت — theme:frontend](../start/cli-reference.md)
- [app.php — کلید `frontend`](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)

# شرح تطبيقي: لوحة Vue SPA

[← العودة إلى الفهرس](../README.md)

ابنِ **لوحة Vue 3 صفحة واحدة** تقرأ الملاحظات من API Pinoox وتنفّذ CRUD بسيط. النمط يطابق `com_pinoox_manager` / `spark`: Twig هو **الغلاف** فقط؛ الواجهة في مكوّنات Vue.

**الحزمة (Package):** `com_acme_vue_notes`  
**الرابط (URL):** `http://localhost/pinoox/vue-notes`  
**الملف الشخصي (Profile):** `spa` · **المكدس (stack):** `vue`  
**الكود المصدري الكامل:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — انسخه إلى `apps/`
---

## المتطلبات المسبقة

- Pinoox 3.x مع PHP 8.1+
- Node.js 18+ و npm
- الإلمام بـ [شرح Notes API](./simple-api-app.md) و [قوالب Twig](../basic/templates.md)

---

## الخطوة 1 — إنشاء التطبيق والمسار

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## الخطوة 2 — Notes API (الخلفية)

أنشئ الجدول، الـ Model، `NoteApiController`، و`routes/api.php` كما في [simple-api-app](./simple-api-app.md) (نفس ترحيل `notes` مع `title` و`body`).

سجّل ملف مسار API في `app.php`:

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

## الخطوة 3 — إنشاء هيكل Vue للواجهة

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

ملفات الثيم تُنشَر في `theme/default/`: `main.twig`، `vite.config.js`، `src/main.js`، `src/boot.js`، وغيرها.

---

## الخطوة 4 — `frontend.config.php`

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

يمكنك عكس هذه القيم في `app.php`:

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## الخطوة 5 — مسار SPA (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

كل رابط التطبيق (باستثناء `/api/v1/…`) يخدم `main.twig`؛ Vue Router يتعامل مع المسارات على جانب العميل.

---

## الخطوة 6 — غلاف Twig

`theme/default/main.twig` (ملخص):

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

`pinoox_bootstrap()` يحقن `window.__PINOOX__`، بما في ذلك `url.API` لاستدعاءات axios/fetch.

---

## الخطوة 7 — `.env` الثيم (التطوير)

`theme/default/.env` (انسخ من `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

لتثبيتات MAMP في مجلد فرعي، يجب أن يكون `VITE_SERVER_URL` **أصل PHP الكامل** (بما في ذلك بادئة المسار).

---

## الخطوة 8 — عميل Vue (مبسّط)

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

`src/App.vue` (CSS في نفس الملف):

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

عادةً يشحن scaffold Pinia و Vue Router؛ لهذا الشرح يمكنك إزالة Router وتحميل `App.vue` مباشرة من `src/main.js`.

---

## الخطوة 9 — التطوير (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

استخدم `--no-serve` عندما يخدم Apache/MAMP PHP بالفعل. لتشغيل PHP و Vite معًا:

```bash
php pinoox fe com_acme_vue_notes dev
```

افتح `http://localhost/pinoox/vue-notes` — Vue يعيد التحميل السريع؛ خادم Vite يوجّه `/api` إلى PHP.

---

## الخطوة 10 — بناء الإنتاج

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

المخرجات في `theme/default/dist/` — Twig يقرأ manifest لوسوم `<script>` / `<link>` في الإنتاج.

---

## الاختبار

1. شغّل الترحيلات وتحقّق من API بـ curl (راجع [simple-api-app](./simple-api-app.md)).
2. افتح SPA — يجب أن تظهر قائمة فارغة أو مملوءة.
3. أضف واحذف ملاحظات دون إعادة تحميل كاملة للصفحة.

---

## الخطوات التالية

| الترقية | التوثيق |
|---------|-----|
| Vue Router + صفحات متعددة | scaffold الافتراضي `src/router/` |
| Auth ووسيط Flow | [Flows](../basic/flows.md) |
| ملف hybrid (SEO + جزيرة Vue) | [شرح Vite hybrid](./vite-hybrid-app.md) |

---

## توثيقات ذات صلة

- [القوالب و `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — مفتاح `frontend`](../start/app-manifest.md)

---

[← العودة إلى الفهرس](../README.md)

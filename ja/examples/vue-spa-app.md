# ウォークスルー: Vue SPA パネル

[← 索引に戻る](../README.md)

Pinoox API からメモを読み取り、シンプルな CRUD を行う **Vue 3 シングルページパネル** を構築します。パターンは `com_pinoox_manager` / `spark` と同様: Twig は **シェル** のみ。UI は Vue コンポーネント内にあります。

**Package:** `com_acme_vue_notes`  
**URL:** `http://localhost/pinoox/vue-notes`  
**Profile:** `spa` · **stack:** `vue`  
**完全なソース:** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — `apps/` にコピー
---

## 前提条件

- PHP 8.2+ 付き Pinoox 3.x
- Node.js 18+ と npm
- [Notes API ウォークスルー](./simple-api-app.md) と [Twig テンプレート](../basic/templates.md) の理解

---

## ステップ 1 — アプリとルートを作成

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## ステップ 2 — Notes API (backend)

[simple-api-app](./simple-api-app.md) と同様にテーブル、Model、`NoteApiController`、`routes/api.php` を作成（同じ `notes` Migration、`title` と `body`）。

`app.php` に API ルートファイルを登録:

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

## ステップ 3 — Scaffold Vue frontend

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

テーマファイルは `theme/default/` に配置: `main.twig`、`vite.config.js`、`src/main.js`、`src/boot.js` など。

---

## ステップ 4 — `frontend.config.php`

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

## ステップ 5 — SPA route (catch-all)

`routes/web.php`:

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

すべてのアプリ URL（`/api/v1/…` 以外）は `main.twig` を配信。Vue Router がクライアントサイドパスを処理します。

---

## ステップ 6 — Twig shell

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

## ステップ 7 — Theme `.env` (development)

`theme/default/.env` (copy from `.env.example`):

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

MAMP サブディレクトリインストールでは、`VITE_SERVER_URL` は **完全な PHP origin**（パスプレフィックス含む）である必要があります。

---

## ステップ 8 — Vue client (minimal)

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

スキャフォールドには通常 Pinia と Vue Router が含まれます。このウォークスルーでは Router を外し、`src/main.js` から直接 `App.vue` をマウントできます。

---

## ステップ 9 — Development (HMR)

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Apache/MAMP が既に PHP を配信している場合は `--no-serve` を使用。PHP と Vite を同時に実行:

```bash
php pinoox fe com_acme_vue_notes dev
```

`http://localhost/pinoox/vue-notes` を開く — Vue はホットリロード。Vite 開発サーバーが `/api` を PHP にプロキシします。

---

## ステップ 10 — Production build

```bash
php pinoox fe com_acme_vue_notes build
# or: php pinoox theme:frontend build com_acme_vue_notes
```

出力は `theme/default/dist/` に — Twig が本番用 `<script>` / `<link>` タグの manifest を読み取ります。

---

## テスト

1. Migration を実行し、curl で API を確認（[simple-api-app](./simple-api-app.md) 参照）。
2. SPA を開く — 空またはデータ入りのリストが表示される。
3. フルページリロードなしでメモを追加・削除する。

---

## 次のステップ

| 拡張 | ドキュメント |
|---------|-----|
| Vue Router + 複数ページ | デフォルトスキャフォールド `src/router/` |
| 認証と Flow ミドルウェア | [Flows](../basic/flows.md) |
| ハイブリッドプロファイル（SEO + Vue アイランド） | [Vite hybrid ウォークスルー](./vite-hybrid-app.md) |

---

## 関連ドキュメント

- [Templates & `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend` key](../start/app-manifest.md)

---

[← 索引に戻る](../README.md)

# 实战演练：Vue SPA 面板

[← 返回索引](../README.md)

构建一个 **Vue 3 单页面板**，从 Pinoox API 读取笔记并执行简单 CRUD。模式与 `com_pinoox_manager` / `spark` 一致：Twig 仅作为**外壳**；UI 在 Vue 组件中。

**包名（Package）：** `com_acme_vue_notes`  
**URL：** `http://localhost/pinoox/vue-notes`  
**配置：** `spa` · **技术栈：** `vue`  
**完整源码：** [docs/source/vue-spa-app/](../../source/vue-spa-app/) — 复制到 `apps/`
---

## 前置条件

- 已安装 PHP 8.2+ 的 Pinoox 3.x
- Node.js 18+ 与 npm
- 熟悉[笔记 API 演练](./simple-api-app.md)与 [Twig 模板](../basic/templates.md)

---

## 步骤 1 — 创建应用与路由

```bash
php pinoox app:create com_acme_vue_notes --simple
php pinoox app:router set /vue-notes com_acme_vue_notes
```

---

## 步骤 2 — 笔记 API（后端）

按 [simple-api-app](./simple-api-app.md) 创建表、Model、`NoteApiController` 和 `routes/api.php`（相同的 `notes` 迁移，含 `title` 和 `body`）。

在 `app.php` 中注册 API 路由文件：

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

## 步骤 3 — 脚手架 Vue 前端

```bash
php pinoox fe com_acme_vue_notes scaffold --stack=vue
php pinoox fe com_acme_vue_notes install
```

主题文件位于 `theme/default/`：`main.twig`、`vite.config.js`、`src/main.js`、`src/boot.js` 等。

---

## 步骤 4 — `frontend.config.php`

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

可在 `app.php` 中镜像这些值：

```php
'frontend' => [
    'profile' => 'spa',
    'stack' => 'vue',
],
```

---

## 步骤 5 — SPA 路由（通配）

`routes/web.php`：

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

除 `/api/v1/…` 外，所有应用 URL 都提供 `main.twig`；Vue Router 处理客户端路径。

---

## 步骤 6 — Twig 外壳

`theme/default/main.twig`（摘要）：

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

`theme/default/partials/scripts.twig`：

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite('src/main.js') }}
```

`pinoox_bootstrap()` 注入 `window.__PINOOX__`，包括供 axios/fetch 使用的 `url.API`。

---

## 步骤 7 — 主题 `.env`（开发）

`theme/default/.env`（从 `.env.example` 复制）：

```env
VITE_SERVER_URL=http://localhost/pinoox/vue-notes
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
VITE_LOCALE=en
```

MAMP 子目录安装时，`VITE_SERVER_URL` 必须是**完整 PHP 源**（含路径前缀）。

---

## 步骤 8 — Vue 客户端（最小示例）

`src/api/notes.js`：

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

`src/App.vue`（CSS 在同文件）：

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

脚手架通常包含 Pinia 和 Vue Router；本演练可直接去掉 Router，从 `src/main.js` 挂载 `App.vue`。

---

## 步骤 9 — 开发（HMR）

```bash
php pinoox fe com_acme_vue_notes dev --no-serve
```

Apache/MAMP 已提供 PHP 时使用 `--no-serve`。同时运行 PHP 与 Vite：

```bash
php pinoox fe com_acme_vue_notes dev
```

打开 `http://localhost/pinoox/vue-notes` — Vue 热重载；Vite 开发服务器将 `/api` 代理到 PHP。

---

## 步骤 10 — 生产构建

```bash
php pinoox fe com_acme_vue_notes build
# 或：php pinoox theme:frontend build com_acme_vue_notes
```

输出到 `theme/default/dist/` — Twig 读取清单以生成生产环境的 `<script>` / `<link>` 标签。

---

## 测试

1. 运行迁移并用 curl 验证 API（见 [simple-api-app](./simple-api-app.md)）。
2. 打开 SPA — 应显示空列表或已有笔记。
3. 添加和删除笔记，无需整页刷新。

---

## 后续步骤

| 升级 | 文档 |
|---------|-----|
| Vue Router + 多页面 | 默认脚手架 `src/router/` |
| 认证与 Flow 中间件 | [Flows](../basic/flows.md) |
| 混合配置（SEO + Vue 岛屿） | [Vite 混合演练](./vite-hybrid-app.md) |

---

## 相关文档

- [模板与 `vite_tags()`](../basic/templates.md)
- [CLI — `theme:frontend`](../start/cli-reference.md)
- [app.php — `frontend` 键](../start/app-manifest.md)

---

[← 返回索引](../README.md)

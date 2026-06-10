import { getUrl } from '../boot.js';

const base = getUrl().API;

async function request(path, options = {}) {
    const res = await fetch(`${base}${path}`, {
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
        ...options,
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'API error');
    return json.data;
}

export const listTasks = () => request('/tasks');
export const addTask = (title) => request('/tasks', { method: 'POST', body: JSON.stringify({ title }) });
export const markDone = (id) => request(`/tasks/${id}/done`, { method: 'PATCH' });
export const removeTask = (id) => request(`/tasks/${id}`, { method: 'DELETE' });

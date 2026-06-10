import { useEffect, useState } from 'react';
import { addTask, listTasks, markDone, removeTask } from './api/tasks.js';
import './App.css';

export default function App() {
    const [tasks, setTasks] = useState([]);
    const [title, setTitle] = useState('');
    const [loading, setLoading] = useState(true);

    async function reload() {
        setLoading(true);
        setTasks(await listTasks());
        setLoading(false);
    }

    useEffect(() => { reload(); }, []);

    async function handleAdd(e) {
        e.preventDefault();
        if (!title.trim()) return;
        await addTask(title.trim());
        setTitle('');
        await reload();
    }

    return (
        <main className="page">
            <h1 className="page-title">کارها (React SPA)</h1>
            <div className="panel">
                <form className="form-row" onSubmit={handleAdd}>
                    <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="عنوان کار" />
                    <button type="submit" className="btn btn-primary">افزودن</button>
                </form>
            </div>
            <div className="panel">
                {loading ? <p className="empty">بارگذاری…</p> : (
                    <ul className="task-list">
                        {tasks.map((t) => (
                            <li key={t.id} className={t.status === 'done' ? 'done' : ''}>
                                <span style={{ flex: 1 }}>{t.title}</span>
                                {t.status !== 'done' && (
                                    <button type="button" className="btn btn-sm" onClick={() => markDone(t.id).then(reload)}>✓</button>
                                )}
                                <button type="button" className="btn btn-sm" onClick={() => removeTask(t.id).then(reload)}>×</button>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </main>
    );
}

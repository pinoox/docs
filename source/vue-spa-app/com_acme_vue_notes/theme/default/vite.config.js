import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const target = env.VITE_SERVER_URL || 'http://127.0.0.1:8000';

    return {
        base: './',
        plugins: [vue()],
        build: {
            manifest: true,
            rollupOptions: {
                input: 'src/main.js',
            },
        },
        server: {
            proxy: {
                '/api': target,
            },
        },
    };
});

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [vue()],
    build: {
        lib: {
            entry: path.resolve(__dirname, 'src/resources/js/app.js'),
            name: 'CharlesgCms',
            fileName: (format) => `charlesg-cms.${format}.js`,
            formats: ['es', 'umd']
        },
        rollupOptions: {
            external: ['vue'],
            output: {
                globals: {
                    vue: 'Vue'
                },
            }
        }
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'src/resources/js'),
            '@cms': path.resolve(__dirname, 'src/resources/js')
        },
    },
});
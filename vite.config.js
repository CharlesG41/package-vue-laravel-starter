import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [vue()],
    build: {
        lib: {
            entry: path.resolve(__dirname, 'src/resources/js/app.js'),
            name: 'Charlesg',
            fileName: (format) => `charlesg.${format}.js`,
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
            '@charlesg': path.resolve(__dirname, 'src/resources/js')
        },
    },
});
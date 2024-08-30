import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [vue()],
    build: {
        lib: {
            entry: path.resolve(__dirname, 'src/resources/js/app.js'),
            name: 'CharlesgCms',
            fileName: (format) => `charlesg-cms.${format}.js`
        },
        rollupOptions: {
            external: ['vue'],
            output: {
                globals: {
                    vue: 'Vue'
                },
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css') return 'charlesg-cms.css';
                    return assetInfo.name;
                },
            }
        }
    },
    css: {
        postcss: {
            plugins: [
                require('tailwindcss'),
                require('autoprefixer'),
            ],
        }
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'src/resources/js'),
            '@cms': path.resolve(__dirname, 'src/resources/js')
        },
    },
});
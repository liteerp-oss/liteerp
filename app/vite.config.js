import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        })
    ],

    resolve: {
        alias: {
            '@i18n': path.resolve(__dirname, 'resources/js/i18n'),
            '@components': path.resolve(__dirname, 'resources/js/react/components'),
            '@pages': path.resolve(__dirname, 'resources/js/react/pages'),
            '@common': path.resolve(__dirname, 'resources/js/react/common'),
            '@redux': path.resolve(__dirname, 'resources/js/react/redux'),
            '@libraries': path.resolve(__dirname, 'resources/js/react/libraries'),
            '@wrappers': path.resolve(__dirname, 'resources/js/react/wrappers'),
            '@layouts': path.resolve(__dirname, 'resources/js/react/layouts'),
            '@core': path.resolve(__dirname, 'resources/js/core'),
            '@services': path.resolve(__dirname, 'resources/js/react/services'),
        }
    },

    build: {
        chunkSizeWarningLimit: 1600
    }
});

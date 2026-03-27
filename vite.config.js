import {
    defineConfig,
    loadEnv,
}              from 'vite';
import laravel from 'laravel-vite-plugin';
import vue     from '@vitejs/plugin-vue';

export default ({ mode }) => {

    process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

    return defineConfig({
        server : {
            https     : false,
            host      : true,
            port      : process.env.VITE_PORT,
            strictPort: true,
            hmr       : { host: process.env.APP_HOST, protocol: 'ws' },
            watch     : { usePolling: true },
        },
        plugins: [
            laravel({
                input  : [
                    'resources/sass/app.scss',
                    'resources/js/app.js',
                    'resources/js/admin.js',
                    'resources/js/profile.js',
                ],
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: { base: null, includeAbsolute: false },
                },
            }),
        ],
        resolve: {
            alias: {
                vue           : 'vue/dist/vue.esm-bundler.js',
                '@components' : '/resources/js/components',
                '@common'     : '/resources/js/components/common',
                '@composables': '/resources/js/composables',
                '@utils'      : '/resources/js/utils',
                '@api'        : '/resources/js/api',
            },
        },
        // ✅ ВАЖНО: все настройки CSS должны быть внутри этого блока
        css         : {
            preprocessorOptions: {
                scss: {
                    // Подавляем ВСЕ известные типы предупреждений
                    silenceDeprecations: [
                        'color-functions',   // Отключает предупреждения о red()/green()/blue() [citation:2][citation:7]
                        'global-builtin',    // Отключает предупреждения о mix()/unit() [citation:2][citation:7]
                        'import',            // Отключает предупреждения о @import [citation:2][citation:7]
                        'if-function',       // Отключает предупреждения об устаревшем if() [citation:5]
                    ],
                },
            },
        },
        optimizeDeps: {
            include: ['axios', 'jquery', 'bootstrap', 'vue', 'vuex'],
        },
    });
};
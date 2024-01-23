import { defineConfig, loadEnv } from 'vite';
import laravel          from 'laravel-vite-plugin';
import vue              from '@vitejs/plugin-vue';

export default ({ mode }) => {

    process.env = {...process.env, ...loadEnv(mode, process.cwd())};

    return defineConfig({server : {
            https     : false,
            host      : true,
            port      : process.env.VITE_PORT,
            strictPort: true,
            hmr       : { host: 'localhost', protocol: 'ws' },
            watch     : {
                usePolling: true,
            },
        },
        plugins: [
            laravel({
                input  : [
                    'resources/sass/app.scss',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base           : null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                vue: 'vue/dist/vue.esm-bundler.js',
            },
        },
    });
}

const mix = require('laravel-mix');
const path = require('path');
require('mix-env-file');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.env(process.env.ENV_FILE);
mix.ts('resources/js/app.ts', 'public/js')
    .ts(
        'resources/js/firebaseServiceWorker.js',
        'public/firebase-messaging-sw.js'
    )
    .postCss('resources/css/app.css', 'public/css', [require('tailwindcss')])
    .css('resources/css/admin/reset.css', 'public/css/admin')
    .css('resources/css/admin/style.css', 'public/css/admin')
    .css('resources/css/login/reset.css', 'public/css/login')
    .css('resources/css/login/style.css', 'public/css/login')
    .css('resources/css/sp/reset.css', 'public/css/sp')
    .css('resources/css/sp/style.css', 'public/css/sp')
    .vue()
    .version()
    .sourceMaps(!mix.inProduction(), 'source-map'); // NOTE: sourcemapファイルは本番に上げないよう注意

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.ts', '.vue'],
        alias: {
            '/@': path.resolve(__dirname, 'resources'),
        },
    },
    watchOptions: {
        ignored: /node_modules/,
    },
});

if (mix.inProduction()) {
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true,
                },
            },
        },
    });
}

module.exports = {
    module: {
        rules: [
            {
                test: /\.(png|jpe?g|gif)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            esModule: false,
                        },
                    },
                ],
            },
        ],
    },
};

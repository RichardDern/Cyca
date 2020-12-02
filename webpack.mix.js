const mix = require("laravel-mix");
const path = require("path");
const webpack = require("webpack");

mix.webpackConfig({
    resolve: {
        alias: {
            ziggy: path.resolve("vendor/tightenco/ziggy/src/js/route.js"),
        },
    },
    plugins: [
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: false,
        }),
    ],
});

mix.postCss("resources/css/app.css", "public/css/app.css", [
    require("postcss-import"),
    require("tailwindcss"),
    require("postcss-nested"),
    require("autoprefixer"),
]);

mix.js("resources/js/app.js", "public/js").vue();
mix.js("resources/js/groups.js", "public/js").vue();
mix.js("resources/js/highlights.js", "public/js").vue();
mix.js("resources/js/import.js", "public/js").vue();

if (mix.inProduction()) {
    mix.version();
}

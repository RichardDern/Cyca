const mix = require("laravel-mix");
const path = require("path");
const fs = require("fs");

mix.webpackConfig({
    resolve: {
        alias: {
            ziggy: path.resolve("vendor/tightenco/ziggy/src/js/route.js")
        }
    }
});

const themes = fs.readdirSync("resources/themes/");

themes.forEach(theme => {
    const dir = "resources/themes/" + theme;

    if (fs.lstatSync(dir).isDirectory()) {
        mix.postCss(
            dir + "/theme.css",
            "public/themes/" + theme + "/theme.css",
            [
                require("postcss-import"),
                require("tailwindcss")(dir + "/theme.js"),
                require("postcss-nested"),
                require("autoprefixer")
            ]
        );

        mix.copy(dir + "/theme.json", "public/themes/" + theme + "/");
        mix.copy(dir + "/resources/", "public/themes/" + theme + "/");
    }
});

mix.js("resources/js/app.js", "public/js");
mix.js("resources/js/themes-browser.js", "public/js");

if (mix.inProduction()) {
    mix.version();
}

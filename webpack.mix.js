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

const publicDir = "public/themes";
const themesDir = "resources/themes";
const themes = fs.readdirSync(themesDir);

themes.forEach(theme => {
    const themeDir = themesDir + "/" + theme;
    const publicThemeDir = publicDir + "/" + theme;

    if (fs.lstatSync(themeDir).isDirectory()) {
        mix.postCss(
            themeDir + "/theme.css",
            publicThemeDir + "/theme.css",
            [
                require("postcss-import"),
                require("tailwindcss")(themeDir + "/theme.js"),
                require("postcss-nested"),
                require("autoprefixer")
            ]
        );

        mix.copy(themeDir + "/theme.json", publicThemeDir + "/");
        mix.copy(themeDir + "/resources/", publicThemeDir + "/");
        mix.copy(publicThemeDir, themeDir + "/dist");
    }
});

mix.js("resources/js/app.js", "public/js");
mix.js("resources/js/themes-browser.js", "public/js");

if (mix.inProduction()) {
    mix.version();
}

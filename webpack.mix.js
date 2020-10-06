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

    if (fs.lstatSync(themeDir).isDirectory()) {
        const json = require("./" + themeDir + "/theme.json");
        const themeName = json["name"]
            .match(
                /[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g
            )
            .map(x => x.toLowerCase())
            .join("-");

        const publicThemeDir = publicDir + "/" + themeName;

        mix.postCss(themeDir + "/theme.css", publicThemeDir + "/theme.css", [
            require("postcss-import"),
            require("tailwindcss")(themeDir + "/theme.js"),
            require("postcss-nested"),
            require("autoprefixer")
        ]);

        mix.copy(themeDir + "/theme.json", publicThemeDir + "/");
        mix.copy(themeDir + "/resources/", publicThemeDir + "/");
        mix.copy(publicThemeDir, themeDir + "/dist");
    }
});

mix.js("resources/js/app.js", "public/js");
mix.js("resources/js/themes-browser.js", "public/js");
mix.js("resources/js/import.js", "public/js");

if (mix.inProduction()) {
    mix.version();
}

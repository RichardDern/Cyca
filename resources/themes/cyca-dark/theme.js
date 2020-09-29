/* -------------------------------------------------------------------------- */
/* ----| Dark (default) theme |---------------------------------------------- */
/* -------------------------------------------------------------------------- */

const themeName = "cyca-dark";
const fonts = require('./fonts');
const colors = require('./colors');
const plugin = require('tailwindcss/plugin');

/**
 * Theme definition
 */
const theme = {
    fontFamily: fonts,
    colors: colors
};

/**
 * Rest of tailwind's config
 */
module.exports = {
    purge: [
        "../../views/**/*.blade.php",
        "../../css/**/*.css",
        "../../js/components/**/*.vue"
    ],
    theme: theme,
    variants: {},
    plugins: [
        /**
         * Plugin to add custom font
         */
        plugin(function({addBase, config}) {
            const fontUrl = "/themes/" + themeName + "/fonts/Quicksand-Medium.ttf";

            addBase({
                '@font-face': {
                    fontFamily: "Quicksand",
                    src: 'url(' + fontUrl + ')'
                }
            })
        }),
        require("@tailwindcss/ui")
    ]
};

/* -------------------------------------------------------------------------- */
/* ----| Dark (default) theme |---------------------------------------------- */
/* -------------------------------------------------------------------------- */

const _ = require('lodash');
const plugin = require("tailwindcss/plugin");

const theme = _.merge(require("../baseTheme.js"), {
    /**
     * Adding theme-specifics
     */
    theme: {
        fontFamily: {
            sans:
                'Quicksand, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"'
        },
        colors: require("./colors")
    },
    plugins: [
        /**
         * Plugin to add custom font
         */
        plugin(function({ addBase, config }) {
            const fontUrl =
                "/themes/cyca-dark/fonts/Quicksand-Medium.ttf";

            addBase({
                "@font-face": {
                    fontFamily: "Quicksand",
                    src: "url(" + fontUrl + ")"
                }
            });
        })
    ]
});

module.exports = theme;

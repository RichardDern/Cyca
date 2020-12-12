const colors = require("tailwindcss/colors");
const plugin = require("tailwindcss/plugin");

module.exports = {
    purge: {
        mode: "all",
        content: [
            "resources/views/**/*.blade.php",
            "resources/css/**/*.css",
            "resources/js/components/**/*.vue",
        ],
    },
    darkMode: "class",
    theme: {
        extend: {
            fontFamily: {
                sans:
                    'Quicksand, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
            },
            fontSize: {
                "2xs": ".675rem",
            },
        },
        colors: {
            white: colors.white,
            black: colors.black,
            gray: {
                ...colors.trueGray,
                ...{
                    150: "#EDEDED",
                    750: "#333333",
                    850: "#1F1F1F",
                },
            },
            blue: colors.blue,
            green: colors.emerald,
            orange: colors.orange,
            red: colors.red,
            purple: colors.purple,
        },
    },
    variants: {
        extend: {
            backgroundColor: ["odd"],
        },
    },
    plugins: [
        plugin(function ({ addBase }) {
            const fontUrl = "/fonts/Quicksand-Medium.ttf";

            addBase({
                "@font-face": {
                    fontFamily: "Quicksand",
                    src: "url(" + fontUrl + ")",
                },
            });
        }),
    ],
};

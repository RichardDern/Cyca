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
    plugins: [],
};

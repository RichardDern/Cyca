module.exports = {
    purge: [
        "./resources/views/**/*.blade.php",
        "./resources/css/**/*.css",
        "./resources/js/components/**/*.vue",
        "./app/Models/Folder.php"
    ],
    theme: {
        fontFamily: {
            'sans': 'Quicksand, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"'
        },
        extend: {
            colors: {
                gray: {
                    "100": "#5c5c5c",
                    "200": "#525252",
                    "300": "#474747",
                    "400": "#3d3d3d",
                    "500": "#333333",
                    "600": "#292929",
                    "700": "#1f1f1f",
                    "800": "#141414",
                    "900": "#0a0a0a"
                }
            }
        }
    },
    variants: {},
    plugins: [require("@tailwindcss/ui")]
};

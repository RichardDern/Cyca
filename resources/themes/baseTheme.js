/**
 * Common code to all themes.
 */

const baseTheme = {
    purge: {
        mode: 'all',
        content: [
            "resources/views/**/*.blade.php",
            "resources/css/**/*.css",
            "resources/js/components/**/*.vue"
        ]
    },
    plugins: [,
        require("@tailwindcss/ui")
    ]
};

module.exports = baseTheme;

/* -------------------------------------------------------------------------- */
/* ----| Light theme |------------------------------------------------------- */
/* -------------------------------------------------------------------------- */

const _ = require('lodash');

const theme = _.merge(require("../cyca_theme_dark/theme"), {
    /**
     * Theme specifics
     */
    theme: {
        colors: require("./colors")
    }
});

module.exports = theme;

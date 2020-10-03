/* -------------------------------------------------------------------------- */
/* ----| Light theme |------------------------------------------------------- */
/* -------------------------------------------------------------------------- */

const _ = require('lodash');

const theme = _.merge(require("../cyca-dark/theme"), {
    /**
     * Theme specifics
     */
    theme: {
        colors: require("./colors")
    }
});

module.exports = theme;

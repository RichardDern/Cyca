/* -------------------------------------------------------------------------- */
/* ----| Colors definition |------------------------------------------------- */
/* -------------------------------------------------------------------------- */

const baseColors = require('./colorScheme');

/**
 * Colors used in the CSS by calling ```theme("colors.<named color>")```
 * For a hovered danger button background color, we will call
 * ```theme("colors.button.danger.bg-hover")```
 */
module.exports = {
    /**
     * Base
     */
    a: {
        normal: baseColors.blue[800],
        hover: baseColors.blue[700]
    },
    article: {
        text: baseColors.black,
        bg: baseColors.gray[500],
        border: baseColors.gray[500],
        body: baseColors.black
    },
    body: {
        text: baseColors.black,
        bg: baseColors.gray[100]
    },
    label: {
        text: baseColors.gray[900]
    },
    input: {
        text: baseColors.black,
        bg: baseColors.white
    },
    scrollbar: baseColors.gray[900],
    /**
     * Components
     */
    badge: {
        text: baseColors.black,
        bg: baseColors.gray[700],
        "bg-article": baseColors.gray[700]
    },
    caret: baseColors.gray[900],
    "folders-tree": baseColors.gray[500],
    "documents-list": baseColors.gray[400],
    "feeditems-list": baseColors.gray[300],
    "details-view": baseColors.gray[200],
    "list-item": {
        text: baseColors.black,
        active: {
            text: baseColors.black,
            bg: baseColors.gray[700]
        },
    },
    "feed-item": {
        text: baseColors.black,
        active: {
            text: baseColors.black,
            bg: baseColors.gray[500]
        },
        read: baseColors.gray[900],
        meta: baseColors["cool-gray"][700]
    },
    account: {
        menu: {
            bg: baseColors.gray[300],
            item: {
                text: baseColors.gray[900],
                selected: {
                    text: baseColors.black
                }
            }
        },
        content: {
            bg: baseColors.gray[200]
        }
    },
};

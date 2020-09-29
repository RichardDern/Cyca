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
    baseColors,
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
    dl: {
        text: baseColors.white
    },
    dt: {
        bg: baseColors.gray[700]
    },
    dd: {
        bg: baseColors.gray[400]
    },
    button: {
        text: baseColors.white,
        danger: {
            bg: baseColors.red[800],
            "bg-hover": baseColors.red[900]
        },
        success: {
            bg: baseColors.green[600],
            "bg-hover": baseColors.green[800]
        },
        info: {
            bg: baseColors.blue[700],
            "bg-hover": baseColors.blue[800]
        }
    },
    label: {
        text: baseColors.gray[900]
    },
    formGroup: {
        text: baseColors.white
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
        "dragged-over": {
            bg: baseColors.green[900]
        },
        "cannot-drop": {
            bg: baseColors.red[900]
        }
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
                hovered: baseColors.blue[500],
                selected: {
                    text: baseColors.black
                }
            }
        },
        content: {
            bg: baseColors.gray[200]
        }
    },
    folders: {
        common: baseColors.yellow[500],
        unread: {
            "not-empty": baseColors.purple[500],
            empty: baseColors.gray[100]
        },
        root: baseColors.blue[500],
        account: baseColors.green[600],
        logout: baseColors.red[800]
    }
};

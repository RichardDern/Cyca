/* -------------------------------------------------------------------------- */
/* ----| Colors definition |------------------------------------------------- */
/* -------------------------------------------------------------------------- */

const baseColors = require("./colorScheme");

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
    "a": {
        normal: baseColors.blue[400],
        hover: baseColors.blue[500],
        letter: baseColors.white,
        capital: baseColors.white,
        empty: baseColors.blue[400],
        number: baseColors.green[400],
        other: baseColors.purple[500],
        operator: baseColors.purple[500],
        suspicious: baseColors.red[500],
    },
    "article": {
        text: baseColors.white,
        bg: baseColors.gray[300],
        border: baseColors.gray[700],
        body: baseColors.white,
    },
    "body": {
        text: baseColors.white,
        bg: baseColors.gray[900],
    },
    "dl": {
        text: baseColors.white,
    },
    "dt": {
        bg: baseColors.gray[700],
    },
    "dd": {
        bg: baseColors.gray[400],
    },
    "button": {
        text: baseColors.white,
        danger: {
            "bg": baseColors.red[800],
            "bg-hover": baseColors.red[900],
        },
        success: {
            "bg": baseColors.green[600],
            "bg-hover": baseColors.green[800],
        },
        info: {
            "bg": baseColors.blue[700],
            "bg-hover": baseColors.blue[800],
        },
        secondary: {
            "bg": baseColors.gray[500],
            "bg-hover": baseColors.gray[600],
        },
    },
    "label": {
        text: baseColors.gray[100],
    },
    "formGroup": {
        text: baseColors.white,
    },
    "input": {
        text: baseColors.white,
        bg: baseColors.gray[300],
    },
    "scrollbar": baseColors.gray[300],
    /**
     * Components
     */
    "badge": {
        "text": baseColors.white,
        "bg": baseColors.gray[300],
        "bg-article": baseColors.gray[500],
    },
    "caret": baseColors.gray[100],
    "folders-tree": {
        bg: baseColors.gray[800],
        separator: baseColors.gray[700],
    },
    "documents-list": baseColors.gray[700],
    "feeditems-list": baseColors.gray[600],
    "details-view": baseColors.gray[500],
    "list-item": {
        "text": baseColors.white,
        "active": {
            text: baseColors.white,
            bg: baseColors.gray[400],
        },
        "dragged-over": {
            bg: baseColors.green[900],
        },
        "cannot-drop": {
            bg: baseColors.red[900],
        },
    },
    "feed-item": {
        text: baseColors.white,
        active: {
            text: baseColors.white,
            bg: baseColors.gray[400],
        },
        read: baseColors.gray[100],
        meta: baseColors["cool-gray"][500],
    },
    "feeds-list": {
        bg: baseColors.gray[600],
    },
    "account": {
        menu: {
            bg: baseColors.gray[800],
            item: {
                text: baseColors.gray[300],
                hovered: baseColors.blue[500],
                selected: {
                    text: baseColors.white,
                },
            },
        },
        content: {
            bg: baseColors.gray[700],
        },
    },
    "folders": {
        "common": baseColors.yellow[500],
        "unread": {
            "not-empty": baseColors.purple[500],
            "empty": baseColors.gray[100],
        },
        "root": baseColors.blue[500],
        "account": baseColors.green[600],
        "logout": baseColors.red[800],
        "inline-folder": {
            color: baseColors.gray[100],
            bg: baseColors.gray[600],
        },
        "group": baseColors.green[600],
    },
    "themes-browser": {
        card: {
            "border-color": baseColors.gray[300],
            "selected": {
                "border-color": baseColors.green[600],
            },
            "hovered": {
                "border-color": baseColors.blue[500],
            },
        },
    },
    "alerts": {
        success: {
            bg: baseColors.green[600],
            text: baseColors.white,
        },
        error: {
            bg: baseColors.red[900],
            text: baseColors.white,
        },
        warning: {
            bg: baseColors.yellow[500],
            text: baseColors.white,
        },
    },
    "timeline": {
        h2: {
            bg: baseColors.gray[700],
        },
    },
    "groups-browser": {
        "item": {
            bg: baseColors.gray[600],
            border: baseColors.gray[500],
        },
        "selected-item": {
            border: baseColors.green[600],
        },
        "status": {
            own: {
                bg: baseColors.purple[500],
            },
            created: {
                bg: baseColors.purple[500],
            },
            invited: {
                bg: baseColors.yellow[500],
            },
            accepted: {
                bg: baseColors.green[600],
            },
            rejected: {
                bg: baseColors.red[500],
            },
            left: {
                bg: baseColors.gray[900],
            },
            joining: {
                bg: baseColors.yellow[500],
            },
        },
    },
};

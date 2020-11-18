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
    /**
     * Base
     */
    "a": {
        normal: baseColors.blue[800],
        hover: baseColors.blue[700],
        letter: baseColors.black,
        capital: baseColors.black,
    },
    "article": {
        text: baseColors.black,
        bg: baseColors.gray[500],
        border: baseColors.gray[600],
        body: baseColors.black,
    },
    "body": {
        text: baseColors.black,
        bg: baseColors.gray[100],
    },
    "dl": {
        text: baseColors.black,
    },
    "dt": {
        bg: baseColors.gray[600],
    },
    "dd": {
        bg: baseColors.gray[300],
    },
    "button": {
        text: baseColors.white,
        danger: {
            "bg": baseColors.red[900],
            "bg-hover": baseColors.red[800],
        },
        success: {
            "bg": baseColors.green[900],
            "bg-hover": baseColors.green[800],
        },
        info: {
            "bg": baseColors.blue[900],
            "bg-hover": baseColors.blue[800],
        },
    },
    "label": {
        text: baseColors.gray[900],
    },
    "input": {
        text: baseColors.black,
        bg: baseColors.white,
    },
    "scrollbar": baseColors.gray[900],
    /**
     * Components
     */
    "badge": {
        "text": baseColors.black,
        "bg": baseColors.gray[700],
        "bg-article": baseColors.gray[700],
    },
    "caret": baseColors.gray[900],
    "folders-tree": {
        bg: baseColors.gray[400],
        separator: baseColors.gray[500],
    },
    "documents-list": baseColors.gray[300],
    "feeditems-list": baseColors.gray[200],
    "details-view": baseColors.gray[100],
    "list-item": {
        "text": baseColors.black,
        "active": {
            text: baseColors.black,
            bg: baseColors.gray[600],
        },
        "dragged-over": {
            bg: baseColors.green[100],
        },
        "cannot-drop": {
            bg: baseColors.red[300],
        },
    },
    "feed-item": {
        text: baseColors.black,
        active: {
            text: baseColors.black,
            bg: baseColors.gray[500],
        },
        read: baseColors.gray[900],
        meta: baseColors["cool-gray"][700],
    },
    "feeds-list": {
        bg: baseColors.gray[400],
    },
    "account": {
        menu: {
            bg: baseColors.gray[400],
            item: {
                text: baseColors.gray[900],
                selected: {
                    text: baseColors.black,
                },
            },
        },
        content: {
            bg: baseColors.gray[200],
        },
    },
    "alerts": {
        success: {
            bg: baseColors.green[100],
            text: baseColors.green[900],
        },
        error: {
            bg: baseColors.red[100],
            text: baseColors.red[900],
        },
        warning: {
            bg: baseColors.yellow[100],
            text: baseColors.yellow[500],
        },
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
    "folders": {
        "common": baseColors.yellow[500],
        "unread": {
            "not-empty": baseColors.purple[500],
            "empty": baseColors.white,
        },
        "root": baseColors.blue[800],
        "account": baseColors.green[800],
        "logout": baseColors.red[800],
        "inline-folder": {
            color: baseColors.black,
            bg: baseColors.gray[300],
        },
    },
    "timeline": {
        h2: {
            bg: baseColors.gray[200],
        },
    },
    "groups-browser": {
        "item": {
            bg: baseColors.gray[400],
            border: baseColors.gray[600],
        },
        "selected-item": {
            border: baseColors.green[600],
        },
    },
};

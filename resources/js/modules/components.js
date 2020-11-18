const sets = {
    app: [
        "Details/DetailsDocument",
        "Details/DetailsDocuments",
        "Details/DetailsFeedItem",
        "Details/DetailsFolder",
        "DateTime",
        "DocumentItem",
        "DocumentsList",
        "FeedItem",
        "FeedItemsList",
        "FolderItem",
        "FoldersTree",
        "GroupItem"
    ],
    import: ["Importers/ImportFromCyca", "Importer"],
    themesBrowser: ["ThemeCard", "ThemesBrowser"],
    highlights: ["Highlights", "Highlight"],
    historyBrowser: ["HistoryBrowser", "DateTime"],
    groups: [
        "GroupsBrowser",
        "GroupsBrowser/GroupsBrowserItem",
        "GroupsBrowser/GroupForm"
    ]
};

module.exports = function(set) {
    const files = sets[set];

    files.map(file => {
        const componentName = file
            .split("/")
            .pop()
            .match(
                /[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g
            )
            .map(x => x.toLowerCase())
            .join("-");

        Vue.component(
            componentName,
            require("../components/" + file + ".vue").default
        );
    });
};

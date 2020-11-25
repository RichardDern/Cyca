const sets = {
    app: [
        "Details/DetailsDocument",
        "Details/DetailsDocuments",
        "Details/DetailsFeedItem",
        "Details/DetailsFolder",
        "DocumentsList",
        "FeedItemsList",
        "FoldersTree",
    ],
    import: ["Importer"],
    themesBrowser: ["ThemeCard", "ThemesBrowser"],
    highlights: ["Highlights", "Highlight"],
    historyBrowser: ["HistoryBrowser"],
    groups: [
        "GroupsBrowser"
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

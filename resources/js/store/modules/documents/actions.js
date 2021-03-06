import { collect } from "collect.js";

/**
 * Actions on documents
 */
export default {
    /**
     * Display a listing of the resource.
     */
    async index(
        { commit, dispatch, getters },
        { documents, updateFeedItems = true }
    ) {
        commit("setDocuments", documents);

        let selectedDocuments = getters.selectedDocuments;

        if (!selectedDocuments || selectedDocuments.length === 0) {
            selectedDocuments = documents;
        }

        if (updateFeedItems) {
            await dispatch("feedItems/index", selectedDocuments, {
                root: true,
            });
        }
    },

    /**
     * Store a newly created resource in storage.
     */
    async store({ dispatch }, { url, folder_id, group_id }) {
        const data = await api.post(route("document.store"), {
            url: url,
            folder_id: folder_id,
            group_id: group_id,
        });

        dispatch("index", { documents: data });
    },

    /**
     * Mark specified documents as selected.
     */
    selectDocuments({ commit, dispatch }, { documents, selectFirstUnread }) {
        commit("setSelectedDocuments", documents);

        commit("feedItems/setSelectedFeedItems", [], {
            root: true,
        });

        dispatch("feedItems/index", documents, { root: true }).then(
            function () {
                if (selectFirstUnread && documents) {
                    dispatch("feedItems/selectFirstUnreadFeedItem", null, {
                        root: true,
                    });
                }
            }
        );
    },

    /**
     * Select first document in currently displayed list
     */
    selectFirstDocument({ getters, dispatch }) {
        const document = collect(getters.documents).first();

        dispatch("selectDocuments", [document]);
    },

    /**
     * Select first document containing unread feed items in currently displayed
     * list
     */
    selectFirstDocumentWithUnreadItems(
        { getters, dispatch, rootGetters },
        { exclude, selectFirstUnread }
    ) {
        if (!exclude) {
            exclude = collect(getters.selectedDocuments).pluck("id").all();
        }

        const document = collect(getters.documents)
            .where("feed_item_states_count", ">", 0)
            .whereNotIn("id", exclude)
            .first();

        if (document) {
            dispatch("selectDocuments", {
                documents: [document],
                selectFirstUnread: selectFirstUnread,
            });
        } else {
            if (rootGetters["folders/selectedFolder"].type === "unread_items") {
                dispatch("selectDocuments", {
                    selectFirstUnread: selectFirstUnread,
                });
            }
        }
    },

    /**
     * Remember documents being dragged
     */
    startDraggingDocuments({ commit }, documents) {
        commit("setDraggedDocuments", documents);
    },

    /**
     * Forget dragged documents
     */
    stopDraggingDocuments({ commit }) {
        commit("setDraggedDocuments", []);
    },

    /**
     * Move selected documents into specified folder
     */
    async dropIntoFolder(
        { getters, commit, dispatch },
        { sourceFolder, targetFolder }
    ) {
        const documents = collect(getters.draggedDocuments).pluck("id").all();

        if (!documents || documents.length === 0) {
            return;
        }

        const response = await api.post(
            route("document.move", {
                sourceFolder: sourceFolder.id,
                targetFolder: targetFolder.id,
            }),
            {
                documents: documents,
            }
        );

        commit("setDraggedDocuments", []);
        commit("setSelectedDocuments", []);

        const newDocumentsList = collect(getters.documents).whereNotIn(
            "id",
            documents
        );

        dispatch("index", { documents: newDocumentsList });
        dispatch("feedItems/index", getters.feeds, { root: true });
        dispatch("feedItems/updateUnreadFeedItemsCount", response, {
            root: true,
        });
    },

    /**
     * Increment visits for specified document
     */
    async incrementVisits({ commit }, { document }) {
        const data = await api.post(
            route("document.visit", { document: document.id })
        );

        commit("update", {
            document: document,
            newProperties: data,
        });
    },

    /**
     * Open specified document in background
     */
    openDocument({ dispatch }, { document }) {
        window.open(document.url);

        dispatch("incrementVisits", { document: document });
    },

    /**
     * Remove specified documents from specified folder
     */
    async destroy({ commit, getters, dispatch }, { folder, documents }) {
        commit("setSelectedDocuments", []);

        const data = await api.post(
            route("document.destroy_bookmarks", { folder: folder.id }),
            {
                documents: collect(documents).pluck("id").all(),
            }
        );

        dispatch("index", { documents: data });
    },

    /**
     * Update the specified resource in storage.
     */
    update({ commit, getters }, { documentId, newProperties }) {
        const document = getters.documents.find((d) => d.id == documentId);

        if (!document) {
            return;
        }

        commit("update", { document: document, newProperties: newProperties });
    },

    /**
     * Load every data available for specified document, unless it was already
     * loaded
     */
    load({ dispatch }, document) {
        if (!document.loaded) {
            return api
                .get(route("document.show", document))
                .then(function (response) {
                    response.loaded = true;

                    return dispatch("update", {
                        documentId: document.id,
                        newProperties: response,
                    });
                });
        }
    },

    followFeed({ commit }, feed) {
        api.post(route("feed.follow", feed));
        commit("ignoreFeed", { feed: feed, ignored: false });
    },

    ignoreFeed({ commit }, feed) {
        api.post(route("feed.ignore", feed));
        commit("ignoreFeed", { feed: feed, ignored: true });
    },
};

/**
 * Actions on documents
 */
export default {
    /**
     * Display a listing of the resource.
     */
    async index({ commit, dispatch, getters }, documents) {
        if (!documents) {
            documents = await api.get(route("document.index"));
        }

        commit("setDocuments", documents);

        let selectedDocuments = getters.selectedDocuments;

        if (!selectedDocuments || selectedDocuments.length === 0) {
            selectedDocuments = documents;
        }

        await dispatch("feedItems/index", selectedDocuments, {
            root: true
        });
    },

    /**
     * Store a newly created resource in storage.
     */
    async store({ dispatch }, { url, folder_id }) {
        const data = await api.post(route("document.store"), {
            url: url,
            folder_id: folder_id
        });

        dispatch("index", data);
    },

    /**
     * Mark specified documents as selected.
     */
    async selectDocuments({ commit, dispatch }, documents) {
        commit("setSelectedDocuments", documents);
        await dispatch("feedItems/index", documents, { root: true });
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
    selectFirstDocumentWithUnreadItems({ getters, dispatch, rootGetters }, exclude) {
        if(!exclude) {
            exclude = collect(getters.selectedDocuments).pluck('id').all();
        }

        const document = collect(getters.documents).where('feed_item_states_count', '>', 0).whereNotIn('id', exclude).first();

        if(document) {
            dispatch("selectDocuments", [document]);
        } else {
            if(rootGetters["folders/selectedFolder"].type === 'unread_items') {
                dispatch("selectDocuments", []);
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
        const documents = getters.draggedDocuments;

        if (!documents || documents.length === 0) {
            return;
        }

        const data = await api.post(
                route("document.move", {
                    sourceFolder: sourceFolder.id,
                    targetFolder: targetFolder.id
                }),
                {
                    documents: collect(documents)
                        .pluck("id")
                        .all()
                }
            );

        commit("setDraggedDocuments", []);
        commit("setSelectedDocuments", []);
        dispatch("feedItems/index", getters.feeds, { root: true });
    },

    /**
     * Increment visits for specified document
     */
    async incrementVisits({ commit }, { document, folder }) {
        const data = await api.post(
            route("document.visit", { document: document.id, folder: folder.id })
        );

        commit("update", {
            document: document,
            newProperties: data
        });
    },

    /**
     * Open specified document in background
     */
    openDocument({ dispatch }, { document, folder }) {
        window.open(document.bookmark.initial_url);

        dispatch("incrementVisits", { document: document, folder: folder });
    },

    /**
     * Remove specified documents from specified folder
     */
    async destroy({ commit, getters, dispatch }, { folder, documents }) {
        commit("setSelectedDocuments", []);

        const data = await api.post(
            route("document.destroy_bookmarks", { folder: folder.id }),
            {
                documents: collect(documents)
                    .pluck("id")
                    .all()
            }
        );

        dispatch("folders/index", null, { root: true });
        dispatch("index", data);
        dispatch("feedItems/index", getters.feeds, { root: true });
    },

    /**
     * Update the specified resource in storage.
     */
    update({ commit, getters }, { documentId, newProperties }) {
        const document = getters.documents.find(d => d.id === documentId);

        if (!document) {
            return;
        }

        commit("update", { document: document, newProperties: newProperties });
    },

    followFeed({commit}, feed) {
        api.post(route("feed.follow", feed));
        commit("ignoreFeed", {feed: feed, ignored: false});
    },

    ignoreFeed({commit}, feed) {
        api.post(route("feed.ignore", feed));
        commit("ignoreFeed", {feed: feed, ignored: true});
    }
};

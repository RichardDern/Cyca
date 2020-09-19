/**
 * Actions on documents
 */
export default {
    /**
     * Display a listing of the resource.
     */
    async index({ commit, dispatch, getters }, documents) {
        if (!documents) {
            const response = await axios.get(route("document.index"));
            documents = response.data;
        }

        commit("setDocuments", documents);

        let selectedDocuments = getters.selectedDocuments;

        if(!selectedDocuments || selectedDocuments.length === 0) {
            selectedDocuments = documents;
        }

        dispatch("feedItems/index", selectedDocuments, { root: true });
    },

    /**
     * Store a newly created resource in storage.
     */
    async store({ dispatch }, { url, folder_id }) {
        const response = await axios.post(route("document.store"), {
            url: url,
            folder_id: folder_id
        });

        dispatch("index", response.data);
    },

    /**
     * Mark specified documents as selected.
     */
    selectDocuments({ commit, dispatch }, documents) {
        commit("setSelectedDocuments", documents);
        dispatch("feedItems/index", documents, { root: true });
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

        await axios
            .post(
                route("document.move", {
                    sourceFolder: sourceFolder,
                    targetFolder: targetFolder
                }),
                {
                    documents: collect(documents)
                        .pluck("id")
                        .all()
                }
            )
            .then(function(response) {
                commit("setDraggedDocuments", []);
                commit("setSelectedDocuments", []);
                dispatch("feedItems/index", getters.feeds, { root: true });
            });
    },

    /**
     * Increment visits for specified document
     */
    async incrementVisits({ commit }, { document, folder }) {
        const response = await axios.post(
            route("document.visit", { document: document, folder: folder })
        );

        commit("update", {
            document: document,
            newProperties: response.data
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

        const response = await axios.post(route("document.destroy_bookmarks", folder), {
            documents: collect(documents)
                .pluck("id")
                .all()
        });

        dispatch("folders/index", null, { root: true });
        dispatch("index", response.data);
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
    }
};

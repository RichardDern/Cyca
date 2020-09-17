/**
 * Actions on documents
 */
export default {
    /**
     * Display a listing of the resource.
     */
    async index({ commit }) {
        const response = await axios.get(route("document.index"));

        commit("setDocuments", response.data);
    },

    /**
     * Store a newly created resource in storage.
     */
    async store({}, { url, folder_id }) {
        await axios.post(route("document.store"), { url: url, folder_id: folder_id });
    },

    /**
     * Mark specified documents as selected.
     */
    selectDocuments({ commit }, documents) {
        commit("setSelectedDocuments", documents);
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
    dropIntoFolder(
        { getters, commit, dispatch },
        { sourceFolder, targetFolder }
    ) {
        const documents = getters.draggedDocuments;

        if (!documents || documents.length === 0) {
            return;
        }

        axios
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
    async destroy({commit}, {folder, documents}) {
        await axios
            .post(route("document.destroy_bookmarks", folder), {
                documents: collect(documents)
                    .pluck("id")
                    .all()
            });

        commit("setSelectedDocuments", []);
    }
};

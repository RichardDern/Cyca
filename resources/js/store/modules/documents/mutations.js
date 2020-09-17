/**
 * Documents mutations
 */
export default {
    /**
     * Store documents list
     * @param {*} state
     * @param {*} documents
     */
    setDocuments(state, documents) {
        state.documents = documents;
    },

    /**
     * Mark specified documents as selected
     * @param {*} state
     * @param {*} documents
     */
    setSelectedDocuments(state, documents) {
        state.selectedDocuments = documents;
    },

    /**
     * Remember documents being dragged
     * @param {*} state
     * @param {*} documents
     */
    setDraggedDocuments(state, documents) {
        state.draggedDocuments = documents;
    },

    /**
     * Update document's properties
     * @param {*} state
     * @param {*} param1
     */
    update(state, {document, newProperties}) {
        for(var property in newProperties) {
            document[property] = newProperties[property];
        }
    },
}
